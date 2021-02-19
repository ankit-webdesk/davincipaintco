<?php
class commonmodel extends CI_Model
{
	function __construct()
	{
		$this->setting_table = "users";
	}
	function getsettingdata($id)
	{
		$query = $this->db->get_where($this->setting_table,array('id'=>$id));
		return $query->row_array();
	}
}
?>