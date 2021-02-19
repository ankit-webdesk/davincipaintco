<?php
class Settingmodel extends CI_Model{
	var $table_name	= "";
	function __construct()
	{
		$this->setting_table = "users";
	}
	function getsettingdata($id)
	{
		$query = $this->db->get_where($this->setting_table,array('id'=>$id));
		return $query->row_array();
	}
	
	function delete_images($image)
	{
		$uploaddir = FCPATH.'application/uploads/sitelogo/'; 
		@unlink($uploaddir.'original/'.$image);
		@unlink($uploaddir.'thumb400/'.$image);
		@unlink($uploaddir.'thumb300/'.$image);
		@unlink($uploaddir.'thumb200/'.$image);
		@unlink($uploaddir.'thumb100/'.$image);
		@unlink($uploaddir.'thumb50/'.$image);
		
		$query = $this->db->query("select id from ".$this->setting_table." WHERE logo_image  ='".$image."'");
		$product_id  = $query->row_array();
		$data= array(
			"logo_image "=> ''
		);
		$this->db->where('id',$product_id['id']);
		$this->db->update($this->setting_table, $data);
		
	}
	
	function update_record()
	{
		$currentdate = date('Y-m-d H:i:s');
		$password = $this->input->post("Password");		
		if(isset($password) && !empty($password))
		{
			$data = array(
				"storename" => $this->input->post("StoreName"),
				"storeurl" => $this->input->post("storeurl"),
				"username" => $this->input->post("UserName"),
				"password"=> md5($this->input->post("Password")),
				"email" => $this->input->post("emailId"),
				"apiusername" => $this->input->post("ApiuserName"),
				"apipath" => $this->input->post("ApiPath"),
				"apitoken" => $this->input->post("ApiToken"),
				"client_id" => $this->input->post("client_id"),
				"storehash" => $this->input->post("storehash"),
				"client_secret" => $this->input->post("client_secret"),
				"storeurl_volusion" => $this->input->post("storeurl_volusion"),
				"login_email" => $this->input->post("login_email"),
				"encryptedpassword" => $this->input->post("encryptedpassword")
				
			);
		}
		else
		{
			$data = array(
				"storename" => $this->input->post("StoreName"),
				"storeurl" => $this->input->post("storeurl"),
				"username" => $this->input->post("UserName"),
				"email" => $this->input->post("emailId"),
				"apiusername" => $this->input->post("ApiuserName"),
				"apipath" => $this->input->post("ApiPath"),
				"apitoken" => $this->input->post("ApiToken"),
				"client_id" => $this->input->post("client_id"),
				"storehash" => $this->input->post("storehash"),
				"client_secret" => $this->input->post("client_secret"),
				"storeurl_volusion" => $this->input->post("storeurl_volusion"),
				"login_email" => $this->input->post("login_email"),
				"encryptedpassword" => $this->input->post("encryptedpassword")
				
			);
		}
		$this->db->where('id', '1');
	    $this->db->update($this->setting_table, $data);
		
		$bannerimages = $this->input->post("banner_images");
		if(isset($bannerimages) && !empty($bannerimages))
		{
			foreach($bannerimages as $banner_image)
			{
				$this->db->query("update ".$this->setting_table." set logo_image ='".$banner_image."' where id ='1'");
			}
		}
		$id = '1';
		return $id;
	}

}
?>