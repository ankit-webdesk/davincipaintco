<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<?php 
$page_title  = 'Prussianmotors Dashboard';
if(isset($store_name) && !empty($store_name)){
	$page_title = ucwords($store_name);	
} ?>
<title><?php echo $page_title;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url()?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url()?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url()?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url()?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url()?>assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url()?>assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url()?>assets/global/css/components-rounded.min.css" rel="stylesheet" id="style_components" type="text/css" />
<link href="<?php echo $this->config->base_url()?>assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->config->base_url()?>assets/pages/css/login.min.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" /> </head>
</head>
<body class="login">
<div class="logo">
<?php if(isset($image_logo) && !empty($image_logo)){?>
	<a href="<?php echo $this->config->base_url()?>index.php/admin/login">
		<img src="<?php echo $this->config->base_url()?>application/uploads/sitelogo/thumb200/<?php echo $image_logo;?>" alt="<?php echo $store_name;?>"/>
	</a>
<?php }?>
</div>
<div class="content">
	<form class="login-form" id="loginform" action="<?php echo $this->config->site_url()?>/admin/login/verify" method="post">
		<h3 class="form-title">Login to your account</h3>
		<div id="errmsg" class="alert alert-danger display-hide"  <?php if($errmsg!='')echo 'style="display: block;"'; ?>>
			<button class="close" data-close="alert"></button>
			<span>
				 <?php if($errmsg =='') { ?>
				 Enter any username and password.
				 <?php }else{ 
				 echo $errmsg;
				 } ?>
			</span>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Username</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>
			</div>
		</div>
		 <div class="form-actions" style="text-align: center;">
			<button type="submit" class="btn green uppercase">Login</button>
		</div>
    </form>
</div>
<style>
.login .logo {
	margin: 28px auto 0 !important;
	padding: 15px;
	text-align: center;
}
</style>
<script src="<?php echo $this->config->base_url()?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/global/scripts/app.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url()?>assets/scripts/custom/login.js" type="text/javascript"></script>
<script>
jQuery(document).ready(function() {     
  App.init();
  Login.init();
});
</script>
</body>
</html>