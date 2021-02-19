<?php 
use Bigcommerce\Api\Client as Bigcommerce;
class Product extends CI_controller{
	
	function Vbproduct()
	{
		parent::__construct();	
		$this->load->model("admin/productmodel");
		$this->load->library('bigcommerceapi');
		$this->load->library('mcurl');
		
		include(APPPATH.'third_party/PHPExcel.php');
		include(APPPATH.'third_party/PHPExcel/Writer/Excel2007.php');
		include(APPPATH.'third_party/bcapi/vendor/autoload.php');
	}

	function index()
	{
		$session_data = $this->session->userdata('admin_session');
		if(!isset($session_data) || empty($session_data))redirect('admin/login');
				
		$this->data["page_head"]  = 'Volusion to BigCommerce Product Import';
		$this->data["page_title"] = 'Volusion to BigCommerce Product Import';
		
		$product_data = $this->productmodel->getProduct();
		
		$this->data['total_product'] = count($product_data);
		$this->data['product_data']  = $product_data;
		
		$this->load->view("admin/common/header",$this->data);
		$this->data['left_nav']=$this->load->view('admin/common/leftmenu',$this->data,true);	
		$this->load->view("admin/vbproduct/list",$this->data);
		$this->load->view("admin/common/footer");
	}
	
	function updateproduct() {
		header('Content-Type: text/html; charset=utf-8');
		
		$product_sku   = $this->input->get('code');
		$bc_product_id = $this->input->get('bc_id');
		
		$setting 			= $this->productmodel->getGeneralSetting();
		$storeurl_volusion 	= $setting['storeurl_volusion'];
		$loginemail 		= $setting['login_email'];
		$encryptedpassword	= $setting['encryptedpassword'];
								
		$bcstoreurl  	= $setting['storeurl'];
		$client_id		= $setting['client_id'];
		$auth_token   	= $setting['apitoken'];
		$store_hash    	= $setting['storehash'];
		
		Bigcommerce::configure(array( 'client_id' => $client_id, 'auth_token' => $auth_token, 'store_hash' => $store_hash )); // Bc class connection	
		Bigcommerce::verifyPeer(false); // SSL verify False
		Bigcommerce::failOnError(); // Display error exception on
		
		// Get product information form volusion
		$product_data        = @file_get_contents($storeurl_volusion."/net/WebService.aspx?Login=".$loginemail."&EncryptedPassword=".$encryptedpassword."&EDI_Name=Generic\Products&SELECT_Columns=p.StockStatus,p.ProductPopularity,p.ProductCode,p.DoNotAllowBackOrders,pe.SalePrice,pe.ProductPrice&WHERE_Column=p.ProductCode&WHERE_Value=".str_replace(' ','%20',$product_sku)."");
		$product_data_e 	 = simplexml_load_string($product_data);
		$product_data_decode = json_encode($product_data_e);
		$product_data_res 	 = json_decode($product_data_decode,TRUE);
		$product_details 	 = array();
		$product_detailsp 	 = array();
		$product_details 	 = $product_data_res['Products'];
		
		// DoNotAllowBackOrders
		$DoNotAllowBackOrders =  "none";
		if(isset($product_details['DoNotAllowBackOrders']) && !empty($product_details['DoNotAllowBackOrders']) && $product_details['DoNotAllowBackOrders'] == 'N'){
			$DoNotAllowBackOrders = "simple";
		}
		// DoNotAllowBackOrders
		$StockStatus = 0;
		if(isset($product_details['StockStatus']) && !empty($product_details['StockStatus']) && $product_details['StockStatus'] > 0){
			$StockStatus =  $product_details['StockStatus'];
		}
		
		// Price
		$SalePrice = '';
		if(isset($product_details['SalePrice']) && !empty($product_details['SalePrice'])){
			 $ProductPrice = $product_details['ProductPrice'];
			 $product_detailsp['retail_price'] 				= $ProductPrice;
		}
		
		// Stock
		$product_detailsp['inventory_tracking'] 		= $DoNotAllowBackOrders;
		$product_detailsp['inventory_level'] 			= $StockStatus;
		
		// Ordering
		if(isset($product_details['ProductPopularity']) && !empty($product_details['ProductPopularity'])){
			$product_detailsp['sort_order'] = '-'.$product_details['ProductPopularity'];
		}
		
		$updateproduct_product							= Bigcommerce::updateProduct($bc_product_id,$product_detailsp);
		
		if(isset($updateproduct_product->id) && !empty($updateproduct_product->id)){
			$this->productmodel->updateotherstatus($bc_product_id);
			echo '<br>';
			echo $product_sku.' - Product price and stock and ordering update successfully...';
		}
		
		/*if(isset($updateproduct_product->description) && !empty($updateproduct_product->description)){
			$product_detailsp				  = array();
			$description = '';
			$description = str_replace('<a href="http://www.jewelrykeepsakes.com/How-do-I-fill-my-Cremation-Jewelry-s/146.htm">Instructions</a>','<a href="/how-do-i-fill-my-cremation-jewelry/">Instructions</a>',$updateproduct_product->description);
			$product_detailsp['description']  = $description;
			$updateproduct_product			  = Bigcommerce::updateProduct($bc_product_id,$product_detailsp);
			
			echo $product_sku.' - Product description update successfully...';
		}
		
		*/
			
		
	}
	
	
	
