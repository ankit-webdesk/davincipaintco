<?php 
use Bigcommerce\Api\Client as Bigcommerce;
class Customer extends CI_controller{
	
	function customer()
	{
		parent::__construct();	
		$this->load->library('bigcommerceapi');
		$this->load->model("admin/customermodel");
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		include(APPPATH.'third_party/PHPExcel.php');
		include(APPPATH.'third_party/PHPExcel/Writer/Excel2007.php');
		include(APPPATH.'/third_party/bcapi/vendor/autoload.php');
	}

	function index()
	{			
		$session_data = $this->session->userdata('admin_session');
		if(!isset($session_data) || empty($session_data))redirect('admin/login');
		
		$this->data["page_head"]  = 'Volusion to BigCommerce customer Import';
		$this->data["page_title"] = 'Volusion to BigCommerce customer Import';
		
		$customer_data = $this->customermodel->getcustomer();
		$this->data['total_customer'] = count($customer_data);
		$this->data['customer_data']  = $customer_data;
		
		$this->load->view("admin/common/header",$this->data);
		$this->data['left_nav']=$this->load->view('admin/common/leftmenu',$this->data,true);	
		$this->load->view("admin/list",$this->data);
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

	function getAllVoluCustomer()
	{ 
		$csvFile = APPPATH."uploads/export/Customers_USJXSKU2QQ.csv";

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

        	$exit = $this->customermodel->exitcustomer($value['customerid']);

			if(isset($exit) && !empty($exit) && $exit = 'no') {
					        	
				$emailaddress = '';
				if(isset($value['emailaddress']) && !empty($value['emailaddress']));
				{
					$emailaddress = $value['emailaddress'];
				}

	        	$cust_inenst['customerid'] 	 	= $value['customerid'];
				$cust_inenst['emailaddress'] 	= $emailaddress;
				$cust_inenst['bc_customerid'] 	= '';
				$cust_inenst['status'] 	 		= 'no';
				$cust_inenst['error_msg'] 		= '';
				$cust_inenst['add_error_msg'] 	= '';	     

	        	$cuesmer[] = $cust_inenst;
	        }
        }

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
		 
	function importbccustomer()
	{
		$customer_id = $this->input->get('code');
		$email	 	 = $this->input->get('email');
		
		$setting_volusion 	= $this->customermodel->getGeneralSetting();
	
		$storeurl_volusion	= $setting_volusion['storeurl_volusion'];
		$loginemail 		= $setting_volusion['loginemail'];
		$encryptedpassword 	= $setting_volusion['encryptedpassword'];
		$volusion_API_URL	= $storeurl_volusion.'net/WebService.aspx?Login='.$loginemail.'&EncryptedPassword='.$encryptedpassword;
		
		$customer_data 		= @file_get_contents($volusion_API_URL."&EDI_Name=Generic\Customers&SELECT_Columns=*&WHERE_Column=CustomerID&WHERE_Value=".$customer_id."");
		$customer_datas		= simplexml_load_string($customer_data);
		$json 				= json_encode($customer_datas);
		$customer_data_f 	= json_decode($json,TRUE);
		$customer_details	= array();
		$customer_details 	= $customer_data_f['Customers'];
		

		$client_id		= $setting_volusion['client_id'];
		$access_token	= $setting_volusion['apitoken'];
		$store_hash		= $setting_volusion['storehash'];	

		Bigcommerce::configure(array('client_id' => $client_id, 'auth_token' => $access_token, 'store_hash' => $store_hash)); // Bc class connection
		Bigcommerce::verifyPeer(false); // SSL verify False 		
		Bigcommerce::failOnError(); 	// Display error exception on
								
		$customer_email = '';
		if(isset($customer_details['EmailAddress']) && !empty($customer_details['EmailAddress'])){
			$customer_email = trim($customer_details['EmailAddress']);
		}
		$customer_firstname = '';
		if(isset($customer_details['FirstName']) && !empty($customer_details['FirstName'])){
			$customer_firstname = trim($customer_details['FirstName']);
		}
		$customer_lastname = '';
		if(isset($customer_details['LastName']) && !empty($customer_details['LastName'])){
			$customer_lastname = trim($customer_details['LastName']);
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
			$customer_address1 = trim($customer_details['BillingAddress1']);
		}
		$customer_address2 = '';
		if(isset($customer_details['BillingAddress2']) && !empty($customer_details['BillingAddress2'])){
			$customer_address2 = trim($customer_details['BillingAddress2']);
		}
		$customer_city = '';
		if(isset($customer_details['City']) && !empty($customer_details['City'])){
			$customer_city = trim($customer_details['City']);
		}
		$customer_state = '';
		if(isset($customer_details['State']) && !empty($customer_details['State'])){
			$customer_state = trim($customer_details['State']);
		}
		$customer_zipcode = '';
		if(isset($customer_details['PostalCode']) && !empty($customer_details['PostalCode'])){
			$customer_zipcode = trim($customer_details['PostalCode']);
		}
		$customer_country = '';
		if(isset($customer_details['Country']) && !empty($customer_details['Country'])){
			$customer_country = trim($customer_details['Country']);
		}			
		
		$customer_data = array();
		$customer_data['first_name'] 	= $customer_firstname;
		$customer_data['last_name'] 	= $customer_lastname;
		$customer_data['company'] 		= $customer_companyname;
		$customer_data['email'] 		= $customer_email;
		$customer_data['phone'] 		= $customer_phonenumber;
		$customer_data['notes'] 		= $customer_note;
		
		$getcustomer = Bigcommerce::getCustomers(array("email" => $email));
		
		if(isset($getcustomer) && !empty($getcustomer))
		{			
			if(isset($customer_data['first_name']) && empty($customer_data['first_name'])){
				$customer_data['first_name'] = trim($getcustomer[0]->first_name);
			}
			
			if(isset($customer_data['last_name']) && empty($customer_data['last_name'])){
				$customer_data['last_name'] = trim($getcustomer[0]->last_name);
			}
		
			if(isset($customer_data['phone']) && empty($customer_data['phone'])){
				$customer_data['phone'] = trim($getcustomer[0]->phone);
			}	
			
			try	{
				$Customer = Bigcommerce::updateCustomer($getcustomer[0]->id,$customer_data);
	        		if(isset($Customer) && empty($Customer)) {
	            	throw new Exception('Bigcommerce\Api\Error');
	       	 	} else {

					echo $Customer->id.' - Customer update succesfully...<br>';
					$message = 'Customer update succesfully...';
					$this->customermodel->updatecustomerstatus($customer_id,$Customer->id,$message);
					
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

					       	 		$this->customermodel->updateCustoAddMessage($customer_id, $error2);					       	 		
								}
				       	 	} catch(Exception $error) {
							$error1 = $error->getMessage();
							$error2 = 'Customer update - '.$this->db->escape_str($error1);

							echo $error2.'<br>';

							$this->customermodel->updateCustoAddMessage($customer_id, $error2);							
						}
					}
				}
			} catch(Exception $error) {
				$error1 = $error->getMessage();
				$error2 = $this->db->escape_str($error1);
				$this->customermodel->updatecustomerMessage($customer_id, $error2);
			
				echo $error1.'<br>';
			}

		} else {				
			try	{
				$Customer = Bigcommerce::createCustomer($customer_data);
	        		if(isset($Customer) && empty($Customer)) {
	            	throw new Exception('Bigcommerce\Api\Error');
	       	 	} else {

					echo $Customer->id.' - Customer import succesfully..<br>';
					$message = 'Customer import succesfully...';
					$this->customermodel->updatecustomerstatus($customer_id,$Customer->id,$message);
					
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
				       	 		$error2 = 'Customer import successfully with address...';
				       	 		echo $Customer->id.' - Customer import successfully with address...<br>';

				       	 		$this->customermodel->updateCustoAddMessage($customer_id, $error2);					       	 		
							}
			       	 	} catch(Exception $error) {
							$error1 = $error->getMessage();
							$error2 = 'Customer - '.$this->db->escape_str($error1);

							echo $error2.'<br>';

							$this->customermodel->updateCustoAddMessage($customer_id, $error2);							
						}
					}
				}
		
			} catch(Exception $error) {
				$error1 = $error->getMessage();
				$error2 = $this->db->escape_str($error1);
				$this->customermodel->updatecustomerMessage($customer_id, $error2);
			
				echo $error1.'<br>';
			}
		}
	}	 

	function usernotification()
	{
		$session_data = $this->session->userdata('admin_session');
		if(!isset($session_data) || empty($session_data))redirect('admin/login');
		
		$this->data["page_head"]  = 'Volusion to BigCommerce customer reset password';
		$this->data["page_title"] = 'Volusion to BigCommerce customer reset password';
		
		$customer_data = $this->customermodel->getcustomerresetpassword();
		$this->data['total_customer'] = count($customer_data);
		$this->data['customer_data']  = $customer_data;
		
		$this->load->view("admin/common/header",$this->data);
		$this->data['left_nav']=$this->load->view('admin/common/leftmenu',$this->data,true);	
		$this->load->view("admin/customer/customernotification",$this->data);
		$this->load->view("admin/common/footer");
	}
	
	function randomPassword() {
		 $password = '';
		 $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		 for($i = 0; $i < 8; $i++)
		 {
			 $random_int = mt_rand();
			 $password .= $charset[$random_int % strlen($charset)];
		 }
		 return $password;
	}
		
	function resetpasswrod()
	{
		$reset_password_error = APPPATH."third_party/customer/reset_password_error.xls";
		$spreadsheet_reset_password = PHPExcel_IOFactory::load($reset_password_error);
		$spreadsheet_reset_password->setActiveSheetIndex(0);
		$worksheet_reset_password = $spreadsheet_reset_password->getActiveSheet();
		
		$customer_id = $this->input->get('code');
		$column = $this->input->get('column');
		$config_data = $this->customermodel->getGeneralSetting();
		$store = '';
		if(isset($config_data[0]['apiusername']) && !empty($config_data[0]['apiusername']) && isset($config_data[0]['apipath']) && !empty($config_data[0]['apipath']) && isset($config_data[0]['apitoken']) && !empty($config_data[0]['apitoken'])){
			// BigCommerce API connection
			$store = new Bigcommerceapi($config_data[0]['apiusername'], $config_data[0]['apipath'] , $config_data[0]['apitoken']);
		}
		
		$password_g  = $this->randomPassword();
		
		$field_reset_password = array();
		$field_reset_password['_authentication']['password'] = $password_g;
		$reset_password = $store->put('/customers/'.$customer_id,$field_reset_password);
		
		
		if(isset($reset_password['email']) && !empty($reset_password['email']))
		{
			$this->customermodel->updatestatus($customer_id);
			
			$store_url = $config_data[0]['storeurl'];
			$username  = $reset_password['email'];
			$password  = $password_g;
			
			$subject_activation = 'Your Password Has Been Changed!';
						
			$html_plan = '<div style="background-color:#ffffff;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:15px;width:800px;margin:0px auto">
				<div style="border-bottom:1px #0086c7 solid;margin-bottom:15px">
					<h1 style="display:block;text-align:center;padding:30px 0px 10px">
						<img src="http://cdn3.bigcommerce.com/s-fxjd74hwbl/product_images/logo_1466588892__47438.png">
					</h1>    
				</div>
				
				<div style="width:100%;margin-bottom:15px">
					<h2 style="color:#444;font-weight:normal;font-size:15px"><span style="color:#000"><b>Your Password Has Been Changed!</b></h2>
				</div>
				<div style="width:100%;margin-bottom:15px">
					<h2 style="color:#444;font-weight:normal;font-size:15px"><span style="color:#000">This email confirm that your password has been changed.</h2>
				</div>
				<div style="width:100%;margin-bottom:15px">
					<h2 style="color:#444;font-weight:normal;font-size:15px"><span style="color:#000">To log on to the site, use the following credentials:</h2>
				</div>
				<div style="width:100%;margin-bottom:15px">
					<h2 style="color:#444;font-weight:normal;font-size:15px"><span style="color:#000"><b>Store URL:</b> '.$store_url.'</h2>
				</div>
				<div style="width:100%;margin-bottom:15px">
					<h2 style="color:#444;font-weight:normal;font-size:15px"><span style="color:#000"><b>Username:</b> '.$username.'</h2>
				</div>
				<div style="width:100%;margin-bottom:15px">
					<h2 style="color:#444;font-weight:normal;font-size:15px"><span style="color:#000"><b>Password:</b> '.$password.'</h2>
				</div>
				<div style="width:100%;margin-bottom:15px">
					<h2 style="color:#444;font-weight:normal;font-size:15px"><span style="color:#000">if you have any questions or encounter any problems logging in, please contact a site administrator <a href="mailto:sales@evrmemories.com">sales@evrmemories.com</a>.</h2>
				</div>
				<div style="width:100%;border-top:1px #0086c7 solid;">
					<div style="clear:both"></div>
					<div style="float:left;margin-right:10px;margin-top:15px">
						<div style="float:left;font-size:14px;color:#333;font-weight:normal;">
							Thanks,<br/> 
							<span style="color:#0086c7;letter-spacing:1">
								 '.$config_data[0]['storename'].'
							</span>
						</div>
					</div>
				</div>
			</div>';
			
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <sales@evrmemories.com>' . "\r\n";
			$to = $username;
			@mail($to,$subject_activation,$html_plan,$headers);		

			echo $username.' - Customer Password Has Been Changed!'	;
		}
		else
		{
			echo 'Customer Password Changed Error!!!';
			$commnet = 'Customer Password Changed Error!!!';
			
			$column = $column + 1;
			$worksheet_reset_password->setCellValueExplicit('A1','Customer ID', PHPExcel_Cell_DataType::TYPE_STRING);
			$worksheet_reset_password->setCellValueExplicit('B1','Comment', PHPExcel_Cell_DataType::TYPE_STRING);
			
			$worksheet_reset_password->setCellValueExplicit('A'.$column,$customer_id, PHPExcel_Cell_DataType::TYPE_STRING);
			$worksheet_reset_password->setCellValueExplicit('B'.$column,$commnet, PHPExcel_Cell_DataType::TYPE_STRING);
			
			$writer_reset_password = new PHPExcel_Writer_Excel2007($spreadsheet_reset_password);
			$writer_reset_password->save($reset_password_error);
			
		}	
			
	}
}
?>