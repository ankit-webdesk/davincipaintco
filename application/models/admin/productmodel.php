<?php
use Bigcommerce\Api\Client as Bigcommerce;
class Productmodel extends CI_Model
{
	public function __construct() {
		$this->setting_table 		= "users";
		$this->category_table		= "category";
		$this->product_table		= "products";
		
		include(APPPATH.'/third_party/bcapi/vendor/autoload.php');
	}

	public function getGeneralSetting() {
		$query = $this->db->query("SELECT * FROM ".$this->setting_table."");
		return $query->row_array();
	}

	public function getproductdata() {
		// $query_product_bc_data = $this->db->query("SELECT * FROM ".$this->product_table." WHERE bc_product_id = '' and message = '' and status = 'no'");
		$query_product_bc_data = $this->db->query("SELECT * FROM ".$this->product_table." WHERE bc_product_id != '' and dis_update_status = 'no'");
		return $query_product_bc_data->result_array();
	}

	public function getProductName($Productcode) {
		$query_product = $this->db->query("SELECT productname FROM `products` WHERE productcode LIKE '".$Productcode."'");
		return  $query_product->row_array();
	}

	public function getProductCategory($Productcode) {
		
		$query_product_cat = $this->db->query("SELECT categoryids FROM `products` WHERE `productcode` LIKE '".$Productcode."'");
		$category_data = $query_product_cat->row_array();
		
		$category = array();
		if(isset($category_data) && !empty($category_data)) {
			
			$cet_data = explode(',',$category_data['categoryids']);
			
			foreach ($cet_data as $cet_data_s) {
				$query_product_cat = $this->db->query("SELECT bc_category_id FROM `categories` WHERE `categoryid` = '".$cet_data_s."'");
				$data = $query_product_cat->row_array();
			
				if(isset($data['bc_category_id']) && !empty($data['bc_category_id']))
				{
					$category[] = $data['bc_category_id'];
				}
			}
			return $category;
		}		
		return '';
	}

	public function UpdateProductStatus($product_code,$bc_product_id,$product_url) {
		$query_update = $this->db->query("Update ".$this->product_table." set bc_product_id = '".$bc_product_id."', bc_url = '".$product_url."', status = 'yes' WHERE productcode = '".$product_code."'");
	}

	public function UpdateProductDisStatus($product_code) {
		$query_update = $this->db->query("Update ".$this->product_table." set  dis_update_status = 'yes' WHERE productcode = '".$product_code."'");
	}

	public function UpdateProductMessage($product_code, $error) {
		$query_update = $this->db->query("Update ".$this->product_table." set message = '".$error."' WHERE productcode = '".$product_code."'");
	}

	public function getProductVideos($productId) {
		$query = $this->db->query("SELECT * FROM `youtube_video` WHERE `productId` = '".$productId."'");
		return $query->result_array();
	}

	public function getRelatedProdata() {
		$query = $this->db->query("SELECT * FROM `related_product` WHERE `bc_product_id` != '' GROUP by `product_id`");
		return $query->result_array();
	}

	public function getBCProductID($Sku)
	{
		$query = $this->db->query("SELECT `Product_ID` FROM `bc_products` WHERE `Code` = '".$Sku."'");
		$data =  $query->row_array();


		if(isset($data['Product_ID']) && !empty($data['Product_ID']))
		{
			$query_update = $this->db->query("Update `live_products` set bc_product_id = '".$data['Product_ID']."' WHERE sku = '".$Sku."'");
		} 
		// return '';
	}

	function getProductSKU($productsku) {
		$query 		   = $this->db->query("select * from ".$this->product_table." WHERE ischildofproductcode = '".$productsku."'");
		$product_data  = $query->result_array();
		return $product_data;
	}


	public function getRelatedIDs($product_id)
	{
		$query = $this->db->query("SELECT `bc_product_id` FROM `related_product` WHERE `product_id` = '".$product_id."' GROUP by bc_product_id");
		return $query->result_array();
	}

	public function updateProductid($product_id) {
		$query_update = $this->db->query("Update `live_products` set status = 'yes' WHERE bc_product_id = '".$product_id."'");
	}

	public function updateVideostatus($product_id) {
		$query_update = $this->db->query("Update `live_products` set video_status = 'yes' WHERE sku = '".$product_id."'");
	}

	public function updateProductData($bc_product_id,$related_ids) {
		$query_update = $this->db->query("Update `related_product` set bc_product_id = '".$bc_product_id."' WHERE related_ids = '".$related_ids."'");
	}

	public function insertRelatedIDS($product_id,$related_ids) {

		$explode = explode(',', $related_ids);
		$related = array();
		$i = 0;
		foreach ($explode as $related_idss) {
			$related[$i]['product_id'] = $product_id;
			$related[$i]['related_ids'] = trim($related_idss);
		$i++;
		}
	
		if(isset($related) && !empty($related))
		{
			$this->db->insert_batch('related_product',$related);
		}
	}

	

	public function updateMPN($product_id,$mpn)
	{
		$query_update = $this->db->query("Update ".$this->product_table." set mpn = '".$mpn."' WHERE product_id = '".$product_id."'");
	}

	public function updatebrand($product_id,$brand)
	{
		$query_update = $this->db->query("Update ".$this->product_table." set brand = '".$brand."' WHERE product_id = '".$product_id."'");
	}

	public function getProductOption($name,$option,$bc_product_id)
	{
		$query_product_bc_data = $this->db->query("SELECT * FROM option_values WHERE option_name = '".$name."' and option_value = '".$option."' and product_id = '".$bc_product_id."'");
		return $query_product_bc_data->row_array();
	}

}
?>