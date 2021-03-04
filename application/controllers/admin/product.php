<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//we need to call PHP's session object to access it through CI
use Bigcommerce\Api\Client as Bigcommerce;
class Product extends CI_Controller 
{
	function __construct()
	{	
		parent::__construct();
		
		ini_set('memory_limit', '-1');
		ini_set('display_errors','on');
		error_reporting(E_ALL);		

		set_time_limit(0);

		$this->load->model("admin/productmodel");
		$this->load->library('bigcommerceapi');
	
		include(APPPATH.'third_party/bcapi/vendor/autoload.php');
	}

	public function index()
    {	
   		$this->data['error'] = '';
		$this->data["page_head"]  = 'Bigcommerce Product';
		$this->data["page_title"] = 'Bigcommerce Product';
				
		$product_data  = $this->productmodel->getproductdata();

		$this->data['total_product']	= count($product_data);
		$this->data["product_data"]  	= $product_data;
				
		$this->load->view("admin/common/header",$this->data);
		$this->data['left_nav']= $this->load->view('admin/common/leftmenu',$this->data,true);
		$this->load->view("admin/product/list", $this->data);
		$this->load->view("admin/common/footer");
	}

	public function getProductVideo() { 

		$product_code = $code = $this->input->get('code');

		$config_data  = $this->productmodel->getGeneralSetting();
				
		$storeurl_volusion 	= $config_data['storeurl_volusion'];
		$loginemail 		= $config_data['login_email'];
		$encryptedpassword 	= $config_data['encryptedpassword'];

		// Get product information form volusion
		$product_data        = @file_get_contents($storeurl_volusion."/net/WebService.aspx?Login=".$loginemail."&EncryptedPassword=".$encryptedpassword."&EDI_Name=Generic/youtubevideo");
							
		$product_data_e 	 = simplexml_load_string($product_data);
		$product_data_decode = json_encode($product_data_e);
		$product_data_res 	 = json_decode($product_data_decode,TRUE);
		$product_details 	 = array();

		$product_details 	 = $product_data_res['Table'];

		$videos = array();
		if(isset($product_details) && !empty($product_details)){
			
			foreach ($product_details as $value) {
				
				$video_inenst['productId'] 			= $value['ProductId'];
				$video_inenst['youTubeId'] 			= $value['YouTubeId'];

				$video_inenst['description'] 		= '';
				if(isset($value['Description']) && !empty($value['Description'])) {
					$video_inenst['description'] 	= $value['Description'];
				}
							
				$video_inenst['sort'] 		 = $value['Sort'];
				$video_inenst['ProductCode'] = $value['ProductCode'];

				$videos[] = $video_inenst;
			}
		}
		
		// echo '<pre>';
		// print_r($videos);
		// exit;
		if (isset($videos) && !empty($videos))  {	
			$query = $this->db->insert_batch('youtube_video', $videos);
        }

		echo '<pre>';
		print_r($product_details);
		exit;
	}

