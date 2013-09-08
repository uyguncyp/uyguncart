<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/bootstrap3/css/bootstrap.min.css');?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/bootstrap3/css/bootstrap-glyphicons.css');?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/default/css/style.css');?>">
	</head>
	<body>
		<div id="wrap">
            <div id="header">
                <div class="container header-up">
                    <span class="pull-right">
                        <a href="<?php echo base_url('user/register') ?>">Register</a>
                        <a href="<?php echo base_url('user/login') ?>">Login</a>
                    </span>
                </div>
            </div>
            <div class="navbar navbar-default navbar-static-top">
                <div class="container">
                    <div class="row">
                            <div class="col-lg-3">
                                <a class="navbar-brand" href="<?php echo base_url(); ?>">UygunCart</a>
                            </div>
                            <div class="col-lg-9">
                            <form class="navbar-form pull-left" action="<?php echo base_url('search') ?>" method="get">
                                <input type="text" name="q" class="form-control" id="search" placeholder="Search" value="<?php if(isset($_GET["q"])) echo $_GET["q"]; ?>">
                                <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i></button>
                            </form>
                            <span class="navbar-form pull-right">
                                <a href="<?php echo base_url('cart') ?>" class="btn btn-success">
                                    <span class="glyphicon glyphicon-shopping-cart"></span>
                                    Cart (<?php echo $cart_item_count ?>)
                                </a>
                            </span>
                            </div>
                        </div>
                </div>
            </div>

			<div class="container">