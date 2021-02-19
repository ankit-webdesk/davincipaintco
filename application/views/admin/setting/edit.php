
<div class="page-container">
<?php echo $left_nav;?>
<div class="page-content-wrapper">
	<div class="page-content">
		<div class="row">
			<div class="col-md-12">				
				<ul class="page-breadcrumb breadcrumb">
					<li>
						<i class="fa fa-home"></i>
						<a href="<?php echo $this->config->site_url();?>dashboard">
							<?php echo $this->lang->line('HOME');?>
						</a>						
					</li>					
					<li> <?php echo $this->lang->line('GENERAL_SETTING_MENU');?> </li>
				</ul>
				<!-- END PAGE TITLE & BREADCRUMB-->
			</div>
		</div>
		
		<div class="portlet box green">
			<div class="portlet-title">
				<div class="caption">
					<?php echo $this->lang->line('GENERAL_SETTING_MENU');?>
				</div>
			</div>
			<div class="portlet-body form">
				<!-- BEGIN FORM-->
				<form action="" id="form_sample_2" class="form-horizontal" method="post" enctype="multipart/form-data">
					<div class="form-body">
						<div class="alert alert-danger display-hide">
							<button class="close" data-close="alert"></button>
							<?php echo $this->lang->line('FORM_ERROR');?>
						</div>
						<?php if($success == 1): ?>
						<div class="alert alert-success">
							<button class="close" data-dismiss="alert">×</button>
							<strong><?php echo $this->lang->line('GENERAL_SUCC');?></strong>
						</div>
						<?php endif; ?>

						<h3 class="form-section">Volusion Setting</h3>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">Store URL</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["storeurl_volusion"] ?>" id="storeurl_volusion" name="storeurl_volusion" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">Login Email</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["login_email"] ?>" id="login_email" name="login_email" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">Encrypted Password</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["encryptedpassword"] ?>" id="encryptedpassword" name="encryptedpassword" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							
						<h3 class="form-section">BigCommerce Setting</h3>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">Store Name</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["storename"] ?>" id="StoreName" name="StoreName" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">User Name</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["username"] ?>" id="UserName" name="UserName" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">Password</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="" autocomplete="off" id="Password" name="Password" type="password" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3"><?php echo $this->lang->line('EMAIL_ADDRESS');?><span class="required"> *</span></label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["email"] ?>" id="emailId" name="emailId" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">API User Name</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["apiusername"] ?>" id="ApiuserName" name="ApiuserName" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">API Path</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["apipath"] ?>" id="ApiPath" name="ApiPath" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">API Token</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["apitoken"] ?>" id="ApiToken" name="ApiToken" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">Store URL</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["storeurl"] ?>" id="storeurl" name="storeurl" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">Store Hash</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["storehash"] ?>" id="storehash" name="storehash" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">Client ID</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["client_id"] ?>" id="client_id" name="client_id" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-md-3">Client Secret</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa"></i>
												<input value="<?php echo $settingdata["client_secret"] ?>" id="client_secret" name="client_secret" type="text" class="form-control">
											</div>	
										</div>
									</div>
								</div>
							</div>
						<!--/row-->
						<h3 class="form-section">Logo</h3>
						<div class="form-group">
							<label class="control-label col-md-3">Store Logo</label>
							<div class="col-md-9">
								<button id="banner_img" class="btn">Upload</button>
								<span id="status"></span>
								<span class="imagelist" id="files">
									<?php if(isset($settingdata['logo_image']) && !empty($settingdata['logo_image'])){?>
									<div class="image_maindiv" id="<?php echo $settingdata['logo_image']; ?>" style="display:block">
										<a href="<?php echo $this->config->base_url().'application/uploads/sitelogo/original/'.$settingdata["logo_image"]; ?>" class="group2">
											<img  src='<?php echo $this->config->base_url()?>application/uploads/sitelogo/thumb200/<?php echo $settingdata['logo_image']?>' border="0" alt="<?php echo $settingdata['logo_image'];?>" class="group2">
											<a class="btn red delete image_removediv"onclick="removeimage('<?php echo $settingdata['logo_image']; ?>')" ><i class="fa fa-trash"></i> <span> Delete</span></a>
										</a>
									</div>
									<?php } ?>
								</span>
								<span class="help-block"><?php echo $this->lang->line('SITE_LOGO_HINT');?></span>
							</div>	
						</div>
					</div>
					
					<div class="form-actions fluid">
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-offset-3 col-md-9">
									<input type="submit" class="btn green" value="<?php echo $this->lang->line('SAVE');?>">
									<button onclick="window.location='<?php echo $this->config->site_url();?>/admin/dashboard'" type="button" class="btn default"><?php echo $this->lang->line('CANCEL');?></button>
								</div>
							</div>
							<div class="col-md-6">
							</div>
						</div>
					</div>
				</form>
				<!-- END FORM-->
			</div>
		</div>
	</div>
						
	</div>
	</div>
	<!-- END CONTENT -->
