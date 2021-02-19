
<div class="footer">
	<div class="footer-inner">
		
	</div>
	<div class="footer-tools">
		<span class="go-top">
			<i class="fa fa-angle-up"></i>
		</span>
	</div>
</div>


<!-- END QUICK NAV -->
<!--[if lt IE 9]>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/respond.min.js"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/excanvas.min.js"></script> 
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/ie8.fix.min.js"></script> 
<![endif]-->
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
		
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/scripts/app.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/jquery.colorbox.js"></script>
<link rel="stylesheet" href="<?php echo $this->config->base_url();?>assets/global/plugins/colorbox.css" /> 

<script src="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
 
<script type="text/javascript" src="<?php echo $this->config->base_url();?>assets/global/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->base_url();?>assets/global/plugins/validation/additional-methods.min.js"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>

<script src="<?php echo $this->config->base_url();?>assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>

<script src="<?php echo $this->config->base_url();?>assets/pages/scripts/ui-modals.min.js" type="text/javascript"></script>

<script src="<?php echo $this->config->base_url();?>assets/pages/scripts/ui-blockui.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/pages/scripts/table-datatables-managed.min.js" type="text/javascript"></script>

<!--<script src="<?php // echo $this->config->base_url();?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script> --> 
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url();?>assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>

<script>

function filtertype(type)
{
	window.location='<?php echo $this->config->site_url();?>/admin/errorlog/type/'+type;
}

function confirm_multiple(module)
{
	var	checkedElements=0;
	var form = document.actionfrm;
	for (var i=0;i<form.elements.length;i++)
	{
		var e = form.elements[i];
		if ((e.type=='checkbox') && (!e.disabled) && e.checked) {
			checkedElements = checkedElements+1;
		}
	}
	var val =checkedElements;
	if(document.getElementById('delete_rec')){
		document.getElementById('delete_rec').value=eval(checkedElements);
	}

	if(val<=0){
		alert("<?php echo $this->lang->line('DELETE_MULTIPLE_SELECT'); ?>");
		return false;
	}else{
		
		if(window.confirm("<?php echo $this->lang->line('DELETE_MULTIPLE_COMFIRM'); ?>"))
		{
			document.actionfrm.action = '<?php echo $this->config->site_url();?>'+module+'/delete';
			document.actionfrm.submit();
		}
		else
		{
			return false;
		}
	}
	return true;
}
function confirm_active(module)
{
	var	checkedElements=0;
	var form = document.actionfrm;
	for (var i=0;i<form.elements.length;i++)
	{
		var e = form.elements[i];
		if ((e.type=='checkbox') && (!e.disabled) && e.checked) {
			checkedElements = checkedElements+1;
		}
	}
	var val =checkedElements;
	if(document.getElementById('delete_rec')){
		document.getElementById('delete_rec').value=eval(checkedElements);
	}

	if(val<=0){
		alert("<?php echo $this->lang->line('DELETE_MULTIPLE_ACTIVE'); ?>");
		return false;
	}else{
		
		if(window.confirm("<?php echo $this->lang->line('STATUS_COMFIRM'); ?>"))
		{
			
			document.actionfrm.action = '<?php echo $this->config->site_url();?>'+module+'/update_status_activeall';
			document.actionfrm.submit();
		}
		else
		{
			return false;
		}
	}
	return true;
}

function confirm_inactive(module)
{
	var	checkedElements=0;
	var form = document.actionfrm;
	for (var i=0;i<form.elements.length;i++)
	{
		var e = form.elements[i];
		if ((e.type=='checkbox') && (!e.disabled) && e.checked) {
			checkedElements = checkedElements+1;
		}
	}
	var val =checkedElements;
	if(document.getElementById('delete_rec')){
		document.getElementById('delete_rec').value=eval(checkedElements);
	}

	if(val<=0){
		alert("<?php echo $this->lang->line('DELETE_MULTIPLE_INACTIVE'); ?>");
		return false;
	}else{
		
		if(window.confirm("<?php echo $this->lang->line('STATUS_COMFIRM'); ?>"))
		{
			
			document.actionfrm.action = '<?php echo $this->config->site_url();?>'+module+'/update_status_inactiveall';
			document.actionfrm.submit();
		}
		else
		{
			return false;
		}
	}
	return true;
}


</script>
<script src="<?php echo $this->config->base_url();?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script>

</body>
<!-- END BODY -->
</html>
