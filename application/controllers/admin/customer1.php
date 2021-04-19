<?php 
use Bigcommerce\Api\Client as Bigcommerce;
class Customer1 extends CI_controller{
	
	function __construct()
	{
		parent::__construct();	
		$this->load->library('bigcommerceapi');
		$this->load->model("admin/customer1model");
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		include(APPPATH.'third_party/PHPExcel.php');
		include(APPPATH.'third_party/PHPExcel/Writer/Excel2007.php');
		include(APPPATH.'/third_party/bcapi/vendor/autoload.php');
	}

	function index() {	
		
		$this->data["page_head"]  = 'Volusion to BigCommerce customer Import';
		$this->data["page_title"] = 'Volusion to BigCommerce customer Import';
		
		$customer_data = $this->customer1model->getcustomer();
		$this->data['total_customer'] = count($customer_data);
		$this->data['customer_data']  = $customer_data;
		
		$this->load->view("admin/common/header",$this->data);
		$this->data['left_nav']=$this->load->view('admin/common/leftmenu',$this->data,true);	
		$this->load->view("admin/customer/list1",$this->data);
		$this->load->view("admin/common/footer");				
	}
	
	function getHttpCode($http_response_header) {
	    if(is_array($http_response_header))
	    {
	        $parts=explode(' ',$http_response_header);
	        echo "<pre>";
	        print_r($http_response_header);
	        exit;	

	        if(count($parts)>1) //HTTP/1.0 <code> <text>
	            return intval($parts[1]); //Get code
	    }
	    return 0;
	}

	function getAllStates() { 

		$config_data  = $this->customer1model->getGeneralSetting();
		
		$storeurl_volusion 	= $config_data['storeurl_volusion'];
		$loginemail 		= $config_data['login_email'];
		$encryptedpassword 	= $config_data['encryptedpassword'];

		$volusion_API_URL	= $storeurl_volusion.'net/WebService.aspx?Login='.$loginemail.'&EncryptedPassword='.$encryptedpassword;

		// Get customer information form volusion
		// $customer_data = @file_get_contents($storeurl_volusion."/net/WebService.aspx?Login=".$loginemail."&EncryptedPassword=".$encryptedpassword."&EDI_Name=Generic\state&SELECT_Columns=*");
							
		// $customer_data_e 	  = simplexml_load_string($customer_data);
		// $customer_data_decode = json_encode($customer_data_e);
		// $customer_data_res 	  = json_decode($customer_data_decode,TRUE);
		
		// // $customer_details 	 = array();
		// // $customer_details 	 = $customer_data_res['Products'];

		$customer_data 		= @file_get_contents($volusion_API_URL."&EDI_Name=Generic\country&SELECT_Columns=*");
		$customer_datas		= simplexml_load_string($customer_data);
		$json 				= json_encode($customer_datas);
		$customer_data_f 	= json_decode($json,TRUE);
		// $customer_details	= array();
		// $customer_details 	= $customer_data_f['Customers'];
		
		// echo '<pre>';
		// print_r($customer_details);
		// exit;

		echo '<pre>';
		print_r($customer_data_f);
		exit;
	}

