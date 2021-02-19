<?php
class Productmodel extends CI_Model{
	var $table_name	= "";
	function __construct() {

		ini_set('display_errors','On');
		error_reporting(E_ALL);
		
		$this->product_table 		  	   = "vbproducts";
		$this->product_category_table	   = "vbcategory_tree";
		$this->product_category_rel_table  = "vbproduct_category_rel";
		$this->setting_table 		 	   = "setting_v_b";
		$this->product_option_rel_table    = "vbproduct_option_rel";
		$this->product_option_table  	   = "vbproduct_option";
		$this->option_category_table  	   = "vboption_category";
	}
	
	function getProduct() {

		$query 		   = $this->db->query("select * from ".$this->product_table." WHERE update_status = 'no'");
		$product_data  = $query->result_array();
		return $product_data;
	}
	
	function updateotherstatus($bc_product_id){
		//$query = $this->db->query("UPDATE ".$this->product_table." SET other_details_s = 'yes' WHERE bc_product_id = '".$bc_product_id."'");
		$query = $this->db->query("UPDATE ".$this->product_table." SET update_status = 'yes' WHERE bc_product_id = '".$bc_product_id."'");
	}
	
	function Updateproductwrantty($bc_product_id)
	{
		$query = $this->db->query("UPDATE ".$this->product_table." SET ordering_status = 'yes' WHERE bc_product_id = '".$bc_product_id."'");
	}
	
	function UpdateOrderingStatus($bc_product_id)
	{
		$query = $this->db->query("UPDATE ".$this->product_table." SET update_status = 'yes' WHERE bc_product_id = '".$bc_product_id."'");
	}
	
	function Updateproductstock($bc_product_id)
	{
		$query = $this->db->query("UPDATE ".$this->product_table." SET stock_status = 'yes' WHERE bc_product_id = '".$bc_product_id."'");
	}
	
	function getAllProducts()
	{
		$query 		 = $this->db->query("select product_sku,bc_product_id from ".$this->product_table." LIMIT 2000,1000");
		$get_productsku  = $query->result_array();
		return $get_productsku;
	}
	
	function UpdateimageStatus($productcode)
	{
		$query = $this->db->query("UPDATE ".$this->product_table." SET image_set = 'no' WHERE product_sku = '".$productcode."'");
	}
	
	
	function UpdateProductStatusp($product_id,$productcode)
	{
		$query = $this->db->query("UPDATE ".$this->product_table." SET bc_product_id = '".$product_id."', status = 'yes'  WHERE  product_sku = '".$productcode."'");
	}
	
	function UpdateOptionID($bc_option_id,$option_cat_id)
	{
		$query = $this->db->query("UPDATE ".$this->option_category_table." SET bc_option_id = '".$bc_option_id."' WHERE id = '".$option_cat_id."'");
	}
	
	function UpdateOptionValueID($option_id,$option_value_id)
	{
		$query = $this->db->query("UPDATE ".$this->product_option_table." SET bc_option_value_id = '".$option_value_id."' WHERE id = '".$option_id."'");
	}
	
	function UpdateOptionSetID($ProductCode,$option_set_id)
	{
		$query = $this->db->query("UPDATE ".$this->product_table." SET option_set_id = '".$option_set_id."' WHERE product_sku = '".$ProductCode."'");
	}
	
	function getBCproductID($ProductCode)
	{
		$query = $this->db->query("select bc_product_id from ".$this->product_table." WHERE product_sku = '".$ProductCode."'");
		$bc_product_id  = $query->result_array();
		if(isset($bc_product_id[0]['bc_product_id']) && !empty($bc_product_id[0]['bc_product_id']))
		{
			return $bc_product_id[0]['bc_product_id'];
		}
		return '';
		
	}
	
	function getProductOptionssetID($ProductCode)
	{
		$query = $this->db->query("select option_set_id from ".$this->product_table." WHERE product_sku = '".$ProductCode."'");
		$option_set_id  = $query->result_array();
		if(isset($option_set_id[0]['option_set_id']) && !empty($option_set_id[0]['option_set_id']))
		{
			return $option_set_id[0]['option_set_id'];
		}
		return '';
		
	}
	
	function getOptiondetails($option_id)
	{
		$query = $this->db->query("select po.*,po.id as opid,opc.*,opc.id as opcid from ".$this->product_option_table." as po,".$this->option_category_table." as opc WHERE opc.id = po.optioncatid AND po.id = '".$option_id."'");
		$option_details  = $query->result_array();
		if(isset($option_details[0]) && !empty($option_details[0]))
		{
			return  $option_details[0];
		}
		return '';
	}
	
	function getProductOptions($productcode)
	{
		$query = $this->db->query("select optionids from ".$this->product_option_rel_table." WHERE productcode = '".$productcode."'");
		$product_option  = $query->result_array();
		if(isset($product_option[0]['optionids']) && !empty($product_option[0]['optionids'])){
			$options = explode(',',$product_option[0]['optionids']);
			return $options;
		}
		return '';
	}
	
	function UpdatecategoryIDtoBCID()
	{
		$query = $this->db->query("select * from ".$this->product_category_rel_table." GROUP BY category_id");
		$category_data  = $query->result_array();
		
		foreach($category_data as $category_data_s)
		{
			$query = $this->db->query("select bc_category_id from ".$this->product_category_table." WHERE category_id = '".$category_data_s['category_id']."'");
			$bcproduct_id_data  = $query->result_array();
			
			$query = $this->db->query("UPDATE ".$this->product_category_rel_table." SET bc_category_id = '".$bcproduct_id_data[0]['bc_category_id']."' WHERE category_id = '".$category_data_s['category_id']."'");
			
		}
		
	}
	
	
	
	function getvolusionCategory($productSKU)
	{
		$query = $this->db->query("select bc_category_id from ".$this->product_category_rel_table." WHERE product_sku = '".$productSKU."'");
		$product_cat_data  = $query->result_array();
		
		if(isset($product_cat_data) && !empty($product_cat_data))
		{
			$product_cat = array();
			foreach($product_cat_data as $product_cat_data_s)
			{
				$product_cat[] = $product_cat_data_s['bc_category_id'];
			}
			return $product_cat;
		}
		return '';
	}
		
	function getGeneralSetting() {

		$query = $this->db->query("select * from ".$this->setting_table."");
		return $query->row_array();
	}
	
	function getvolusionCategoryDetails($category_id)
	{
		$query = $this->db->query("select * from ".$this->product_category_table." WHERE category_id = '".$category_id."'");
		$category_data  = $query->result_array();
		return $category_data[0];
	}
	
	function storeCategoryID($bc_category_id,$category_id)
	{
		$query = $this->db->query("UPDATE ".$this->product_category_table." SET bc_category_id = '".$bc_category_id."' WHERE category_id = '".$category_id."'");
	}
	
	
}
?>