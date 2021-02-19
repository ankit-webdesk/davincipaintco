<?php
class Dashboardmodel extends CI_Model
{
	function __construct()
	{
		$this->product_table = "products";
	}
	function getrecentproduct(){
		$query_tot_product = $this->db->query("SELECT * FROM ".$this->product_table."");
		$total_product = $query_tot_product->num_rows();
		return $total_product;
	}
}
?>