	function getproductimages() {
	
		$setting_volusion 		= $this->productmodel->getGeneralSetting();
		$storeurl_volusion 		= $setting_volusion[0]['storeurl_volusion'];
		$loginemail 		    = $setting_volusion[0]['loginemail'];
		$encryptedpassword      = $setting_volusion[0]['encryptedpassword'];
		$bcstoreurl  			= $setting_volusion[0]['storeurl'];
		$apiusername			= $setting_volusion[0]['apiusername'];
		$apitoken   			= $setting_volusion[0]['apitoken'];
		$apipath    			= $setting_volusion[0]['apipath'];
		
		// Store connection
		$store = new Bigcommerceapi($apiusername, $apipath , $apitoken);
		// Bc class connection
		Bigcommerce::configure(array('store_url' => $bcstoreurl,'username'  => $apiusername,'api_key'   => $apitoken));		
		// SSL verify False
		Bigcommerce::verifyPeer(false);
		// Display error exception on
		Bigcommerce::failOnError();
		
		$get_all_products = $this->productmodel->getAllProducts();
		
		if(isset($get_all_products) && !empty($get_all_products))
		{
			foreach($get_all_products as $get_all_products_s)
			{
				$productimage = Bigcommerce::getProductImages($get_all_products_s['bc_product_id']);
				
				if(empty($productimage))
				{
					$this->productmodel->UpdateimageStatus($get_all_products_s['product_sku']);
				}
			}
		}
	}
	
