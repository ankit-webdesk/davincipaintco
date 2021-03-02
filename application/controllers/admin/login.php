<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {

	function __construct()
	{
	   parent::__construct();
		$this->load->model("admin/commonmodel");
	}

	public function index()
	{ 
	
		$logo_data = $this->commonmodel->getsettingdata(1);
		
		$this->data["image_logo"] = $logo_data['logo_image'];
		$this->data["store_name"] = $logo_data['storename'];
 		$this->data["page_title"] = $this->lang->line('LOGIN_TITLE');
		$session_data = $this->session->userdata('admin_session');
		if(isset($session_data) && !empty($session_data))
		{
			redirect('admin/dashboard');
		}
		$this->data['errmsg']='';

		if($this->input->post('username') && $this->input->post('password'))
		{
			// Check USER AND PASSWORD VALID OR NOT
			$query = $this->db->query("SELECT id,username from users where username='".$this->input->post('username')."' and password='".md5($this->input->post('password'))."'");
			if ($query->num_rows() > 0)
			{
				   $row = $query->row_array();
				   //SET SESSION
				   $this->session->set_userdata('admin_session',$row['id']);
				   $this->session->set_userdata('admin_username',$row['username']);
				   // $this->session->set_userdata('firstname',$row['firstname']);
				   redirect('admin/dashboard');
			}
			else
			{
				$data['errmsg']='Invalid username or password';
			}
		}
		$this->load->view('admin/login.php',$this->data);
	}
	
	// Verify Username And Password
	public function verify()
	{ 
		$logo_data = $this->commonmodel->getsettingdata(1);
		$this->data["image_logo"] = $logo_data['logo_image'];
		$this->data["store_name"] = $logo_data['storename'];
 		$this->data["page_title"] = $this->lang->line('LOGIN_TITLE');
		$this->data['errmsg']='';
		if($this->input->post('username') && $this->input->post('password'))
		{
			$query = $this->db->query("SELECT id,username from users where username='".$this->input->post('username')."' and password='".md5($this->input->post('password'))."'");
			if ($query->num_rows() > 0)
			{
			   $row = $query->row_array();
			   //SET SESSION
			   $this->session->set_userdata('admin_session',$row['id']);
			   $this->session->set_userdata('admin_username',$row['username']);
			   // $this->session->set_userdata('firstname',$row['firstname']);
			   redirect('admin/dashboard');
			}
			else
			{
			  $this->data['errmsg']='Invalid username or password';
			}
		}
		$this->load->view('admin/login.php',$this->data);
	}
	function logout()
	{
	   $this->session->unset_userdata('admin_session');
	   $this->session->sess_destroy();
	   redirect('admin/login', 'refresh');
	}
}
?>