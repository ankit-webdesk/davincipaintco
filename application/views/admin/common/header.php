<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>

<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
<link href="<?php echo $this->config->base_url();?>assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />

<link href="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">	
	
<link href="<?php echo $this->config->base_url();?>assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url();?>assets/global/css/components-rounded.min.css" rel="stylesheet" id="style_components" type="text/css" />

<script src="<?php echo $this->config->base_url();?>assets/global/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/scripts/custom/ajaxupload.3.5.js"></script>
</head>

<script>
$(document).ready(function(){
   $(".group2").colorbox({rel:'group2', transition:"fade"});
});
</script>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            <!-- BEGIN HEADER -->
            <div class="page-header navbar navbar-fixed-top">
                <!-- BEGIN HEADER INNER -->
                <div class="page-header-inner ">
                    <!-- BEGIN LOGO -->
                    <div class="page-logo">
                         <?php 
						$query = $this->db->get_where('users',array('id'=>1));
						$result_query = $query->row_array();
						if(isset($result_query['logo_image']) && !empty($result_query['logo_image'])){?>
							<a  href="<?php echo $this->config->base_url();?>index.php/admin/dashboard">
								<img  class="logo-default" src="<?php echo $this->config->base_url();?>application/uploads/sitelogo/thumb300/<?php echo $result_query['logo_image'];?>" alt="<?php echo $result_query['storename'];?>" class="img-responsive" width="60px"/>
							</a>
						<?php } ?>		
                        <div class="menu-toggler sidebar-toggler">
                            <span></span>
                        </div>
                    </div>
                    <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                        <span></span>
                    </a>
					<div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
							<li class="dropdown dropdown-user">
								<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                	<img alt="user logo" class="img-circle" src="<?php echo $this->config->base_url();?>assets/img/no_user_photo-v1.gif"/>
									<span class="username username-hide-on-mobile"> Admin </span>
									<i class="fa fa-angle-down"></i>
								</a>
								<ul class="dropdown-menu dropdown-menu-default">
									<li>
										<a href="<?php echo $this->config->site_url();?>/admin/login/logout">
											<i class="fa fa-key"></i> <?php echo $this->lang->line('LOGOUT');?>
										</a>
									</li>
								</ul>
							</li>
						</ul>
		
					</div>
			  </div>
		</div>
	</div>

