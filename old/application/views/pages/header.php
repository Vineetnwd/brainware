<!doctype html>
<html>
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
	<meta name="keywords" content="">
	<meta name="description" content="">
	
	<title><?php echo ucwords(config('sc_name'));?></title>
	
	<!-- FAVICON AND APPLE TOUCH -->    
	<link rel="shortcut icon" href="<?php echo base_url();?>web/images/favicon.ico">
	
	<!-- FONTS -->
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,700">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Volkhov:400,400italic">
	
	<!-- BOOTSTRAP CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/bootstrap/css/bootstrap.min.css"> 
	
	<!-- FONT AWESOME -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/fonts/fontawesome/css/font-awesome.min.css">
	
	<!-- FANCYBOX -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/fancybox/jquery.fancybox.min.css">
	
	<!-- REVOLUTION SLIDER -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/revolutionslider/css/settings.css">
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/revolutionslider/css/layers.css">
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/revolutionslider/css/navigation.css">
	
	<!-- OWL Carousel -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/owl-carousel/owl.carousel.css">
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/owl-carousel/owl.transitions.css">
	
	<!-- YOUTUBE PLAYER -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/ytplayer/css/jquery.mb.YTPlayer.min.css">
	
	<!-- ANIMATIONS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/animations/animate.min.css">
	
	<!-- CUSTOM & PAGES STYLE -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/custom.css">
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/pages-style.css">
	
	<!-- THEME OPTIONS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/theme-options/theme-options.css">  
	
	<!-- ALTERNATIVE STYLES -->
	<link rel="stylesheet" href="#" data-style="styles">
	

</head>

<body>

	<div id="main-container">
		
		<!-- HEADER -->
		<header>
			<div id="header-top">
			
				<div class="container">
					<div class="row">
						<div class="col-sm-8">
							
							<div class="widget widget-contact">

								<ul>
									<!-- <li>
										<strong>Hello,</strong> this is your startup business.
									</li> -->
									<li class="phone">
										<span class="hidden-xs"><a href='tel:<?php echo str_replace(" ","",config('sc_contact1'));?>'><?php echo config('sc_contact1');?></a></span>
										<a class="visible-xs" href="tel:<?php echo str_replace(" ","",config('sc_contact1'));?>"><?php echo config('sc_contact1');?></a>
									</li>
									<li class="email">
										<a href="mailto:<?php echo config('sc_email');?>"><?php echo config('sc_email');?></a>
									</li>
								</ul>

							</div><!-- widget-contact -->
							
						</div><!-- col -->
						<div class="col-sm-4">
							
							<div class="widget widget-social">

								<div class="social-media">
									<a class="facebook" href="#"><i class="fa fa-facebook"></i></a>
									<a class="twitter" href="#"><i class="fa fa-twitter"></i></a>
									<a class="behance" href="#"><i class="fa fa-youtube"></i></a>
								</div><!-- social-media -->

							</div><!-- widget-social -->
							
						</div><!-- col -->
					</div><!-- row -->
				</div><!-- container -->
			
			</div><!-- header-top -->
			<div id="header">
			
				<div class="container">
					<div class="row">
						<div class="col-sm-3" style='padding-top: 10px;'>
							
							<!-- LOGO -->
							<div id="logos">
								<a href="<?php echo base_url();?>">
									<img src="<?php echo base_url();?>web/images/brainware-logo.png" alt="">
								</a>
							</div><!-- LOGO -->
							
						</div><!-- col -->
						<div class="col-sm-9">
							
							<!-- MENU --> 
							<nav>
							
								<a id="mobile-menu-button" href="#"><i class="fa fa-bars"></i></a>
												 
								<ul class="menu clearfix" id="menu">
									<li class="active"><a href="<?php echo base_url();?>">Home</a></li>
									<li class=""><a href="<?php echo base_url();?>about">About</a></li>
									<li class="dropdown">
										<a href="#">Student Zone</a>
										<ul>
											<li class="dropdown">
												<a href="#">Admission</a>
												<ul>
													<li><a href="registration_verification">Registration Verification</a></li>
													<li><a href="advance_search">Advance Search</a></li>
												</ul>
											</li>
											<li><a href="result">Result</a></li>
											<!-- <li class="dropdown">
												<a href="">Result</a>
												<ul>
													<li><a href="semester1">Semester 1</a></li>
													<li><a href="semester2">Semester 2</a></li>
													<li><a href="semester3">Semester 3</a></li>
													<li><a href="semester4">Semester 4</a></li>
												</ul>
											</li> -->
											<!-- <li><a href="about-us.html">About us</a></li> -->
											<li><a href="certificate">Certificate</a></li>
										</ul>
									</li>
									<li class=""><a href="study_center">Study Center</a></li>
									<li class=""><a href="courses">Courses</a></li>
									<li class="dropdown">
										<a href="#">Gallery</a>
										<ul>	
											<li><a href="web_gallery">Image Gallery</a></li>
											<li><a href="video_gallery">Video Gallery</a></li>
										</ul>
									</li>
									
									<li class="dropdown">
										<a href="#">Login</a>
										<ul>
											<li><a href="admin" target='_blank'>Admin</a></li>
											<li><a href="webmail" target='_blank'>Mail</a></li>
										</ul>
									</li>
									<li class=""><a href="contact">Contact</a></li>
								</ul>
							
							</nav>
						
						</div><!-- col -->
					</div><!-- row -->
				</div><!-- container -->
				
			</div><!-- 	header -->
			<div class='wave-bordered'></div>
		</header><!-- HEADER -->
		