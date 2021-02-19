<?php 
class Setting extends CI_controller{
	
	function Setting()
	{
		parent::__construct();	
		$this->load->library('upload');
		$this->load->library('image_lib');
		$this->load->model("admin/settingmodel");
	}

	function index()
	{	
		$session_data = $this->session->userdata('admin_session');
		if(!isset($session_data) || empty($session_data))redirect('admin/login');
		
		$this->data["page_head"]= $this->lang->line('SETTING_TITLE');
		$success = 0;
		
		$this->form_validation->set_rules("UserName","Username","required");
		$this->form_validation->set_rules("emailId","Email","required");
		if ($this->form_validation->run() == TRUE)
		{ 
		$update_record = $this->settingmodel->update_record();
		$success = 1;
		}
		$this->data["success"] = $success;
		$this->data["settingdata"] = $this->settingmodel->getsettingdata('1');
		
		$this->data["page_title"] = $this->lang->line('SETTING_TITLE');
		$this->load->view("admin/common/header",$this->data);
		$this->data['left_nav']=$this->load->view('admin/common/leftmenu',$this->data,true);	
		$this->load->view("admin/setting/edit",$this->data);
		$this->load->view("admin/common/footer");
	}
	
	function ajaxdelete()
	{	
		$images =$this->input->get('imgname');
		$this->settingmodel->delete_images($images);
		exit;
	}
	function ajaxupload()
	{
		$uploaddir = FCPATH.'application/uploads/sitelogo/'; 
		$upload_conf = array(
            'upload_path'   => $uploaddir.'original/' ,
            'allowed_types' => 'gif|jpg|png|jpeg',
            'max_size'      => '0',
            'overwrite'     => false,
			'remove_spaces' => true,
			'encrypt_name' => true,
			'file_name' =>time(),
            );
		$this->upload->initialize($upload_conf);
		foreach($_FILES['uploadfile'] as $key=>$val)
        {
            $i = 1;
            foreach($val as $v)
            {
                $field_name = "file_".$i;
                $_FILES[$field_name][$key] = $v;
                $i++;   
            }
        }
        unset($_FILES['uploadfile']);
		$error = array();
        $success = array();
		foreach($_FILES as $field_name => $file)
        {
		    if ( ! $this->upload->do_upload($field_name))
            {
                $error['upload'][] = $this->upload->display_errors();
            }
            else
            {
				$config = array(
					'file_name' => time().$field_name
				);
				$upload_data = $this->upload->data($config);
				$success['original'][] = $upload_data;
				$upload_name = $upload_data['file_name'];
				$image_sizes = array(
					'thumb400' => array(400, 400),
					'thumb300' => array(300, 300),
					'thumb200' => array(200, 200),
					'thumb100' => array(100, 100),
					'thumb50'  => array(50, 50)
				);
				foreach($image_sizes as $key => $resize){
					$config = array(
						'source_image' => $upload_data['full_path'],
						'new_image' => $uploaddir.$key.'/'.$upload_name,
						'maintain_ration' => true,
						'overwrite'     => false,
						'width' => $resize[0],
						'remove_spaces' => true,
						'encrypt_name' => true,
						'height' => $resize[1]
					);
					$this->image_lib->initialize($config);
					if ( ! $this->image_lib->resize())
					{
						$error['resize'][$key][] = $this->image_lib->display_errors();
					}
					$this->image_lib->clear();
				}
			}
        }
        if(count($error) > 0)
        {
            $data['status'] = 'error';
			$data['error_data'] = $error;
        }
        else
        {
			$data['status'] = 'success';
            $data['success_data'] = $success;
        }
		echo json_encode($data);
	}
	
}

?>