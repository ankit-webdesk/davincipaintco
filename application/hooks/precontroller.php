<?php
// pre hook call all controller
class MY_Hooks extends CI_Controller {
    function index() {
		//set language in all controller
		$this->lang->load('defines', 'english');
	}
}  
?>