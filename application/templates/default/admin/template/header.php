<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo $title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/templates/default/admin/css/bootstrap-responsive.min.css');?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/templates/default/admin/css/bootstrap.min.css');?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/templates/default/admin/css/style.css');?>">
	</head>
	<body>
		<div id="wrap">
			<div class="navbar navbar-inverse navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container-fluid">
						<a class="brand" href="<?php echo base_url('admin/home'); ?>">UygunCart</a>
						<ul class="nav">
							<li class="<?php if(isset($menu_active)&&$menu_active=="home")echo 'active'; ?>"><a href="<?php echo base_url('admin/home'); ?>">Home</a></li>
							<li class="dropdown <?php if(isset($menu_active)&&$menu_active=="catalog")echo 'active'; ?>">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Catalog <i class="caret"></i></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo base_url('admin/category'); ?>">Categories</a></li>
									<li><a href="<?php echo base_url('admin/product'); ?>">Products</a></li>
									<li><a href="<?php echo base_url('admin/manufacturer'); ?>">Manufacturers</a></li>
								</ul>
							</li>
							<li class="dropdown <?php if(isset($menu_active)&&$menu_active=="sales")echo 'active'; ?>">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Sales <i class="caret"></i></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo base_url('admin/order'); ?>">Order</a></li>
								</ul>
							</li>
							<li class="dropdown <?php if(isset($menu_active)&&$menu_active=="system")echo 'active'; ?>">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">System <i class="caret"></i></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo base_url('admin/category'); ?>">Payment Setting</a></li>
								</ul>
							</li>
						</ul>
						<div class="btn-group pull-right">
							<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
								<i class="icon-user icon-white"></i>
								<?php echo $fullname ?>
								<i class="caret"></i>
							</button>
							<ul class="dropdown-menu">
								<li><a href="<?php echo base_url('admin/user'); ?>">Account</a></li>
								<li class="divider"></li>
								<li> <a href="<?php echo base_url('admin/logout'); ?>">Logout</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="container-fluid" style="padding-top:60px">