	public function ImportProduct() { 

		$product_code = $code = $this->input->get('code');

		$config_data  = $this->productmodel->getGeneralSetting();
			
		$storeurl_volusion 	= $config_data['storeurl_volusion'];
		$loginemail 		= $config_data['login_email'];
		$encryptedpassword 	= $config_data['encryptedpassword'];

		$bcstoreurl  	= $config_data['storeurl'];
		$client_id		= $config_data['client_id'];
		$auth_token   	= $config_data['apitoken'];
		$store_hash    	= $config_data['storehash'];
		
		// Bc class connection
		Bigcommerce::configure(array('client_id' => $client_id,'auth_token' => $auth_token,'store_hash' => $store_hash));	
		// SSL verify False
		Bigcommerce::verifyPeer(false);
 		// Display error exception on
		Bigcommerce::failOnError();			

		// Get product information form volusion
		$product_data        = @file_get_contents($storeurl_volusion."/net/WebService.aspx?Login=".$loginemail."&EncryptedPassword=".$encryptedpassword."&EDI_Name=Generic\Products&SELECT_Columns=*&WHERE_Column=ProductCode&WHERE_Value=".str_replace(' ','%20',$product_code)."");
							
		$product_data_e 	 = simplexml_load_string($product_data);
		$product_data_decode = json_encode($product_data_e);
		$product_data_res 	 = json_decode($product_data_decode,TRUE);
		$product_details 	 = array();

		$product_details 	 = $product_data_res['Products'];

		// echo '<pre>';
		// print_r($product_data_res);
		// exit;

		$pro_video    = $this->productmodel->getProductVideos($product_details['ProductID']);

		// echo '<pre>';
		// print_r($pro_video);
		// exit;

		$product_array = array();

		//Product Name
		$product_array['name'] = '';
		if(isset($product_details['ProductName']) && !empty($product_details['ProductName'])){
			$product_array['name'] = $product_details['ProductName'];
		}

		//Product SKU
		$product_array['sku'] = '';
		if(isset($product_details['ProductCode']) && !empty($product_details['ProductCode'])){
			$product_array['sku'] = $product_details['ProductCode'];
		}

		$product_array['type'] = 'physical';

		//Product Price
		$product_array['price'] = 0.00;
		
		if(isset($product_details['ListPrice']) && !empty($product_details['ListPrice'])){
			$product_array['price'] = number_format($product_details['ListPrice'],2);
			$product_array['sale_price'] = number_format($product_details['ProductPrice'],2);
			$product_array['retail_price'] = number_format($product_details['ListPrice'],2);
		 
		} else {
			if(isset($product_details['ProductPrice']) && !empty($product_details['ProductPrice'])){
				$product_array['price'] = number_format($product_details['ProductPrice'],2);
			}
		}			
	
		//Product ProductWeight
		$product_array['weight'] = '0';
		if(isset($product_details['ProductWeight']) && !empty($product_details['ProductWeight'])){
			$product_array['weight'] = $product_details['ProductWeight'];
		}
		
		//Product Category
		$ProductCategory = $this->productmodel->getProductCategory($product_code);
				
		if(isset($ProductCategory) && !empty($ProductCategory)) {
			$product_array['categories'] = $ProductCategory;
		} else {
			$product_array['categories'] = array('23'); // shop category
		}
		
		$product_array['availability'] = 'available';

		//Product Availability
		$product_array['availability_description'] = '';
		if(isset($product_details['Availability']) && !empty($product_details['Availability'])){
			$product_array['availability_description'] = $product_details['Availability'];
		}

		// Product status
		$product_array['is_visible'] = true;
		if(isset($product_details['HideProduct']) && !empty($product_details['HideProduct']) && $product_details['HideProduct'] == 'Y'){
			$product_array['is_visible'] = false;
		}
		
		// Product Description 
		$ProductDescription = '';
		if(isset($product_details['ProductDescription']) && !empty($product_details['ProductDescription'])){
			// Image Path replace 
			$description_p = str_replace('src="https://www.davincipaints.com/','src="/content/',$product_details['ProductDescription']);
			$description_p = str_replace('src="v/','src="/content/v/',$description_p);
			$description_p = str_replace('src="/v/','src="/content/v/',$description_p);
			$description_p = str_replace('href="https://www.davincipaints.com','href="/',$description_p);
			$description_p = str_replace('â€™',"'",$description_p);
			$ProductDescription = '<div class="product_description">'.$description_p.'</div>';
		}
		// Product Features
		if(isset($product_details['ProductFeatures']) && !empty($product_details['ProductFeatures'])){
			// Image Path replace 
			$description_p_ProductFeatures = str_replace('src="https://www.davincipaints.com/','src="/content/',$product_details['ProductFeatures']);
			$description_p_ProductFeatures = str_replace('src="v/','src="/content/v/',$description_p_ProductFeatures);
			$description_p_ProductFeatures = str_replace('src="/v/','src="/content/v/',$description_p_ProductFeatures);
			$description_p_ProductFeatures = str_replace('href="https://www.davincipaints.com','href="/',$description_p_ProductFeatures);
			$description_p_ProductFeatures = str_replace('â€™',"'",$description_p_ProductFeatures);
			$ProductDescription .= '<div class="product_features">'.$description_p_ProductFeatures.'</div>';
		}
			
		// Product ProductDescription_AbovePricing
		if(isset($product_details['ProductDescription_AbovePricing']) && !empty($product_details['ProductDescription_AbovePricing'])){
			// Image Path replace 
			$description_p_ProductDescription_AbovePricing = str_replace('src="https://www.davincipaints.com/','src="/content/',$product_details['ProductDescription_AbovePricing']);
			$description_p_ProductDescription_AbovePricing = str_replace('src="v/','src="/content/v/',$description_p_ProductDescription_AbovePricing);
			$description_p_ProductDescription_AbovePricing = str_replace('src="/v/','src="/content/v/',$description_p_ProductDescription_AbovePricing);
			$description_p_ProductDescription_AbovePricing = str_replace('href="https://www.davincipaints.com','href="/',$description_p_ProductDescription_AbovePricing);
			$description_p_ProductDescription_AbovePricing = str_replace('â€™',"'",$description_p_ProductDescription_AbovePricing);
			$ProductDescription .= '<div class="ProductDescription_AbovePricing">'.$description_p_ProductDescription_AbovePricing.'</div>';
		}

		$product_array['description'] = '';
		$product_array['description'] = $ProductDescription;

		// Product TechSpecs
		$product_warranty = '';
		if(isset($product_details['TechSpecs']) && !empty($product_details['TechSpecs'])){
			// Image Path replace 
			$TechSpecs_d = str_replace('src="https://www.davincipaints.com/','src="/content/',$product_details['TechSpecs']);
			$TechSpecs_d = str_replace('src="v/','src="/content/v/',$TechSpecs_d);
			$TechSpecs_d = str_replace('src="/v/','src="/content/v/',$TechSpecs_d);
			$TechSpecs_d = str_replace('href="https://www.davincipaints.com','href="/',$TechSpecs_d);
			$TechSpecs_d = str_replace('â€™',"'",$TechSpecs_d);
			$product_warranty .= '<div class="TechSpecs">'.$TechSpecs_d.'</div>';
		}

		// Product ExtInfo
		if(isset($product_details['ExtInfo']) && !empty($product_details['ExtInfo'])){
			// Image Path replace 
			$description_p_ExtInfo = str_replace('src="https://www.davincipaints.com/','src="/content/',$product_details['ExtInfo']);
			$description_p_ExtInfo = str_replace('src="v/','src="/content/v/',$description_p_ExtInfo);
			$description_p_ExtInfo = str_replace('src="/v/','src="/content/v/',$description_p_ExtInfo);
			$description_p_ExtInfo = str_replace('href="https://www.davincipaints.com','href="/',$description_p_ExtInfo);
			$description_p_ExtInfo = str_replace('â€™',"'",$description_p_ExtInfo);
			$product_warranty .= '<div class="ExtInfo">'.$description_p_ExtInfo.'</div>';
		}

		$product_array['warranty'] = '';
		$product_array['warranty'] = $product_warranty;

		// Free Shipping
		$product_array['is_free_shipping'] = true;
		if(isset($product_details['FreeShippingItem']) && !empty($product_details['FreeShippingItem']) && $product_details['FreeShippingItem'] == 'N'){
			$product_array['is_free_shipping'] =  false;
		}
		// Fixed_ShippingCost
		$product_array['fixed_cost_shipping_price'] = 0.0;
		if(isset($product_details['Fixed_ShippingCost']) && !empty($product_details['Fixed_ShippingCost'])){
			$product_array['fixed_cost_shipping_price'] = $product_details['Fixed_ShippingCost'];
		}
			
		// DoNotAllowBackOrders
		$product_array['inventory_tracking'] = "none";
		if(isset($product_details['DoNotAllowBackOrders']) && !empty($product_details['DoNotAllowBackOrders']) && $product_details['DoNotAllowBackOrders'] == 'N') {
			$product_array['inventory_tracking'] = "none";
		}
		
		$product_array['inventory_level'] = 0;
		if(isset($product_details['StockStatus']) && !empty($product_details['StockStatus']) && $product_details['StockStatus'] > 0){
			$product_array['inventory_level'] = $product_details['StockStatus'];
		}
				
		// METATAG_Description
		$product_array['meta_description'] = '';
		if(isset($product_details['METATAG_Description']) && !empty($product_details['METATAG_Description'])){
			$product_array['meta_description'] = $product_details['METATAG_Description'];
		}
		// METATAG_Title
		$product_array['meta_keywords'] = array();
		if(isset($product_details['METATAG_Title']) && !empty($product_details['METATAG_Title'])){
			$product_array['meta_keywords'] = $product_details['METATAG_Title'];
		}
		// METATAG_Keywords
		$METATAG_Keywords = '';
		if(isset($product_details['METATAG_Keywords']) && !empty($product_details['METATAG_Keywords'])){
			$METATAG_Keywords =  $product_details['METATAG_Keywords'];
		}
		// ProductKeywords
		$product_array['search_keywords'] = '';
		if(isset($product_details['ProductKeywords']) && !empty($product_details['ProductKeywords'])){
			$product_array['search_keywords'] = $product_details['ProductKeywords'];
		}
		// UPC_code
		$product_array['upc'] = '';
		if(isset($product_details['UPC_code']) && !empty($product_details['UPC_code'])){
			$product_array['upc'] = $product_details['UPC_code'];
		}
		
		$product_detailsp['brand_id'] = 0;
		$product_array['sort_order']  = 0;
		if(isset($product_details['ProductManufacturer']) && !empty($product_details['ProductManufacturer'])) {			
			$manufacture = $product_details['ProductManufacturer'];
		  	$manufacture_sort = preg_replace('/\D/', '', $manufacture);
		  	$manufacture = str_replace($manufacture_sort, '', $product_details['ProductManufacturer']);
		  	$manufacture = str_replace('- ', '', $manufacture);
			
			$getBrand = Bigcommerce::getBrands(array('name' => trim($manufacture)));

			if(isset($getBrand) && !empty($getBrand))  {
				$product_detailsp['brand_id'] = $getBrand[0]->id;
			} else {

				$data = array();
				$data['name'] = trim($manufacture);
				$CreateBrand = Bigcommerce::createBrand($data);
				$product_detailsp['brand_id'] = $CreateBrand->id;
			}

			if(isset($manufacture_sort) && !empty($manufacture_sort)) {
				$product_array['sort_order'] = $manufacture_sort;
			} else {

				if(isset($product_details['ProductPopularity']) && !empty($product_details['ProductPopularity'])) {
					$product_array['sort_order'] = $product_details['ProductPopularity'];
				}
			}

		} else {
			if(isset($product_details['ProductPopularity']) && !empty($product_details['ProductPopularity'])) {
				$product_array['sort_order'] = $product_details['ProductPopularity'];
			}
		}
			
		// Customfields	
		$i = 0;
		// Product Id
		if (isset($product_details['ProductID']) && !empty($product_details['ProductID'])) {
			$product_array['custom_fields'][$i]['name']   = 'Product Id';
			$product_array['custom_fields'][$i]['value']  = $product_details['ProductID'];
		}
		
		// Yahoo Category
		if(isset($product_details['Yahoo_Category']) && !empty($product_details['Yahoo_Category'])) {
			$i = $i + 1;
			$product_array['custom_fields'][$i]['name']  = 'Yahoo! Shopping Category';
			$product_array['custom_fields'][$i]['value'] = $product_details['Yahoo_Category'];
		}

		// Yahoo! Shopping Medium
		if(isset($product_details['Yahoo_Medium']) && !empty($product_details['Yahoo_Medium'])) {
			$i = $i + 1;
			$product_array['custom_fields'][$i]['name']  = 'Yahoo! Shopping Medium';
			$product_array['custom_fields'][$i]['value'] = $product_details['Yahoo_Medium'];
		}

		// Create Customfields
		if(isset($product_details['CustomField1']) && !empty($product_details['CustomField1'])) {
			$i = $i + 1;
			$product_array['custom_fields'][$i]['name']  = 'Custom Field 1';
			$product_array['custom_fields'][$i]['value'] = $product_details['CustomField1'];
		}

		if(isset($product_details['CustomField2']) && !empty($product_details['CustomField2'])) {
			$i = $i + 1;
			$product_array['custom_fields'][$i]['name']  = 'Custom Field 2';
			$product_array['custom_fields'][$i]['value'] = $product_details['CustomField2'];
		}

		if(isset($product_details['CustomField3']) && !empty($product_details['CustomField3'])) {
			$i = $i + 1;
			$product_array['custom_fields'][$i]['name']  = 'Custom Field 3';
			$product_array['custom_fields'][$i]['value'] = $product_details['CustomField3'];
		}

		if(isset($product_details['CustomField4']) && !empty($product_details['CustomField4'])) {
			$i = $i + 1;
			$product_array['custom_fields'][$i]['name']  = 'Custom Field 4';
			$product_array['custom_fields'][$i]['value'] = $product_details['CustomField4'];
		}

		if(isset($product_details['CustomField5']) && !empty($product_details['CustomField5'])) {
			$i = $i + 1;
			$product_array['custom_fields'][$i]['name']  = 'Custom Field 5';
			$product_array['custom_fields'][$i]['value'] = $product_details['CustomField5'];
		}
		 
		// echo '<pre>';
		// print_r($product_array);
		// exit;

		$ProductCodenew = str_replace('/', '-fslash-', $product_code);
		$ProductCodenew = str_replace(' ', '%20', $ProductCodenew);

		$live_image_URL = 'https://www.davincipaints.com/v/vspfiles/photos/';
		$check_img = $live_image_URL.$ProductCodenew.'-2.jpg';
		if ($check_img) {
			$imageurls = array();
			for ($i = 2; $i <= 15; ++$i) {
				$check_image = '';

				$check_image = $live_image_URL.$ProductCodenew.'-'.$i.'.jpg';
				if (@getimagesize($check_image)) {
					$image_title = '';
					//$image_title = str_replace(' ','%20',$ProductCode).'-'.$i.'.jpg';
					$image_title = $ProductCodenew.'-'.$i.'.jpg';
					$imageurls[] = $live_image_URL.$image_title;
				}
			}
		} else {
		
			$check_img2 = $live_image_URL.$ProductCodenew.'-2.png';
			if ($check_img2) {
				$imageurls = array();
				for ($i = 2; $i <= 15; ++$i) {
					$check_image = '';
					$ProductCodenew = str_replace('/', '-fslash-', $product_code);
					$ProductCodenew = str_replace(' ', '%20', $ProductCodenew);
					$check_image = $live_image_URL.$ProductCodenew.'-'.$i.'.png';
					if (@getimagesize($check_image)) {
						$image_title = '';
						//$image_title = str_replace(' ','%20',$ProductCode).'-'.$i.'.jpg';
						$image_title = $ProductCodenew.'-'.$i.'.png';
						$imageurls[] = $live_image_URL.$image_title;
					}
				}
			}
		}	

		// $client_id		= $config_data['client_id'];
		// $auth_token   	= $config_data['apitoken'];
		// $store_hash    	= $config_data['storehash'];

		$encodedToken = base64_encode("".$config_data['client_id'].":".$config_data['apitoken']."");
		$authHeaderString = 'Authorization: Basic ' . $encodedToken;
		$products_data = json_encode($product_array);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $products_data,
		  CURLOPT_HTTPHEADER => array($authHeaderString,'Accept: application/json',
			'Content-Type: application/json',
			'X-Auth-Client: '.$config_data['client_id'].'',
			'X-Auth-Token: '.$config_data['apitoken'].''),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if($err) {
			echo $err;
		}  else  {
		    $product_create = json_decode($response);
			// echo '<pre>';
			// print_r($product_create);

		    if(isset($product_create->data) && !empty($product_create->data)) {

		  		// $to_productid  = 112;
		  		$to_productid  = $product_create->data->id;
				$to_producturl = $product_create->data->custom_url->url;
				
				$this->productmodel->UpdateProductStatus($product_code, $to_productid, $to_producturl);
			
				echo $product_code." - Product Create Successfully..<br>";		

				if(isset($imageurls) && !empty($imageurls)) {
					$s = 0;
					foreach($imageurls as $imageurl) {
						$images = array();
						if($s == 0) {
							$images['image_url'] = $imageurl;
							$images['is_thumbnail'] = true;
							$images['sort_order'] = $s;
						} else {
							$images['image_url'] = $imageurl;
							$images['is_thumbnail'] = false;
							$images['sort_order'] = $s;
						}
						
						$encodedToken = base64_encode("".$config_data['client_id'].":".$config_data['apitoken']."");
						$authHeaderString = 'Authorization: Basic ' . $encodedToken;
						$images_data = json_encode($images);
						
						$curl = curl_init();

						curl_setopt_array($curl, array(
						CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products/".$to_productid."/images",
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => $images_data,
						CURLOPT_HTTPHEADER => array($authHeaderString,'Accept: application/json',
							'Content-Type: application/json',
							'X-Auth-Client: '.$config_data['client_id'].'',
							'X-Auth-Token: '.$config_data['apitoken'].''),			  
						));

						$img_response = curl_exec($curl);
						$err = curl_error($curl);

						curl_close($curl);

						if ($err) {
						echo "cURL Error #:" . $err;
						} else {
							$product_img_create = json_decode($img_response);
							
							if(isset($product_img_create->data) && !empty($product_img_create->data)) { 
								$im = $s + 1;
								echo $product_code." - ".$im." Product Image Create Successfully..<br>";	
							} else {
								$error1 = @$product_img_create->title;					
								echo $product_code.' - Product Image Create Error '. $error1;			
							}
						}
					$s++;
					}
				}

				if(isset($pro_video) && !empty($pro_video)) {
					$vp = 0;
					foreach($pro_video as $pVideo) {
						$Videos = array();
						$Videos['description'] = $pVideo['description'];
						$Videos['sort_order']  = $pVideo['sort'];
						$Videos['type'] 	   = 'youtube';
						$Videos['video_id']    = $pVideo['youTubeId'];
						
						$encodedToken = base64_encode("".$config_data['client_id'].":".$config_data['apitoken']."");
						$authHeaderString = 'Authorization: Basic ' . $encodedToken;
						$Videos_data = json_encode($Videos);
						
						// $to_productid = 125;
						
						$curl = curl_init();
						curl_setopt_array($curl, array(
						CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products/".$to_productid."/videos",
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => $Videos_data,
						CURLOPT_HTTPHEADER => array($authHeaderString,'Accept: application/json',
							'Content-Type: application/json',
							'X-Auth-Client: '.$config_data['client_id'].'',
							'X-Auth-Token: '.$config_data['apitoken'].''),			  
						));
		
						$video_response = curl_exec($curl);
						$err = curl_error($curl);
		
						curl_close($curl);
		
						if ($err) {
						echo "cURL Error #:" . $err;
						} else {
							$product_video_create = json_decode($video_response);
		
							// echo '<pre>';
							// print_r($product_video_create);
		
							if(isset($product_video_create->data) && !empty($product_video_create->data)) { 
								$imb = $vp + 1;
								echo $product_code." - ".$imb." Product Video Create Successfully..<br>";	
							} else {
								$error1 = @$product_video_create->title;					
								echo $product_code.' - '.$imb.' Product Video Create Error '. $error1;	
							}
						}
					}
				}
			} else {

				$error1 = @$product_create->title;					
				$error2 = $this->db->escape_str($error1);
		
				$this->productmodel->UpdateProductMessage($product_code, $error2);

				echo $product_code.' - Product Import Error '. $error1;
			}
		}		
	}
}
?>