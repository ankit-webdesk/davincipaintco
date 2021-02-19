<?php
class customermodel extends CI_Model{
	var $table_name	= "";
	function __construct()
	{
		$this->customer_table = "customers";
		$this->setting_table = "users";
		$this->customer_reset_password = "customer_reset_password";
	}

	function getGeneralSetting()
	{
		$query = $this->db->query("select * from ".$this->setting_table."");
		$setting_data  = $query->row_array();
		return $setting_data;
	}
	
	function exitcustomer($customerid) {
		$query = $this->db->query("select * from ".$this->customer_table." WHERE customerid = ".$customerid."");
			
		if ($query->num_rows() > 0)	{
			return 'yes';
		} else {	
			return 'no';
		}
	}

	function getcustomer()
	{
		$query = $this->db->query("select * from ".$this->customer_table." WHERE status = 'no' limit 5000");
		$customer_data  = $query->result_array();
		return $customer_data;
	}
	
	function updatestatus($customer_id)
	{
		$this->db->query("UPDATE ".$this->customer_reset_password." SET status = 'yes' WHERE customer_id = '".$customer_id."'");
	}
	
	function updatecustomerstatus($customer_id,$bc_cust_id,$message)
	{
		$this->db->query("UPDATE ".$this->customer_table." SET status = 'yes',bc_customerid = '".$bc_cust_id."',error_msg = '".$message."' WHERE customerid = '".$customer_id."'");
	}
	
	function updatecustomerMessage($customer_id, $error) {
		$this->db->query("UPDATE ".$this->customer_table." SET error_msg = '".$error."' WHERE customerid = '".$customer_id."'");
	}

	function updateCustoAddMessage($customer_id, $error){
		
		$this->db->query("UPDATE ".$this->customer_table." SET `add_error_msg`= '".$error."' WHERE `customerid`= ".$customer_id."");
	}

	function getcustomerresetpassword()
	{
		$query = $this->db->query("select * from ".$this->customer_reset_password."");
		$customer_data  = $query->result_array();
		return $customer_data;
	}
}
?>