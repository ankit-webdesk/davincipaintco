<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Dashboard extends CI_Controller {

 function __construct()
 {	
	parent::__construct();	
	$this->load->model("admin/dashboardmodel");
 }

 function index()
 {
	 $session_data = $this->session->userdata('admin_session');
     if(!isset($session_data) || empty($session_data))redirect('admin/login');
	 
     $this->data["page_title"] = $this->lang->line('DASBOARD_TITLE');
	 
	 $this->data["username"]   = $this->session->userdata('admin_username');
	 $this->data["firstname"]  = $this->session->userdata('firstname');
	 
	 $this->data["recentproduct"] = $this->dashboardmodel->getrecentproduct();
	 
	 $this->load->view('admin/common/header',$this->data);
	 $this->data['left_nav']=$this->load->view('admin/common/leftmenu',$this->data,true);	
     $this->load->view('admin/dashboard', $this->data);
	 $this->load->view('admin/common/footer',$this->data);
   }
  
 }



?>