	function preparcsvfile()
	{
		$setting_volusion 		= $this->productmodel->getGeneralSetting();
		$storeurl_volusion 		= $setting_volusion[0]['storeurl_volusion'];
		$loginemail 		    = $setting_volusion[0]['loginemail'];
		$encryptedpassword      = $setting_volusion[0]['encryptedpassword'];
		$bcstoreurl  			= $setting_volusion[0]['storeurl'];
		$apiusername			= $setting_volusion[0]['apiusername'];
		$apitoken   			= $setting_volusion[0]['apitoken'];
		$apipath    			= $setting_volusion[0]['apipath'];
		
		// Store connection
		$store = new Bigcommerceapi($apiusername, $apipath , $apitoken);
		// Bc class connection
		Bigcommerce::configure(array('store_url' => $bcstoreurl,'username'  => $apiusername,'api_key'   => $apitoken));		
		// SSL verify False
		Bigcommerce::verifyPeer(false);
		// Display error exception on
		Bigcommerce::failOnError();
				
		
		$product_edisnial_file = APPPATH."third_party/product/product_image_brand_missin.xls";
		$spreadsheet 		   = new PHPExcel();
		$spreadsheet->setActiveSheetIndex(0);
		$worksheet 			   = $spreadsheet->getActiveSheet();
		
		$worksheet->setCellValueExplicit('A1','Item Type', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('B1','Product Name', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('C1','Product Type', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('D1','Product Code/SKU', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('E1','Brand Name', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('F1','Category', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('G1','Product Image File - 1', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('H1','Product Image Description - 1', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('I1','Product Image Is Thumbnail - 1', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('J1','Product Image Sort - 1', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('K1','Product Image File - 2', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('L1','Product Image Description - 2', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('M1','Product Image Is Thumbnail - 2', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('N1','Product Image Sort - 2', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('O1','Product Image File - 3', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('P1','Product Image Description - 3', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('Q1','Product Image Is Thumbnail - 3', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('R1','Product Image Sort - 3', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('S1','Product Image File - 4', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('T1','Product Image Description - 4', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('U1','Product Image Is Thumbnail - 4', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('V1','Product Image Sort - 4', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('W1','Product Image File - 5', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('X1','Product Image Description - 5', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('Y1','Product Image Is Thumbnail - 5', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('Z1','Product Image Sort - 5', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AA1','Product Image File - 6', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AB1','Product Image Description - 6', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AC1','Product Image Is Thumbnail - 6', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AD1','Product Image Sort - 6', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AE1','Product Image File - 7', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AF1','Product Image Description - 7', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AG1','Product Image Is Thumbnail - 7', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AH1','Product Image Sort - 7', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AI1','Product Image File - 8', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AJ1','Product Image Description - 8', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AK1','Product Image Is Thumbnail - 8', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AL1','Product Image Sort - 8', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AM1','Product Image File - 9', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AN1','Product Image Description - 9', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AO1','Product Image Is Thumbnail - 9', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AP1','Product Image Sort - 9', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AQ1','Product Image File - 10', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AR1','Product Image Description - 10', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AS1','Product Image Is Thumbnail - 10', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AT1','Product Image Sort - 10', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AU1','Product Image File - 11', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AV1','Product Image Description - 11', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AW1','Product Image Is Thumbnail - 11', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AX1','Product Image Sort - 11', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AY1','Product Image File - 12', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('AZ1','Product Image Description - 12', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BA1','Product Image Is Thumbnail - 12', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BB1','Product Image Sort - 12', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BC1','Product Image File - 13', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BD1','Product Image Description - 13', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BE1','Product Image Is Thumbnail - 13', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BF1','Product Image Sort - 13', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BG1','Product Image File - 14', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BH1','Product Image Description - 14', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BI1','Product Image Is Thumbnail - 14', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BJ1','Product Image Sort - 14', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BK1','Product Image File - 15', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BL1','Product Image Description - 15', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BM1','Product Image Is Thumbnail - 15', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BN1','Product Image Sort - 15', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BO1','Product Image File - 16', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BP1','Product Image Description - 16', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BQ1','Product Image Is Thumbnail - 16', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BR1','Product Image Sort - 16', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BS1','Product Image File - 17', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BT1','Product Image Description - 17', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BU1','Product Image Is Thumbnail - 17', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BV1','Product Image Sort - 17', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BW1','Product Image File - 18', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BX1','Product Image Description - 18', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BY1','Product Image Is Thumbnail - 18', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('BZ1','Product Image Sort - 18', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('CA1','Product Image File - 19', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('CB1','Product Image Description - 19', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('CC1','Product Image Is Thumbnail - 19', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('CD1','Product Image Sort - 19', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('CE1','Product Image File - 20', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('CF1','Product Image Description - 20', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('CG1','Product Image Is Thumbnail - 20', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('CH1','Product Image Sort - 20', PHPExcel_Cell_DataType::TYPE_STRING);
		$worksheet->setCellValueExplicit('CI1','GPS Manufacturer Part Number', PHPExcel_Cell_DataType::TYPE_STRING);
		
		$get_all_products = $this->productmodel->getAllProducts();
		
		$live_image_URL = 'http://www.jewelrykeepsakes.com/v/vspfiles/photos/';
		
		if(isset($get_all_products) && !empty($get_all_products))
		{
			$column = 2;
			foreach($get_all_products as $get_all_products_s)
			{
				// Get product information form volusion
				$product_data        = @file_get_contents($storeurl_volusion."/net/WebService.aspx?Login=".$loginemail."&EncryptedPassword=".$encryptedpassword."&EDI_Name=Generic\Products&SELECT_Columns=p.ProductName,p.ProductCode,p.Vendor_PartNo,pe.ProductManufacturer&WHERE_Column=p.ProductCode&WHERE_Value=".str_replace(' ','%20',$get_all_products_s['product_sku'])."");
				$product_data_e 	 = simplexml_load_string($product_data);
				$product_data_decode = json_encode($product_data_e);
				$product_data_res 	 = json_decode($product_data_decode,TRUE);
				$product_details 	 = array();
				$product_detailsp 	 = array();
				$product_details 	 = $product_data_res['Products'];
				
				$ProductName = '';
				if(isset($product_details['ProductName']) && !empty($product_details['ProductName'])){
					 $ProductName = $product_details['ProductName'];
				}
				$ProductCode = '';
				if(isset($product_details['ProductCode']) && !empty($product_details['ProductCode'])){
					 $ProductCode = $product_details['ProductCode'];
				}
				$Brandname = '';
				if(isset($product_details['ProductManufacturer']) && !empty($product_details['ProductManufacturer'])){
					 $Brandname = $product_details['ProductManufacturer'];
				}
				$Partnumber = '';
				if(isset($product_details['Vendor_PartNo']) && !empty($product_details['Vendor_PartNo'])){
					 $Partnumber = $product_details['Vendor_PartNo'];
				}
				
				$worksheet->setCellValueExplicit('A'.$column,'Product', PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValueExplicit('B'.$column,$ProductName, PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValueExplicit('C'.$column,'P', PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValueExplicit('D'.$column,$ProductCode, PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValueExplicit('E'.$column,$Brandname, PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValueExplicit('F'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
				
				// Image Set
				for($i=2;$i<=20;$i++)
				{
					$check_image   = APPPATH."third_party/product_images/vspfiles/photos/".$get_all_products_s['product_sku'].'-'.$i.'.jpg';
					$final_img_url = '';
					
					if(@file_exists($check_image)){
						$image_title = str_replace(' ','%20',$get_all_products_s['product_sku']).'-'.$i.'.jpg';
						$final_img_url = $live_image_URL.$image_title;
					}
					
					if($i == 2){
						if(empty($final_img_url))
						{
							$check_image2t   = APPPATH."third_party/product_images/vspfiles/photos/".$get_all_products_s['product_sku'].'-'.$i.'T.jpg';
							if(@file_exists($check_image2t)){
								$image_title2t = str_replace(' ','%20',$get_all_products_s['product_sku']).'-'.$i.'T.jpg';
								$final_img_url = $live_image_URL.$image_title2t;
							}
						}
						
						$worksheet->setCellValueExplicit('G'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('H'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('I'.$column,'Y', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('J'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 3){
						$worksheet->setCellValueExplicit('K'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('L'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('M'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('N'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 4){
						$worksheet->setCellValueExplicit('O'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('P'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('Q'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('R'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 5){
						$worksheet->setCellValueExplicit('S'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('T'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('U'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('V'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 6){
						$worksheet->setCellValueExplicit('W'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('X'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('Y'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('Z'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 7){
						$worksheet->setCellValueExplicit('AA'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AB'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AC'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AD'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 8){
						$worksheet->setCellValueExplicit('AE'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AF'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AG'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AH'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 9){
						$worksheet->setCellValueExplicit('AI'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AJ'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AK'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AL'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 10){
						$worksheet->setCellValueExplicit('AM'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AN'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AO'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AP'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 11){
						$worksheet->setCellValueExplicit('AQ'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AR'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AS'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AT'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 12){
						$worksheet->setCellValueExplicit('AU'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AV'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AW'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AX'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 13){
						$worksheet->setCellValueExplicit('AY'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('AZ'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BA'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BB'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 14){
						$worksheet->setCellValueExplicit('BC'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BD'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BE'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BF'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 15){
						$worksheet->setCellValueExplicit('BG'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BH'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BI'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BJ'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 16){
						$worksheet->setCellValueExplicit('BK'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BL'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BM'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BN'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 17){
						$worksheet->setCellValueExplicit('BO'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BP'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BQ'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BR'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 18){
						$worksheet->setCellValueExplicit('BS'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BT'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BU'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BV'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 19){
						$worksheet->setCellValueExplicit('BW'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BX'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BY'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('BZ'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 20){
						$worksheet->setCellValueExplicit('CA'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('CB'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('CC'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('CD'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					if($i == 21){
						$worksheet->setCellValueExplicit('CE'.$column,$final_img_url, PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('CF'.$column,'', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('CG'.$column,'N', PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->setCellValueExplicit('CH'.$column,$i, PHPExcel_Cell_DataType::TYPE_STRING);
					}
				}
				$worksheet->setCellValueExplicit('CI'.$column,$Partnumber, PHPExcel_Cell_DataType::TYPE_STRING);
				$column++;
			}
		}
		$writer = new PHPExcel_Writer_Excel2007($spreadsheet);
		$writer->save($product_edisnial_file);
		
	}
	
	function UpdateCategoryID()
	{
		$this->productmodel->UpdatecategoryIDtoBCID();
	}
	
	
	function updateproductnotmigrated()
	{
		$setting_volusion 		= $this->productmodel->getGeneralSetting();
		$storeurl_volusion 		= $setting_volusion[0]['storeurl_volusion'];
		$loginemail 		    = $setting_volusion[0]['loginemail'];
		$encryptedpassword      = $setting_volusion[0]['encryptedpassword'];
		$bcstoreurl  			= $setting_volusion[0]['storeurl'];
		$apiusername			= $setting_volusion[0]['apiusername'];
		$apitoken   			= $setting_volusion[0]['apitoken'];
		$apipath    			= $setting_volusion[0]['apipath'];
		
		// Store connection
		$store = new Bigcommerceapi($apiusername, $apipath , $apitoken);
		
		// Bc class connection
		Bigcommerce::configure(array('store_url' => $bcstoreurl,'username'  => $apiusername,'api_key'   => $apitoken));		
		
		// SSL verify False
		Bigcommerce::verifyPeer(false);
		// Display error exception on
		Bigcommerce::failOnError();
		
		//$ProductCode = $_REQUEST['productcode'];
		
		$ProductCode = $this->input->get('productcode');
		
		
		
		if(isset($ProductCode) && !empty($ProductCode))
		{
			// Get product information form volusion
			$product_data        = @file_get_contents($storeurl_volusion."/net/WebService.aspx?Login=".$loginemail."&EncryptedPassword=".$encryptedpassword."&EDI_Name=Generic\Products&SELECT_Columns=*,pe.ProductCategory&WHERE_Column=p.ProductCode&WHERE_Value=".str_replace(' ','%20',$ProductCode)."");
			$product_data_e 	 = simplexml_load_string($product_data);
			$product_data_decode = json_encode($product_data_e);
			$product_data_res 	 = json_decode($product_data_decode,TRUE);
			$product_details 	 = array();
			$product_detailsp 	 = array();
			$product_details 	 = @$product_data_res['Products'];
			
			
			// Get Option set id in DB
			$bc_option_set_id 								= $this->productmodel->getProductOptionssetID($ProductCode);
			$bc_product_ids_db 								= $this->productmodel->getBCproductID($ProductCode);
			if(isset($bc_product_ids_db) && !empty($bc_product_ids_db))
			{
				$productid 	= $bc_product_ids_db;
			}
			
			if(isset($productid) && !empty($productid))
			{
				// Create Options
				$getProductOption = $this->productmodel->getProductOptions($ProductCode);
				$product_option = array();
				if(isset($getProductOption) && !empty($getProductOption))
				{
					// Create Option Set
					$option_set_id = '';
					if(isset($bc_option_set_id) && !empty($bc_option_set_id)){
						$option_set_id = $bc_option_set_id;
					}else{
						$option_set_details 		= array();
						$option_set_details['name'] = $ProductCode;
						$osets_details 				= Bigcommerce::createOptionSet($option_set_details);
						$option_set_id 				= $osets_details->id;
						$this->productmodel->UpdateOptionSetID($ProductCode,$option_set_id);
					}
					$product_detailsp_update 					= array();
					$product_detailsp_update['option_set_id'] 	= $option_set_id; 
					$updateproduct_assing_option_set			= Bigcommerce::updateProduct($productid,$product_detailsp_update);
					
					$main_option_a = array();
					$option_ids_a  = array();
					$opr = 1;
					$newarray = array();
					
					$i = 1;
					foreach($getProductOption as $options_s)
					{
						$option_details = $this->productmodel->getOptiondetails($options_s);
						$newarray[$option_details['id']]['headinggroup'] 		 = $option_details['headinggroup'];
						$newarray[$option_details['id']]['optioncategoriesdesc'] = $option_details['optioncategoriesdesc'];
						$newarray[$option_details['id']]['displaytype'] 		 = $option_details['displaytype'];
						unset($option_details['headinggroup']);
						unset($option_details['optioncategoriesdesc']);
						unset($option_details['displaytype']);
						$newarray[$option_details['id']][] = $option_details;
						$i++;
					}
					
					if(isset($newarray) && !empty($newarray))
					{
							
						foreach($newarray as $newarray_s)
						{
								$product_option = array();
								$option_type = 'S';
								if(isset($newarray_s['displaytype']) && !empty($newarray_s['displaytype']) && $newarray_s['displaytype'] == 'DROPDOWN'){
									$option_type = 'S';
								}
								if(isset($newarray_s['displaytype']) && !empty($newarray_s['displaytype']) && $newarray_s['displaytype'] == 'TEXTBOX'){
									$option_type = 'T';
								}
								if(isset($newarray_s['displaytype']) && !empty($newarray_s['displaytype']) && $newarray_s['displaytype'] == 'CHECKBOX'){
									$option_type = 'C';
								}
								
								if(isset($newarray_s['optioncategoriesdesc']) && !empty($newarray_s['optioncategoriesdesc'])){
									$display_n_m = $newarray_s['optioncategoriesdesc'];
								}else{
									$display_n_m = $newarray_s['headinggroup'];
								}
								
								$unc_option 		 = $this->uniqnumberop();
								$option_name 		 = $display_n_m.'-'.$unc_option.'_'.$ProductCode;
								$option_display_name = $display_n_m;
								if(isset($newarray_s['displaytype']) && !empty($newarray_s['displaytype']) && $newarray_s['displaytype'] == 'TEXTBOX'){
									$option_name 		 = $newarray_s['optioncategoriesdesc'].'-'.$unc_option.'_'.$ProductCode;
									$option_display_name = $newarray_s['optioncategoriesdesc'];
								}
								$product_option['name']				= $option_name;
								$product_option['type']				= $option_type;
								$product_option['display_name']		= $option_display_name;
								
								// Create Option
								$create_options 					= Bigcommerce::createOption($product_option);
								$option_id 							= $create_options->id;
								
								// Assing Option To Option Set
								$optionIDS 							= array('option_id' => $option_id);
								$options_set_assing_option  		=  Bigcommerce::createOptionSetOption($optionIDS, $option_set_id);
								
								unset($newarray_s['headinggroup']);
								unset($newarray_s['optioncategoriesdesc']);
								unset($newarray_s['displaytype']);
								
								$short_o = 1;
								foreach($newarray_s as $newarray_s_v)
								{
									$create_option_value_id_f = '';
									if(isset($newarray_s_v['optionsdesc']) && !empty($newarray_s_v['optionsdesc']))
									{
										$create_option_value = array();
										$default_option_set  = false;
										$label_r = "Add $";
										if ( $newarray_s_v['pricediff'] < 0 ) {
										   $label_r = "Remove $";
										}
										$label_pref_p = ' ['.$label_r.$newarray_s_v['pricediff'].']';
										if($newarray_s_v['pricediff'] == '0.00'){
											$default_option_set = true;
											$label_pref_p = '';
										}
										$short_n = rand(0,8);
										if(isset($newarray_s_v['arrangeoptionsby']) && !empty($newarray_s_v['arrangeoptionsby']))
										{
											$short_n = $newarray_s_v['arrangeoptionsby'];
										}
										$create_option_value['label'] 	   = $newarray_s_v['optionsdesc'].$label_pref_p;
										$create_option_value['value'] 	   = $newarray_s_v['optionsdesc'];
										$create_option_value['is_default'] = $default_option_set;
										$create_option_value['sort_order'] = $short_n;
										
										$create_option_value_id 		   = Bigcommerce::createOptionValue($option_id,$create_option_value);
										$create_option_value_id_f 		   = $create_option_value_id->id;
										
										// Get Product Option ID
										$product_option_id_bc = $store->get('/products/'.$productid.'/options.json');
										if(isset($product_option_id_bc) && !empty($product_option_id_bc))
										{
											foreach($product_option_id_bc as $product_option_id_bc_s)
											{
												if($product_option_id_bc_s['option_id'] == $option_id){
													$product_option_id = $product_option_id_bc_s['id'];
												}
											}
										}
										
										// Assign Rule For Product
										$optionreuls = array();
										if(isset($product_option_id) && !empty($product_option_id))
										{
											if($option_type == 'S')
											{
												settype($newarray_s_v['pricediff'], "float");
												$optionreuls['conditions'][$short_o]['sku_id'] 				= null;
												$optionreuls['conditions'][$short_o]['option_value_id']     = $create_option_value_id_f;
												$optionreuls['conditions'][$short_o]['product_option_id']  	= $product_option_id;
												$optionreuls['is_enabled'] 	  								= true;
												$optionreuls['price_adjuster']['adjuster'] 					= 'relative';
												$optionreuls['price_adjuster']['adjuster_value'] 			=  $newarray_s_v['pricediff'];
												$productoption_rule 										= Bigcommerce::createOptionRules($productid,$optionreuls);
											}
										}
										$short_o++;
									}
								}
								echo $option_id.' - Product Option Import Successfully...'.'<br>';	
						 }
				   }
				   else
				   {
						echo $product_code." - No Options Found...".'<br>';
				   }
				}
			}
			else
			{
				echo 'Product ID Not found...';
			}
		 }
		 else
		 {
			echo 'Please enter valid Product Code...';
		 }
	}
	
	
	function updateproductordering()
	{
		$product_sku   = $this->input->get('code');
		$bc_product_id = $this->input->get('bc_id');
		
		$setting_volusion 		= $this->productmodel->getGeneralSetting();
		$storeurl_volusion 		= $setting_volusion[0]['storeurl_volusion'];
		$loginemail 		    = $setting_volusion[0]['loginemail'];
		$encryptedpassword      = $setting_volusion[0]['encryptedpassword'];
		$bcstoreurl  			= $setting_volusion[0]['storeurl'];
		$apiusername			= $setting_volusion[0]['apiusername'];
		$apitoken   			= $setting_volusion[0]['apitoken'];
		$apipath    			= $setting_volusion[0]['apipath'];
		
		
		// Bc class connection
		Bigcommerce::configure(array('store_url' => $bcstoreurl,'username'  => $apiusername,'api_key'   => $apitoken));	
		
		// SSL verify False
		Bigcommerce::verifyPeer(false);
		// Display error exception on
		Bigcommerce::failOnError();
		
		// Get product information form volusion
		$product_data        = @file_get_contents($storeurl_volusion."/net/WebService.aspx?Login=".$loginemail."&EncryptedPassword=".$encryptedpassword."&EDI_Name=Generic\Products&SELECT_Columns=p.StockStatus,p.ProductPopularity,p.ProductCode,p.DoNotAllowBackOrders&WHERE_Column=p.ProductCode&WHERE_Value=".str_replace(' ','%20',$product_sku)."");
		$product_data_e 	 = simplexml_load_string($product_data);
		$product_data_decode = json_encode($product_data_e);
		$product_data_res 	 = json_decode($product_data_decode,TRUE);
		$product_details 	 = array();
		$product_detailsp 	 = array();
		$product_details 	 = $product_data_res['Products'];
		
		if(isset($product_details['ProductPopularity']) && !empty($product_details['ProductPopularity']))
		{
			$product_detailsp['sort_order'] = '-'.$product_details['ProductPopularity'];
			$updateproduct_product	= Bigcommerce::updateProduct($bc_product_id,$product_detailsp);
			$this->productmodel->UpdateOrderingStatus($bc_product_id);
			echo $bc_product_id.' - Product Ordering Update Successfully...';
		}else{
			echo $bc_product_id.' - Product ordering not found in volusion...'.'<br/>';
			
		}
		
		
	}
	
	
	
	function uniqnumberop() {
		 $password = '';
		 $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		 for($i = 0; $i < 8; $i++)
		 {
			 $random_int = mt_rand();
			 $password .= $charset[$random_int % strlen($charset)];
		 }
		 return $password;
	}
	
	
	
	function import()
	{
		$code = $this->input->get('code');
		if(isset($code) && !empty($code)){
			$this->getVolusionProduct($code);
		}
	}
	
	function getVolusionProduct($product_code)
	{
		$setting_volusion 		= $this->productmodel->getGeneralSetting();
		$storeurl_volusion 		= $setting_volusion[0]['storeurl_volusion'];
		$loginemail 		    = $setting_volusion[0]['loginemail'];
		$encryptedpassword      = $setting_volusion[0]['encryptedpassword'];
		$bcstoreurl  			= $setting_volusion[0]['storeurl'];
		$apiusername			= $setting_volusion[0]['apiusername'];
		$apitoken   			= $setting_volusion[0]['apitoken'];
		$apipath    			= $setting_volusion[0]['apipath'];
		
		// Store connection
		$store = new Bigcommerceapi($apiusername, $apipath , $apitoken);
		
		// Bc class connection
		Bigcommerce::configure(array('store_url' => $bcstoreurl,'username'  => $apiusername,'api_key'   => $apitoken));		
		
		// SSL verify False
		Bigcommerce::verifyPeer(false);
		// Display error exception on
		Bigcommerce::failOnError();
		
		// Get product information form volusion
		$product_data        = @file_get_contents($storeurl_volusion."/net/WebService.aspx?Login=".$loginemail."&EncryptedPassword=".$encryptedpassword."&EDI_Name=Generic\Products&SELECT_Columns=*,pe.SalePrice,pe.ProductCategory&WHERE_Column=p.ProductCode&WHERE_Value=".str_replace(' ','%20',$product_code)."");
		$product_data_e 	 = simplexml_load_string($product_data);
		$product_data_decode = json_encode($product_data_e);
		$product_data_res 	 = json_decode($product_data_decode,TRUE);
		$product_details 	 = array();
		$product_detailsp 	 = array();
		$product_details 	 = $product_data_res['Products'];
		
	
		//Product Name
		$ProductName = '';
		if(isset($product_details['ProductName']) && !empty($product_details['ProductName'])){
			 $ProductName = $product_details['ProductName'].' '.$product_details['ProductCode'];
		}
		//Product Price
		$ProductPrice = 0.00;
		if(isset($product_details['ProductPrice']) && !empty($product_details['ProductPrice'])){
			 $ProductPrice = $product_details['ProductPrice'];
		}
		//Product SetupCost
		$SetupCost = '';
		if(isset($product_details['SetupCost']) && !empty($product_details['SetupCost'])){
			 $SetupCost = $product_details['SetupCost'];
		}
		//Product SalePrice
		$SalePrice = '';
		if(isset($product_details['SalePrice']) && !empty($product_details['SalePrice'])){
			 $SalePrice = $product_details['SalePrice'];
		}
		//Product ListPrice
		$ListPrice = '';
		if(isset($product_details['ListPrice']) && !empty($product_details['ListPrice'])){
			 $ListPrice = $product_details['ListPrice'];
		}		
		//Product SKU
		$ProductCode = '';
		if(isset($product_details['ProductCode']) && !empty($product_details['ProductCode'])){
			 $ProductCode = $product_details['ProductCode'];
		}
		//Product ProductWeight
		$ProductWeight = '0';
		if(isset($product_details['ProductWeight']) && !empty($product_details['ProductWeight'])){
			 $ProductWeight = $product_details['ProductWeight'];
		}
		
		//Product Category
		$ProductCategory 	  = $this->productmodel->getvolusionCategory($product_details['ProductCode']);
		if(isset($ProductCategory) && !empty($ProductCategory))
		{
			$ProductCategory 	  = $ProductCategory;
		}
		else
		{
			$ProductCategory 	  = array('185'); // shop category
		}
		
		
		//Product Availability
		$Availability = '';
		if(isset($product_details['Availability']) && !empty($product_details['Availability'])){
			 $Availability = $product_details['Availability'];
		}
		// Product status
		$ProductStatus = true;
		if(isset($product_details['HideProduct']) && !empty($product_details['HideProduct']) && $product_details['HideProduct'] == 'Y'){
			$ProductStatus = false;
		}
		
		// Product Description 
		$ProductDescription = '';
		if(isset($product_details['ProductDescription']) && !empty($product_details['ProductDescription'])){
			// Image Path replace 
			$description_p = str_replace('src="http://www.jewelrykeepsakes.com','src="/content',$product_details['ProductDescription']);
			$description_p = str_replace('src="/v/','src="/content/v/',$description_p);
			$ProductDescription = '<div class="ProductDescription">'.$description_p.'</div>';
		}
		// Product Features
		if(isset($product_details['ProductFeatures']) && !empty($product_details['ProductFeatures'])){
			// Image Path replace 
			$description_p_ProductFeatures = str_replace('src="http://www.jewelrykeepsakes.com','src="/content',$product_details['ProductFeatures']);
			$description_p_ProductFeatures = str_replace('src="/v/','src="/content/v/',$description_p_ProductFeatures);
			$ProductDescription .= '<div class="ProductFeatures">'.$description_p_ProductFeatures.'</div>';
		}
		
		// Product ExtInfo
		if(isset($product_details['ExtInfo']) && !empty($product_details['ExtInfo'])){
			// Image Path replace 
			$description_p_ExtInfo = str_replace('src="http://www.jewelrykeepsakes.com','src="/content',$product_details['ExtInfo']);
			$description_p_ExtInfo = str_replace('src="/v/','src="/content/v/',$description_p_ExtInfo);
			$ProductDescription .= '<div class="ExtInfo">'.$description_p_ExtInfo.'</div>';
		}
		
		// Product ProductDescription_AbovePricing
		if(isset($product_details['ProductDescription_AbovePricing']) && !empty($product_details['ProductDescription_AbovePricing'])){
			// Image Path replace 
			$description_p_ProductDescription_AbovePricing = str_replace('src="http://www.jewelrykeepsakes.com','src="/content',$product_details['ProductDescription_AbovePricing']);
			$description_p_ProductDescription_AbovePricing = str_replace('src="/v/','src="/content/v/',$description_p_ProductDescription_AbovePricing);
			$ProductDescription .= '<div class="ProductDescription_AbovePricing">'.$description_p_ProductDescription_AbovePricing.'</div>';
		}
		
		// Product TechSpecs
		$TechSpecs = '';
		if(isset($product_details['TechSpecs']) && !empty($product_details['TechSpecs'])){
			// Image Path replace 
			$TechSpecs_d = str_replace('src="http://www.jewelrykeepsakes.com','src="/content',$product_details['TechSpecs']);
			$TechSpecs_d = str_replace('src="/v/','src="/content/v/',$TechSpecs_d);
			$TechSpecs = '<div class="TechSpecs">'.$TechSpecs_d.'</div>';
		}
		
		// Free Shipping
		$FreeShippingItem = true;
		if(isset($product_details['FreeShippingItem']) && !empty($product_details['FreeShippingItem']) && $product_details['FreeShippingItem'] == 'N'){
			$FreeShippingItem =  false;
		}
		// Fixed_ShippingCost
		$Fixed_ShippingCost = '';
		if(isset($product_details['Fixed_ShippingCost']) && !empty($product_details['Fixed_ShippingCost'])){
			$Fixed_ShippingCost =  $product_details['Fixed_ShippingCost'];
		}
		
	
		// DoNotAllowBackOrders
		$DoNotAllowBackOrders =  "none";
		if(isset($product_details['DoNotAllowBackOrders']) && !empty($product_details['DoNotAllowBackOrders']) && $product_details['DoNotAllowBackOrders'] == 'N'){
			$DoNotAllowBackOrders = "simple";
		}
		
		$StockStatus = 0;
		if(isset($product_details['StockStatus']) && !empty($product_details['StockStatus']) && $product_details['StockStatus'] > 0){
			$StockStatus =  $product_details['StockStatus'];
		}
		
		
		
		// METATAG_Description
		$METATAG_Description = '';
		if(isset($product_details['METATAG_Description']) && !empty($product_details['METATAG_Description'])){
			$METATAG_Description =  $product_details['METATAG_Description'];
		}
		// METATAG_Title
		$METATAG_Title = '';
		if(isset($product_details['METATAG_Title']) && !empty($product_details['METATAG_Title'])){
			$METATAG_Title =  $product_details['METATAG_Title'];
		}
		// METATAG_Keywords
		$METATAG_Keywords = '';
		if(isset($product_details['METATAG_Keywords']) && !empty($product_details['METATAG_Keywords'])){
			$METATAG_Keywords =  $product_details['METATAG_Keywords'];
		}
		// ProductKeywords
		$ProductKeywords = '';
		if(isset($product_details['ProductKeywords']) && !empty($product_details['ProductKeywords'])){
			$ProductKeywords =  $product_details['ProductKeywords'];
		}
		// UPC_code
		$UPC_code = '';
		if(isset($product_details['UPC_code']) && !empty($product_details['UPC_code'])){
			$UPC_code =  $product_details['UPC_code'];
		}
		
		$product_sort_order = 0;
		if(isset($product_details['ProductPopularity']) && !empty($product_details['ProductPopularity']))
		{
			$product_sort_order = $product_details['ProductPopularity'];
		}
		
		// Get Option set id in DB
		$bc_option_set_id 								= $this->productmodel->getProductOptionssetID($ProductCode);
		
		$product_detailsp['name'] 						= $ProductName;
		$product_detailsp['sku'] 						= $ProductCode;
		$product_detailsp['type'] 						= 'physical';
		$product_detailsp['price']						= $ProductPrice;
		$product_detailsp['cost_price'] 				= $SetupCost;
		$product_detailsp['sale_price'] 				= $SalePrice;
		$product_detailsp['retail_price'] 				= $ListPrice;
		$product_detailsp['weight']						= $ProductWeight;
		$product_detailsp['categories'] 				= $ProductCategory;
		$product_detailsp['availability'] 				= 'available';
		$product_detailsp['availability_description'] 	= $Availability; 
		$product_detailsp['is_visible'] 				= $ProductStatus;
		$product_detailsp['description'] 				= $ProductDescription;
		$product_detailsp['warranty'] 					= $TechSpecs;
		$product_detailsp['is_free_shipping'] 			= $FreeShippingItem;
		$product_detailsp['fixed_cost_shipping_price'] 	= $Fixed_ShippingCost;
		$product_detailsp['inventory_tracking'] 		= $DoNotAllowBackOrders;
		$product_detailsp['inventory_level'] 			= $StockStatus;
		$product_detailsp['meta_description'] 			= $METATAG_Description; 
		$product_detailsp['meta_keywords'] 				= $METATAG_Keywords; 
		$product_detailsp['page_title'] 				= $METATAG_Title; 
		$product_detailsp['search_keywords'] 			= $ProductKeywords; 
		$product_detailsp['upc'] 						= $UPC_code; 
		$product_detailsp['sort_order'] 				= $product_sort_order; 
		
		
		
		
		$bc_product_ids_db = $this->productmodel->getBCproductID($ProductCode);
		if(isset($bc_product_ids_db) && !empty($bc_product_ids_db))
		{
			$productid 	= $bc_product_ids_db;
		}
		else
		{
			$product_create = Bigcommerce::createProduct($product_detailsp);
			$productid 		= $product_create->id;
			$this->productmodel->UpdateProductStatusp($productid,$ProductCode);
		}
		
		echo $productid.' - Product Import Successfully...'.'<br>';	
		
		if(isset($productid) && !empty($productid))
		{
			// Create Options
			$getProductOption = $this->productmodel->getProductOptions($ProductCode);
			$product_option = array();
			if(isset($getProductOption) && !empty($getProductOption))
			{
				// Create Option Set
				$option_set_id = '';
				if(isset($bc_option_set_id) && !empty($bc_option_set_id)){
					$option_set_id = $bc_option_set_id;
				}else{
					$option_set_details 		= array();
					$option_set_details['name'] = $ProductCode;
					$osets_details = Bigcommerce::createOptionSet($option_set_details);
					$option_set_id = $osets_details->id;
					$this->productmodel->UpdateOptionSetID($ProductCode,$option_set_id);
				}
				$product_detailsp_update 					= array();
				$product_detailsp_update['option_set_id'] 	= $option_set_id; 
				$updateproduct_assing_option_set = Bigcommerce::updateProduct($productid,$product_detailsp_update);
				
				$main_option_a = array();
				$option_ids_a  = array();
				$opr = 1;
				$newarray = array();
				
				$i = 1;
				foreach($getProductOption as $options_s)
				{
					$option_details = $this->productmodel->getOptiondetails($options_s);
					
					$newarray[$option_details['id']]['headinggroup'] = $option_details['headinggroup'];
					$newarray[$option_details['id']]['optioncategoriesdesc'] = $option_details['optioncategoriesdesc'];
					$newarray[$option_details['id']]['displaytype'] = $option_details['displaytype'];
					
					unset($option_details['headinggroup']);
					unset($option_details['optioncategoriesdesc']);
					unset($option_details['displaytype']);
					
					$newarray[$option_details['id']][] = $option_details;
					$i++;
				}
				
				if(isset($newarray) && !empty($newarray))
				{
						
					foreach($newarray as $newarray_s)
					{
							$product_option = array();
							$option_type = 'S';
							if(isset($newarray_s['displaytype']) && !empty($newarray_s['displaytype']) && $newarray_s['displaytype'] == 'DROPDOWN'){
								$option_type = 'S';
							}
							if(isset($newarray_s['displaytype']) && !empty($newarray_s['displaytype']) && $newarray_s['displaytype'] == 'TEXTBOX'){
								$option_type = 'T';
							}
							if(isset($newarray_s['displaytype']) && !empty($newarray_s['displaytype']) && $newarray_s['displaytype'] == 'CHECKBOX'){
								$option_type = 'C';
							}
							
							
							if(isset($newarray_s['optioncategoriesdesc']) && !empty($newarray_s['optioncategoriesdesc'])){
								$display_n_m = $newarray_s['optioncategoriesdesc'];
							}else{
								$display_n_m = $newarray_s['headinggroup'];
							}
						
							$unc_option 		 = $this->uniqnumberop();
							$option_name 		 = $display_n_m.'-'.$unc_option.'_'.$ProductCode;
							//$option_name 		 = $display_n_m.'_'.$ProductCode;
							$option_display_name = $display_n_m;
							if(isset($newarray_s['displaytype']) && !empty($newarray_s['displaytype']) && $newarray_s['displaytype'] == 'TEXTBOX'){
								$option_name 		 = $newarray_s['optioncategoriesdesc'].'_'.$ProductCode;
								$option_display_name = $newarray_s['optioncategoriesdesc'];
							}
							$product_option['name']				= $option_name;
							$product_option['type']				= $option_type;
							$product_option['display_name']		= $option_display_name;
							
							
							
							// Create Option
							$create_options 					= Bigcommerce::createOption($product_option);
							$option_id 							= $create_options->id;
							
							// Assing Option To Option Set
							$optionIDS 							= array('option_id' => $option_id);
							$options_set_assing_option  		=  Bigcommerce::createOptionSetOption($optionIDS, $option_set_id);
							
							unset($newarray_s['headinggroup']);
							unset($newarray_s['optioncategoriesdesc']);
							unset($newarray_s['displaytype']);
							
							$short_o = 1;
							foreach($newarray_s as $newarray_s_v)
							{
								$create_option_value_id_f = '';
								if(isset($newarray_s_v['optionsdesc']) && !empty($newarray_s_v['optionsdesc']))
								{
									$create_option_value = array();
									$default_option_set  = false;
									$label_r = "Add $";
									if ( $newarray_s_v['pricediff'] < 0 ) {
									   $label_r = "Remove $";
									}
									$label_pref_p = ' ['.$label_r.$newarray_s_v['pricediff'].']';
									if($newarray_s_v['pricediff'] == '0.00'){
										$default_option_set = true;
										$label_pref_p = '';
									}
									$short_n = rand(0,8);
									if(isset($newarray_s_v['arrangeoptionsby']) && !empty($newarray_s_v['arrangeoptionsby']))
									{
										$short_n = $newarray_s_v['arrangeoptionsby'];
									}
									$create_option_value['label'] 	   = $newarray_s_v['optionsdesc'].$label_pref_p;
									$create_option_value['value'] 	   = $newarray_s_v['optionsdesc'];
									$create_option_value['is_default'] = $default_option_set;
									$create_option_value['sort_order'] = $short_n;
									
									$create_option_value_id 		   = Bigcommerce::createOptionValue($option_id,$create_option_value);
									$create_option_value_id_f 		   = $create_option_value_id->id;
									
									// Get Product Option ID
									$product_option_id_bc = $store->get('/products/'.$productid.'/options.json');
									if(isset($product_option_id_bc) && !empty($product_option_id_bc))
									{
										foreach($product_option_id_bc as $product_option_id_bc_s)
										{
											if($product_option_id_bc_s['option_id'] == $option_id){
												$product_option_id = $product_option_id_bc_s['id'];
											}
										}
									}
									
									// Assign Rule For Product
									$optionreuls = array();
									if(isset($product_option_id) && !empty($product_option_id))
									{
										if($option_type == 'S')
										{
											settype($newarray_s_v['pricediff'], "float");
											$optionreuls['conditions'][$short_o]['sku_id'] 				= null;
											$optionreuls['conditions'][$short_o]['option_value_id']     = $create_option_value_id_f;
											$optionreuls['conditions'][$short_o]['product_option_id']  	= $product_option_id;
											$optionreuls['is_enabled'] 	  								= true;
											$optionreuls['price_adjuster']['adjuster'] 					= 'relative';
											$optionreuls['price_adjuster']['adjuster_value'] 			=  $newarray_s_v['pricediff'];
											$productoption_rule 										= Bigcommerce::createOptionRules($productid,$optionreuls);
										}
									}
									$short_o++;
								}
							}
							echo $option_id.' - Product Option Import Successfully...'.'<br>';	
					 }
			  }
			   else
			   {
					echo $product_code." - No Options Found...".'<br>';
			   }
			}
		}
		else
		{
			echo $product_code.' - Product Import Error...';
		}
	}
	
}
?>