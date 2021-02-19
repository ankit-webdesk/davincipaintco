<div class="page-sidebar-wrapper">
	 <div class="page-sidebar navbar-collapse collapse">
		<!-- BEGIN SIDEBAR MENU -->
		 <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 0px">
			<!---=============== MENU PART START ================--->
			<!---=============== DASHBOARD ================--->
			<li class="nav-item start <?php if($this->router->class =='dashboard')echo 'active';?>">
				<a class="nav-link" href="<?php echo $this->config->site_url();?>/admin/dashboard">
					<i class="icon-home"></i>
					<span class="title"> <?php echo $this->lang->line('DASHBOARD_MENU');?> </span>
				</a>
			</li>
			<li class="nav-item start <?php if($this->router->class =='category')echo 'active';?>">
				<a class="nav-link" href="<?php echo $this->config->site_url();?>/admin/category">
					<i class="fa fa-cube"></i> 
					<span class="title"> Categories </span>
				</a>
			</li>
			<li class="nav-item start <?php if($this->router->class =='product')echo 'active';?>">
				<a class="nav-link" href="<?php echo $this->config->site_url();?>/admin/product">
					<i class="fa fa-th"></i> 
					<span class="title"> Products </span>
				</a>
			</li>
			<li class="nav-item start <?php if($this->router->class =='customer')echo 'active';?>">
				<a class="nav-link" href="<?php echo $this->config->site_url();?>/admin/customer">
					<i class="icon-users"></i>
					<span class="title"> Customers </span>
				</a>
			</li>			
			<li class="nav-item start <?php if($this->router->class =='setting')echo 'active';?>">
				<a class="nav-link" href="<?php echo $this->config->site_url();?>/admin/setting">
					<i class="icon-settings"></i>
					<span class="title">Setting</span>
				</a>
			</li>
			<!---=============== MENU PART END ================--->
		</ul>
		<!-- END SIDEBAR MENU -->
	</div>
</div>