<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
use Bigcommerce\Api\Client as Bigcommerce;
class Category extends CI_Controller {

	function __construct()
	{	
	   	parent::__construct();	
	   	$this->load->model("admin/categorymodel");
	   
	  	error_reporting(E_ALL);
	  	ini_set("display_errors", 1);
	   	ini_set('memory_limit', '-1');

		$this->load->library('bigcommerceapi');

		include(APPPATH.'third_party/bcapi/vendor/autoload.php');
	}

	public function index() {		
		$this->data['error'] = '';
		
		$this->data["page_head"]  = 'Volusion To BigCommerce Category Import';
		$this->data["page_title"] = 'Volusion To BigCommerce Category Import';
		
		$this->data["category_data"]  = $this->categorymodel->getCategory();
		$this->data["total_category"] = count($this->categorymodel->getCategory());
		
		$this->load->view("admin/common/header",$this->data);
		$this->data['left_nav']= $this->load->view('admin/common/leftmenu',$this->data,true);
		$this->load->view("admin/category/list", $this->data);
		$this->load->view("admin/common/footer");
	}
	
	function getCategoryFromVolusion() { 

		$csvFile = APPPATH."uploads/export/Categories_SPGUPHSYNM.csv";

		$file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 1024000000,",");
        }
	
		$name  = str_replace(' ','_',$line_of_text[0]);
		$name  = str_replace('-','_',$name);
		$name  = str_replace('/','_',$name);

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
            	
		// echo '<pre>';
		// print_r($nbew_line_of_text);
		// exit; 	

        $category_inenst = array();

        foreach ($nbew_line_of_text as $value) {
						
			$category_inenst['categoryid'] 		= $value['categoryid'];
			$category_inenst['parentid'] 			= $value['parentid'];
			
			$category_inenst['categoryname'] = '';
			if(isset($value['categoryname']) && !empty($value['categoryname'])) {
				$category_inenst['categoryname'] 	= $value['categoryname'];
			}
			
			$category_inenst['categoryorder'] 		= $value['categoryorder'];
			$category_inenst['categoryorder'] 		= $value['categoryorder'];
					
			$category_inenst['categorydescription'] = '';
			if(isset($value['categorydescription']) && !empty($value['categorydescription'])) {
				$category_inenst['categorydescription']	= $value['categorydescription'];
			}

			$category_inenst['metatag_title'] = '';
			if(isset($value['metatag_title']) && !empty($value['metatag_title'])) {
				$category_inenst['metatag_title']	= $value['metatag_title'];
			}
			
			$category_inenst['metatag_description'] = '';
			if(isset($value['metatag_description']) && !empty($value['metatag_description'])) {
				$category_inenst['metatag_description']	= $value['metatag_description'];
			}
			
			$category_inenst['metatag_keywords'] = '';
			if(isset($value['metatag_keywords']) && !empty($value['metatag_keywords'])) {
				$category_inenst['metatag_keywords']	= $value['metatag_keywords'];
			}

			$category_inenst['link_title_tag'] = '';
			if(isset($value['link_title_tag']) && !empty($value['link_title_tag'])) {
				$category_inenst['link_title_tag']	= $value['link_title_tag'];
			}

			$category_inenst['alternateurl'] 		= '';
			if(isset($value['alternateurl']) && !empty($value['alternateurl']))	{
				$category_inenst['alternateurl'] 	= $value['alternateurl'];
			}

			$category_inenst['hidden'] = '';
			if(isset($value['hidden']) && !empty($value['hidden'])) {
				$category_inenst['hidden']	= $value['hidden'];
			}

			$category_inenst['bc_category_id'] 	= '';
			$category_inenst['message'] 		= '';			
			$category_inenst['status'] 			= 'no';
			$category_inenst['bc_url'] 			= '';
		
			$category[] = $category_inenst;	 			
		}
		
		if (isset($category) && !empty($category))  {	

			$record = 500;
			$data = array_chunk($category,$record,true);
		
			foreach ($data as $category_data)  {

				$query = $this->db->insert_batch('categories', $category_data);
				// exit;
			} 
			echo 'done';
			return 1;
        }
	} 

	// Category import Volusion to BC
	function ImportCategory() {

		$category_id 	= $this->input->get('code');

		$setting		= $this->categorymodel->getGeneralSetting();
				
		$bcstoreurl  	= $setting['storeurl'];
		$client_id		= $setting['client_id'];
		$auth_token   	= $setting['apitoken'];
		$store_hash    	= $setting['storehash'];
			
		$volusion_cat_details = $this->categorymodel->getvolusionCategoryDetails($category_id);		
			
		if(isset($volusion_cat_details) && !empty($volusion_cat_details)) {

			$category_array			 = array();
			
			$categoryname = str_replace('<br>','',$volusion_cat_details['categoryname']);
			$categoryname = str_replace('<br/>','',$categoryname);
			
			$category_array['name'] 		=  '';
			if(isset($categoryname) && !empty($categoryname)) {
				$category_array['name']		= substr($categoryname,0,50);
			}
			
			$category_array['description']	=  '';
			if(isset($volusion_cat_details['categorydescription']) && !empty($volusion_cat_details['categorydescription'])) {
				$description_p = str_replace('src="https://www.davincipaints.com/','src="/content/',$volusion_cat_details['categorydescription']);
				$description_p = str_replace('src="v/','src="/content/v/',$description_p);
				$description_p = str_replace('src="/v/','src="/content/v/',$description_p);
				$category_array['description'] 	= $description_p;
			}
			
			$category_array['meta_keywords']	= array();
			if(isset($volusion_cat_details['metatag_keywords']) && !empty($volusion_cat_details['metatag_keywords'])) {
				$meta_keywords = array();
				$meta_keywords[] = $volusion_cat_details['metatag_keywords'];
				$category_array['meta_keywords']	= $meta_keywords;
			}
			
			$category_array['meta_description']	= '';
			if(isset($volusion_cat_details['metatag_description']) && !empty($volusion_cat_details['metatag_description'])) {
				$category_array['meta_description']	= $volusion_cat_details['metatag_description'];
			}
			
			$category_array['page_title']	= '';
			if(isset($volusion_cat_details['metatag_title']) && !empty($volusion_cat_details['metatag_title'])) {
				$category_array['page_title']	= $volusion_cat_details['metatag_title'];
			}
			
			$category_array['search_keywords']	= '';
			if(isset($volusion_cat_details['link_title_tag']) && !empty($volusion_cat_details['link_title_tag'])) {
				$category_array['search_keywords']	= $volusion_cat_details['link_title_tag'];
			}
			
			if(isset($volusion_cat_details['categoryorder']) && !empty($volusion_cat_details['categoryorder'])) {
				$category_array['sort_order']	= $volusion_cat_details['categoryorder'];
			}
			$category_array['is_visible'] = false;
			
			if(isset($volusion_cat_details['hidden']) && !empty($volusion_cat_details['hidden']) && $volusion_cat_details['hidden'] == 'N') {
				$category_array['is_visible'] = true;
			}

			$category_array['parent_id']	= 0;
			if(isset($volusion_cat_details['parentid']) && !empty($volusion_cat_details['parentid']) && $volusion_cat_details['parentid'] != 0) {
				$parent_id = $this->categorymodel->getParentId($volusion_cat_details['parentid']);
				$category_array['parent_id']	= $parent_id['bc_category_id'];
			}
			
			$encodedToken 		= base64_encode("".$client_id.":".$auth_token."");
			$authHeaderString 	= 'Authorization: Basic ' . $encodedToken;   	
			$post_data 		    = json_encode($category_array);
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/categories",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $post_data,
				CURLOPT_HTTPHEADER => array($authHeaderString,'Accept: application/json','Content-Type: application/json','X-Auth-Client: '.$client_id.'','X-Auth-Token: '.$auth_token.''),
		 	));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			echo "cURL Error #:" . $err;
			} else {
				$importcategory = json_decode($response);
				// echo '<pre>';
				// print_r($importcategory);
				// exit;
			
				if(isset($importcategory->data) && !empty($importcategory->data)) {
					
					$this->categorymodel->updateCategorystatus($category_id, $importcategory->data->id, $importcategory->data->custom_url->url);
				
					echo $importcategory->data->id.' - BC category import successfully...';
				} else {
					$error1 = @$importcategory->title;
					
					$error2 = $this->db->escape_str($error1);
					$this->categorymodel->updateCategoryMessage($category_id, $error2);

					echo $category_id.' - '. $error1;
				}
			}	
		} else {
			echo $category_id.' - Category Details Not Found...';
		}	
	}  
}
?>