	function getAllVoluCustomer() { 

		// $config_data  = $this->customer1model->getGeneralSetting();
		
		// $storeurl_volusion 	= $config_data['storeurl_volusion'];
		// $loginemail 		= $config_data['login_email'];
		// $encryptedpassword 	= $config_data['encryptedpassword'];

		// // Get customer information form volusion
		// $customer_data = @file_get_contents($storeurl_volusion."/net/WebService.aspx?Login=".$loginemail."&EncryptedPassword=".$encryptedpassword."&EDI_Name=Generic\Customers&SELECT_Columns=*");
							
		// $customer_data_e 	  = simplexml_load_string($customer_data);
		// $customer_data_decode = json_encode($customer_data_e);
		// $customer_data_res 	  = json_decode($customer_data_decode,TRUE);
		
		// // $customer_details 	 = array();
		// // $customer_details 	 = $customer_data_res['Products'];

		// echo '<pre>';
		// print_r($customer_data_res);
		// exit;


		$csvFile = APPPATH."uploads/export/26-03-2021/Customers_QMESMEPVKJ.csv";

		$file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 1024000);
        }
		
		$name  = str_replace(' ','_',$line_of_text[0]);
		
		$nbew_line_of_text = array();
		
		foreach ($line_of_text as $d) {
            $row_array = array();
			if (is_array($d)) {
				foreach ($d as $key => $d) {
					$row_array[trim(strtolower($name[$key]))] = trim($d);
				}
				$nbew_line_of_text[] = $row_array;
			}
        }			
					
        fclose($file_handle);
		
        unset($nbew_line_of_text[0]);
        unset($nbew_line_of_text[1]);
	
        $cust_inenst = array();

        foreach ($nbew_line_of_text as $value) {

        	$exit = $this->customer1model->exitcustomer($value['customerid']);
		
			if(isset($exit) && !empty($exit) && $exit == 'no') {
					        	
				$emailaddress = '';
				if(isset($value['emailaddress']) && !empty($value['emailaddress']));
				{
					$emailaddress = $value['emailaddress'];
				}

	        	$cust_inenst['customerid'] 	 	= $value['customerid'];
				$cust_inenst['emailaddress'] 	= $emailaddress;
				$cust_inenst['bc_customer_id'] 	= '';
				$cust_inenst['status'] 	 		= 'no';
				$cust_inenst['error_msg'] 		= '';
				$cust_inenst['add_error_msg'] 	= '';	     

	        	$cuesmer[] = $cust_inenst;
	        }
        }

		// echo '<pre>';
		// print_r($cuesmer);
		// exit;


		if (isset($cuesmer) && !empty($cuesmer)) 
		{	
			$record = 500;
			$data = array_chunk($cuesmer,$record,true);
			$orders_data_s = array();
			foreach ($data as $orders_data) 
			{	
				$query = $this->db->insert_batch('customers', $orders_data);
			} 
			
			return 1;
        }
    }
		 
	function updateCustomer() {

		// echo '<pre>';
		// print_r($_GET);
		// exit;

		$customer_id 	= $this->input->get('code');
		$bc_customer_id	= $this->input->get('email');
		
		$config_data  = $this->customer1model->getGeneralSetting();
		
		$storeurl_volusion 	= $config_data['storeurl_volusion'];
		$loginemail 		= $config_data['login_email'];
		$encryptedpassword 	= $config_data['encryptedpassword'];

		$volusion_API_URL	= $storeurl_volusion.'net/WebService.aspx?Login='.$loginemail.'&EncryptedPassword='.$encryptedpassword;
		
		$customer_data 		= @file_get_contents($volusion_API_URL."&EDI_Name=Generic\Customers&SELECT_Columns=*&WHERE_Column=CustomerID&WHERE_Value=".$customer_id."");
		$customer_datas		= simplexml_load_string($customer_data);
		$json 				= json_encode($customer_datas);
		$customer_data_f 	= json_decode($json,TRUE);
		$customer_details	= array();
		$customer_details 	= $customer_data_f['Customers'];
		
		// echo '<pre>';
		// print_r($customer_details);
		// exit;

		$client_id		= $config_data['client_id'];
		$access_token	= $config_data['apitoken'];
		$store_hash		= $config_data['storehash'];	

		Bigcommerce::configure(array('client_id' => $client_id, 'auth_token' => $access_token, 'store_hash' => $store_hash)); // Bc class connection
		Bigcommerce::verifyPeer(false); // SSL verify False 		
		Bigcommerce::failOnError(); 	// Display error exception on
								
		$customer_firstname = '';
		if(isset($customer_details['FirstName']) && !empty($customer_details['FirstName'])){
			$customer_firstname_s = trim($customer_details['FirstName']);
			$customer_firstname = mb_convert_encoding($customer_firstname_s, "Windows-1252", "auto");
		}
		$customer_lastname = '';
		if(isset($customer_details['LastName']) && !empty($customer_details['LastName'])){
			$customer_lastname_s = trim($customer_details['LastName']);
			$customer_lastname = mb_convert_encoding($customer_lastname_s, "Windows-1252", "auto");
		}
		$customer_companyname = '';
		if(isset($customer_details['CompanyName']) && !empty($customer_details['CompanyName'])){
			$customer_companyname = trim($customer_details['CompanyName']);
		}
		$customer_phonenumber = '';
		if(isset($customer_details['PhoneNumber']) && !empty($customer_details['PhoneNumber'])){
			$customer_phonenumber = trim($customer_details['PhoneNumber']);
		}
		$customer_note = '';
		$customer_note .= 'CustomerID: '.$customer_details['CustomerID']."\n";
		if(isset($customer_details['EmailSubscriber']) && !empty($customer_details['EmailSubscriber']))
		{
			$customer_note .= 'EmailSubscriber: '.$customer_details['EmailSubscriber']."\n";
		}
		if(isset($customer_details['Customer_Notes']) && !empty($customer_details['Customer_Notes'])){
			$customer_note .= trim($customer_details['Customer_Notes']);
		}
		$customer_address1 = '';
		if(isset($customer_details['BillingAddress1']) && !empty($customer_details['BillingAddress1'])){
			$customer_address1_s = $customer_details['BillingAddress1'];
			$customer_address1 = iconv("windows-1256", "utf-8//TRANSLIT//IGNORE", $customer_address1_s);
			// $customer_address1 = $mb_convert_encoding($customer_address1_s, "Windows-1252", "auto");
		}
		$customer_address2 = '';
		if(isset($customer_details['BillingAddress2']) && !empty($customer_details['BillingAddress2'])){
			$customer_address2_s = $customer_details['BillingAddress2'];
			$customer_address2 = iconv("windows-1256", "utf-8//TRANSLIT//IGNORE", $customer_address2_s);
			// $customer_address2 = $mb_convert_encoding($customer_address2_s, "Windows-1252", "auto");
		}
		$customer_city = '';
		if(isset($customer_details['City']) && !empty($customer_details['City'])){
			$customer_city = trim($customer_details['City']);
		}
		
		$customer_country = '';
		if(isset($customer_details['Country']) && !empty($customer_details['Country'])){
			$customer_country = trim($customer_details['Country']);
		}
		
		$customer_state = '';
		if(isset($customer_details['State']) && !empty($customer_details['State'])){
			$cust_state = $this->customer1model->GetState($customer_details['State'], $customer_country);
			$customer_state = trim($cust_state);
		}
				
		$customer_zipcode = '';
		if(isset($customer_details['PostalCode']) && !empty($customer_details['PostalCode'])){
			$customer_zipcode = trim($customer_details['PostalCode']);
		}
		
		$customer_data = array();
		$customer_data['first_name'] 	= $customer_firstname;
		$customer_data['last_name'] 	= $customer_lastname;
		
		// $customer_data['company'] 	= $customer_companyname;
		// $customer_data['email'] 		= $customer_email;
		// $customer_data['email'] 		= 'testing@1digitalagency.com';
		// $customer_data['phone'] 		= $customer_phonenumber;
		// $customer_data['notes'] 		= $customer_note;


		// echo '<pre>';
		// print_r($customer_data);
		// // print_r($customer_address);
		// exit;


		try	{
			$Customer = Bigcommerce::updateCustomer($bc_customer_id,$customer_data);
				if(isset($Customer) && empty($Customer)) {
				throw new Exception('Bigcommerce\Api\Error');
			} else {

				echo $bc_customer_id.' - Customer update succesfully...<br>';
				$message = 'Customer update succesfully...';

				$this->customer1model->updatecustomerstatus($customer_id,$Customer->id,$message);

				$curl = curl_init();

				curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.bigcommerce.com/stores/9n4xcihrak/v2/customers/".$bc_customer_id."/addresses",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "DELETE",
				CURLOPT_HTTPHEADER => array(
					"accept: application/json",
					"content-type: application/json",
					"x-auth-token: k284xhm2k9fxlgxzwbnom02n45sbxne"
				),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				echo "cURL Error #:" . $err;
				} else {
				echo $response;
				}
								
				$customer_address = array();
				if(isset($Customer->id) && !empty($Customer->id)){
					
					$customer_address['first_name'] = $customer_firstname;
					$customer_address['last_name']	= $customer_lastname;
					$customer_address['company']	= $customer_companyname;
					$customer_address['street_1'] 	= $customer_address1;
					$customer_address['street_2'] 	= $customer_address2;
					$customer_address['city']		= $customer_city;
					$customer_address['state']		= $customer_state;
					$customer_address['zip']		= $customer_zipcode;
					$customer_address['country']	= $customer_country;
					$customer_address['phone']		= $customer_phonenumber;
					
					try	{
						$Customeradd = Bigcommerce::createCustomeraddress($Customer->id,$customer_address);
								if(isset($Customeradd) && empty($Customeradd)) {
								throw new Exception('Bigcommerce\Api\Error');
							} else {
								$error2 = 'Customer update successfully with address...';
								echo $Customer->id.' - Customer update successfully with address...<br>';

								$this->customer1model->updateCustoAddMessage($customer_id, $error2);					       	 		
							}
						} catch(Exception $error) {
						$error1 = $error->getMessage();
						$error2 = 'Customer update - '.$this->db->escape_str($error1);

						echo $error2.'<br>';

						$this->customer1model->updateCustoAddMessage($customer_id, $error2);							
					}
				}
			}
		} catch(Exception $error) {
			$error1 = $error->getMessage();
			$error2 = $this->db->escape_str($error1);
			$this->customer1model->updatecustomerMessage($customer_id, $error2);
		
			echo $error1.'<br>';
		}			
	}	 
}
?>