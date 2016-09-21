<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=$data[0]['title']?></title>
		<?php EStructure::view("extra/bootstrap_css");?>
	</head>
	<body bgcolor = "white" style="padding-top:10px">
		<?php
		EStructure::view("extra/bootstrap_js");
		?>
		<div class="container">
		<nav class="navbar navbar-default">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Your web application</a>
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="index">Home</a></li>

                    <!--first dropdown menu (Tools)-->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Tools<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li class="dropdown-header">Buy</li>
                          <li><a href="#">Product 1</a></li>
                          <li role="separator" class="divider"></li>
                          <li class="dropdown-header">Some numbers</li>
                          <li><a href="#">Statistics</a></li>
                        </ul>
                    </li>

                    <!--first dropdown menu (Help)-->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Help<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li class="dropdown-header">Getting help</li>
                          <li><a href="#">Documentation</a></li>
                          <li><a href="#">Contacts</a></li>
                          <li role="separator" class="divider"></li>
                          <li class="dropdown-header">The company</li>
                          <li><a href="about">About us</a></li>
                        </ul>
                    </li>
			</ul>
			  </div><!--/.nav-collapse -->
		  </nav>
		  </div>
		<div class="container">
			<div class="row">
				<div class="col-md-8">
