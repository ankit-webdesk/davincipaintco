<div class="page-container">
	<?php echo $left_nav;?>
<div class="page-content-wrapper">
	<div class="page-content">
			<div class="row">
				<div class="col-md-12">
					
					<ul class="page-breadcrumb breadcrumb">
						<li>
							<i class="fa fa-home"></i>
							<a href="<?php echo $this->config->site_url();?>/admin/dashboard">
								<?php echo $this->lang->line('HOME');?>
							</a>
							
						</li>
						<li>
							<?php echo $this->lang->line('DASHBOARD_MENU');?>
						</li>
					</ul>
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>	
			<div class="row" >
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="display: none;">
					<div class="dashboard-stat green">
						<div class="visual">
							<i class="fa fa-shopping-cart"></i>
						</div>
						<div class="details">
							<div class="number">
								<?php 
								echo @$recentproduct;
								?>
							</div>
							<div class="desc">
								Products
							</div>
						</div>
						<a href="<?php echo $this->config->site_url();?>/admin/product" class="more">
							 View more <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="display: none;">
					<div class="dashboard-stat purple">
						<div class="visual">
							<i class="fa fa-download"></i>
						</div>
						<div class="details">
							<div class="number">
								&nbsp;
							</div>
							<div class="desc">
								Single Import 
							</div>
						</div>
						<a href="<?php echo $this->config->site_url();?>/admin/import" class="more">
							 View more <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat blue">
						<div class="visual">
							<i class="fa fa-wrench"></i>
						</div>
						<div class="details">
							<div class="number">
								&nbsp;
							</div>
							<div class="desc">
								Settings 
							</div>
						</div>
						<a href="<?php echo $this->config->site_url();?>/admin/setting/" class="more">
							 View more <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
			 </div>
			
			<div class="col-md-12 col-sm-12" style="display:none">
				<div class="portlet box green">
					<div class="portlet-title">
						<div class="caption">
							<?php echo $this->lang->line('DASHBOARD_RESIENT_TRIVING');?>
						</div>
					</div>
					<div class="portlet-body">
						<div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
							<ul class="feeds">
								<?php 
								
								if(isset($recentproduct) && !empty($recentproduct)){ 
									foreach($recentproduct as $recentpro){?>
									<li>
										<div class="col1">
											<div class="cont">
												<div class="cont-col1">
													<div class="label label-sm label-info">
														<i class="fa fa-check"></i>
													</div>
												</div>
												<div class="cont-col2">
													<div class="desc">
														 <a href="product/edit?id=<?php echo $recentpro->id; ?>"><?php echo $recentpro->product_name;?></a>
													</div>
												</div>
											</div>
										</div>
									</li>
								<?php 
								}
								} ?>	
							</ul>
						</div>
						<div class="scroller-footer">
							<div class="pull-right">
								<a href="product">
									 <?php echo $this->lang->line('SEE_ALL_TRAVEL');?> <i class="m-icon-swapright m-icon-gray"></i>
								</a>
								 &nbsp;
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
<!-- END CONTENT -->
</div>
</div>