</div>
<script>
var SettingValidation = function () {
	var handleValidation2 = function() {
       form2.validate({
			messages: { // custom messages for radio buttons and checkboxes
                    companyName: {
                        required: "<?php echo $this->lang->line('FIELD_REQ');?>",
						minlength: "<?php echo $this->lang->line('NAME_INVALID');?>"
                    },
                    emailId: {
                        required: "<?php echo $this->lang->line('FIELD_REQ');?>",
                        email: jQuery.format("Please enter valid email")
                    },
					phoneNo: {
                        required: "<?php echo $this->lang->line('FIELD_REQ');?>"
                    },
					 address: {
                        required: "<?php echo $this->lang->line('FIELD_REQ');?>",
                        minlength: jQuery.format("<?php echo $this->lang->line('ADDRESS_INVALID');?>")
                    }
                }
			});
    }

}();
</script>

<div id="steps1"></div>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function() { 
	var btnUpload=$('#banner_img');
	var status=$('#status');
	new AjaxUpload(btnUpload, {
	action: '<?php echo $this->config->site_url();?>/admin/setting/ajaxupload/',
	name: 'uploadfile[]',
	multiple: false,
	onSubmit: function(file, ext)
	{
	
	if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
    status.text('Only JPG, PNG or GIF files are allowed');
	return false;
	}status.html('<img src="<?php echo $this->config->base_url();?>/assets/img/loader.gif">');
	},
	onComplete: function(file, response)
	{
		status.html('');
		status.text('');
		var responseObj = jQuery.parseJSON(response);
		if(responseObj.status=="success")
		{
			var images_data = responseObj.success_data.original;
			
			$.each(images_data,function(index, value ){
				var  imagename = "'"+value.file_name+"'";
				$('#files').html(''); 
				$('<span></span>').appendTo('#files').html('<div class="image_maindiv" id="'+value.file_name+'" style="display:block"><a href="<?php echo $this->config->base_url().'application/uploads/sitelogo/original/';?>'+value.file_name+'" class="group2 cboxElement"><img src="<?php echo $this->config->base_url().'application/uploads/sitelogo/thumb200/'; ?>'+value.file_name+'" alt=""  /><a class="image_removediv" onclick="removeimage('+imagename+')" ><span><i class="fa fa-trash-o"></i></span></a></a><input type="hidden" name="banner_images[]" value="'+value.file_name+'" />').addClass('success');
			});
			
		}
		else
		{
			$('<span></span>').appendTo('#files').text(response.error_data).addClass('error');
		}
	}});
});

function removeimage(str)
{
  var status=$('#status');
  status.html('<img src="<?php echo $this->config->base_url();?>/assets/img/loader.gif">');
  if (window.XMLHttpRequest)
  {
  xmlhttp=new XMLHttpRequest();
  }
  else
  {
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function()
  {
	 if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
		 status.html('');
		 document.getElementById(str).style.display="none";
	}
  }
  var url="<?php echo $this->config->site_url();?>/admin/setting/ajaxdelete";
  url=url+"?imgname="+str;
  xmlhttp.open("GET",url,true);
  xmlhttp.send();
  return false;

}
</script>