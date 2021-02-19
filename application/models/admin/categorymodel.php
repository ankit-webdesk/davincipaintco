<?php
class Categorymodel extends CI_Model
{
	public function __construct() {
       	$this->category_table = "categories";
       	$this->setting_table  = "users";
    }
	
	function getGeneralSetting() {
		$query = $this->db->query("select * from ".$this->setting_table."");
		return $query->row_array();
	}

	function getCategory() {
		$query = $this->db->query("select * from ".$this->category_table." WHERE bc_category_id = '' and status = 'no' ORDER BY `parentid` ASC");
		return $query->result_array();
	}

	function getvolusionCategoryDetails($category_id){
		$query = $this->db->query("SELECT * FROM ".$this->category_table." WHERE `categoryid` = '".$category_id."'");
		return $query->row_array();
	}

	function getParentId($parentid){
		$query = $this->db->query("SELECT bc_category_id FROM ".$this->category_table." WHERE `categoryid` = '".$parentid."'");
		return $query->row_array();
	}

	function updateCategorystatus($category_id, $bc_cat_id, $bc_cat_url) {
		$this->db->query("UPDATE ".$this->category_table." SET status = 'yes', bc_category_id = '".$bc_cat_id."', bc_url = '".$bc_cat_url."' WHERE categoryid = '".$category_id."'");
	}
	
	function updateCategoryMessage($category_id, $error) {
		$this->db->query("UPDATE ".$this->category_table." SET message = '".$error."' WHERE categoryid = '".$category_id."'");
	}
}
?>