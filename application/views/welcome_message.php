<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="<?php echo $this->data['lang'] ?>">
	<head>
		<!-- Meta Tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- Title -->
		<title><?php echo $this->data['meta_title'] ?></title>
                <meta name="description" content="<?php echo $this->data['meta_description']; ?>" />
                <meta name="author" content="<?php echo $this->data['meta_author']; ?>" />
                <meta name="keywords" content="<?php echo $this->data['meta_keywords']; ?>" />
		
		<!-- Google Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Great+Vibes' rel='stylesheet' type='text/css'>
		
		<!-- Favicon -->
		<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
		
		<!-- Stylesheets -->
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="css/fontello.css" rel="stylesheet" type="text/css">
		<link href="css/flexslider.css" rel="stylesheet" type="text/css">
		<link href="js/revolution-slider/css/settings.css" rel="stylesheet" type="text/css" media="screen" />
		<link href="css/owl.carousel.css" rel="stylesheet" type="text/css">
		<link href="css/responsive-calendar.css" rel="stylesheet" type="text/css">
		<link href="css/chosen.css" rel="stylesheet" type="text/css">
		<link href="jackbox/css/jackbox.min.css" rel="stylesheet" type="text/css" />
		<link href="css/cloud-zoom.css" rel="stylesheet" type="text/css" />
		<link href="css/colorpicker.css" rel="stylesheet" type="text/css">
		<link href="css/style.css" rel="stylesheet" type="text/css">

		<!--[if IE 9]>
		<link rel="stylesheet" href="css/ie9.css">
		<![endif]-->
		
		<!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<link href="css/jackbox-ie8.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="css/ie.css">
                <![endif]-->
		
		<!--[if gt IE 8]>
		<link href="css/jackbox-ie9.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		
		<!--[if IE 7]>
		<link rel="stylesheet" href="css/fontello-ie7.css">
		<![endif]-->
		
		<style type="text/css">
			.no-fouc {display:none;}
	  	</style>
		
		<!-- jQuery -->
		<script src="js/jquery-1.11.0.min.js"></script>
		<script src="js/jquery-ui-1.10.4.min.js"></script>
		
		<!-- Preloader -->
		<script src="js/jquery.queryloader2.min.js"></script>
		
		<script type="text/javascript">
		$('html').addClass('no-fouc');
		
		$(document).ready(function(){
			
			$('html').show();
			
			var window_w = $(window).width();
			var window_h = $(window).height();
			var window_s = $(window).scrollTop();
			
			$("body").queryLoader2({
				backgroundColor: '#f2f4f9',
				barColor: '#63b2f5',
				barHeight: 4,
				percentage:false,
				deepSearch:true,
				minimumTime:1000,
				onComplete: function(){
					
					$('.animate-onscroll').filter(function(index){
					
						return this.offsetTop < (window_s + window_h);
						
					}).each(function(index, value){
						
						var el = $(this);
						var el_y = $(this).offset().top;
						
						if((window_s) > el_y){
							$(el).addClass('animated fadeInDown').removeClass('animate-onscroll');
							setTimeout(function(){
								$(el).css('opacity','1').removeClass('animated fadeInDown');
							},2000);
						}
						
					});
					
				}
			});
			
		});
		</script>
		
	</head>
	
	<body class="sticky-header-on tablet-sticky-header <?php if(isset($this->data['page-layout'])) { if($this->data['page-layout'] == 'boxed') echo 'boxed-layout'; }?>">
	
		
		<!-- Container -->
		<div class="container">
			
			
			<!-- Header -->
			<header id="header" class="animate-onscroll">
				
				<!-- Main Header -->
				<div id="main-header">
					
					<div class="container">
					
					<div class="row">
						
						
						
						<!-- Logo -->
						<div id="logo" class="col-lg-3 col-md-3 col-sm-3">
							
							<a href="/"><img src="img/logo.png" alt="Logo"></a>
							
						</div>
						<!-- /Logo -->
						
						
						
						<!-- Main Quote -->
						<div class="col-lg-5 col-md-4 col-sm-4">
													<!-- Main Quote

							<blockquote>Citate</blockquote>
													<!-- Main Quote -->

						</div>
						<!-- /Main Quote -->
						
						
						
						<!-- Newsletter -->
						<div class="col-lg-4 col-md-5 col-sm-5">
							
							
						</div>
						<!-- /Newsletter -->
						
						
						
					</div>
					
					</div>
					
				</div>
				<!-- /Main Header -->
				
				
				
				
				
				<!-- Lower Header -->
				<div id="lower-header">
					
					<div class="container">
					
					<div id="menu-button">
						<div>
						<span></span>
						<span></span>
						<span></span>
						</div>
						<span>Menu</span>
					</div>
					
					<ul id="navigation">
						
						<!-- Home -->
						<li class="home-button <?php if($this->data['current-menu-item'] == 'Acasa') echo 'current-menu-item'; ?>">
						
							<a href="/"><i class="icons icon-home"></i></a>
							
							
							
						</li>
						<!-- /Home -->
						
						<!-- Pages -->
						<li <?php if($this->data['current-menu-item'] == 'Pages') echo 'class="current-menu-item"'; ?>>
						
							<span>Despre Noi</span>
							
							<ul>
							
								<li><a href="statut.php">Statut</a></li>
								<li><a href="team.php">Echipa</a></li>
								<li><a href="program-politic.php">Program Politic</a></li>
								<li><a href="team.php">Program Electoral</a></li>
								
								
							</ul>
							
						</li>
						<!-- /Pages -->
						
						<!-- Events -->
						<li <?php if($this->data['current-menu-item'] == 'Events') echo 'class="current-menu-item"'; ?>>
						
							<span>Știri</span>
							
							
							
						</li>
						<!-- /Events -->
						
						<!-- Media -->
						<li <?php if($this->data['current-menu-item'] == 'Media') echo 'class="current-menu-item"'; ?>>
						
							<a href="donatii.php">Donații</a>
							
							
							
						</li>
						<!-- /Media -->
						
						
						
						<!-- Get Involved -->
						<li <?php if($this->data['current-menu-item'] == 'Get Involved') echo 'class="current-menu-item"'; ?>>
							<a href="cotizatii.php">Cotizații</a>
						</li>
						<!-- /Get Involved -->
						
						
						
						<!-- Features -->
						<li <?php if($this->data['current-menu-item'] == 'Features') echo 'class="current-menu-item"'; ?>>
						
							<span>Presă</span>
							
							<ul>
							
								<li><a href="features-typography.php">Comunicate de Presă</a></li>
								<li><a href="features-shortcodes.php">Galerie</a></li>
								
							</ul>
							
						</li>
						<!-- /Features -->
						
						
						
						
						
						<!-- Shop -->
						<li <?php if($this->data['current-menu-item'] == 'Organizații Locale') echo 'class="current-menu-item"'; ?>>
						
							<a href="organizatii-locale.php">Organizații Locale</a>
						
						</li>
						<!-- /Shop -->

						<!-- Blog -->
						<li <?php if($this->data['current-menu-item'] == 'Blog') echo 'class="current-menu-item"'; ?>>
						
							<span><?php echo $this->lang->line('contact_us') ?></span>
							
						
							
						</li>
						<!-- /Blog -->
						
						<!-- Donate -->
						<li class="donate-button <?php if($this->data['current-menu-item'] == 'Donate Today') echo 'current-menu-item'; ?>">
							<a href="#">Înscrie-te !</a>
						</li>
						<!-- /Donate -->
						
						
						
					</ul>
					
					</div>
					
				</div>
				<!-- /Lower Header -->
				
				
			</header>
			<!-- /Header -->





		<section id="content">
			
			<!-- Section -->
			<section class="section full-width-bg">
				
				<div class="row">
				
					<div class="col-lg-12 col-md-12 col-sm-12">
						
						<!-- Revolution Slider -->
						<div class="tp-banner-container main-revolution">
						
							<span class="Apple-tab-span"></span>
 
							<div class="tp-banner">
								
								<ul>
									<li data-transition="papercut" data-slotamount="7">
										<img src="img/slide1.jpg" alt="">
										<div class="tp-caption"  data-x="100" data-y="115" data-speed="700" data-start="1000" data-easing="easeOutBack"><h2>Unity<br>Liberty<br>Solidarity</h2></div>
										<div class="tp-caption"  data-x="100" data-y="310" data-speed="500" data-start="1200" data-easing="easeOutBack"><a href="#" class="button big">Find out more</a></div>
									</li>
									
									<li data-transition="papercut" data-slotamount="7">
										<img src="img/slide2.jpg" alt="">
										<div class="tp-caption align-center" data-x="center" data-y="135" data-speed="700" data-start="1000" data-easing="easeOutBack"><h4 class="great-vibes">it’s time for changes</h4></div>
										<div class="tp-caption align-center" data-x="center" data-y="220" data-speed="500" data-start="1200" data-easing="easeOutBack"><h2>Building Our Future Together!</h2></div>
										<div class="tp-caption align-center" data-x="center" data-y="300" data-speed="300" data-start="1400"><a href="#" class="button big button-arrow">Get Involved</a></div>
									</li>
									
									<li data-transition="papercut" data-slotamount="7">
										<img src="img/slide3.jpg" alt="">
										<div class="tp-caption align-right" data-x="right" data-hoffset="-100" data-y="150" data-speed="700" data-start="1000" data-easing="easeOutBack"><h2>10 YEARS OF EXPERIENCE</h2></div>
										<div class="tp-caption align-right" data-x="right" data-hoffset="-100" data-y="225" data-speed="500" data-start="1200" data-easing="easeOutBack"><p>Mauris fermentum dictum magna. Sed laoreet aliquam leo. Ut tellus dolor,<br> dapibus eget, elementum vel, cursus eleifend, elit. </p></div>
										<div class="tp-caption align-right" data-x="right" data-hoffset="-100" data-y="305" data-speed="300" data-start="1400"><a href="#" class="button big button-arrow">More Info</a></div>
									</li>
								</ul>
								
							</div>
						 
						</div>
						<!-- /Revolution Slider -->
						
					</div>
					
					
					<div class="col-lg-12 col-md-12 col-sm-12">
						
						<div class="banners-inline">
						
							<?php 
							
							/* Banners */
							include('includes/sidebar_items/banners.php'); 
							
							
							?>
							
						</div>
						
					</div>
				
				</div>
				
			</section>
			<!-- /Section -->
			
			
			
			
			<!-- Section -->
			<section class="section full-width-bg gray-bg">
				
				<div class="row">
				
					<div class="col-lg-9 col-md-9 col-sm-8">
						
						<h3 class="animate-onscroll no-margin-top">Știri recente</h3>
						
						<?php
						
						/* Blog Item Big */
						include('includes/blog_items/blog-item-big.php');
						
						?>
						
					</div>
					
					
					
					<!-- Sidebar -->
					<div class="col-lg-3 col-md-3 col-sm-4 sidebar">
						
						<?php
						
						/* Upcoming Events */
						include('includes/sidebar_items/upcoming-events.php');
						
						?>
						
					</div>
					<!-- /Sidebar -->
					
				</div>
				
				
				
				<div class="row no-margin-bottom">
				
					
					<div class="col-lg-12 col-md-12 col-sm-12">
						
						<?php
						
						/* Blog Items Carousel */
						$candidate['owl-carousel-items'] = 4; // Number of items
						include('includes/blog_items/blog-items-carousel.php');
						
						?>
						
					</div>
					
					
				
				</div>
				
				
				
				
				<div class="row">
				
					<div class="col-lg-9 col-md-9 col-sm-8">
						
						<?php
						
						/* Banner Rotator */
						include('includes/content/banner-rotator.php');
						
						?>
						
					</div>
					
					
					
					<!-- Sidebar -->
					<div class="col-lg-3 col-md-3 col-sm-4 sidebar">
						
						<?php
						
						$image_banner['image'] = 'img/main-issues-big.jpg';
						
						/* Image Banner */
						include('includes/sidebar_items/image-banner.php');
						
						?>
						
					</div>
					<!-- /Sidebar -->
					
				</div>
				
				
				
			</section>
			<!-- /Section -->
		
		</section>


			
			<!-- Footer -->
			<footer id="footer">
				
				<!-- Main Footer -->
				<div id="main-footer">
					
					<div class="row">
						
						<div class="col-lg-3 col-md-3 col-sm-6 animate-onscroll">
							
							<h4>Implică-te !</h4>
							
							<p>Ideile, planurile, speranțele și dorințele tale prind contur in cadrul proiectului NOUA ROMÂNIE !  </p>
							<p> Ia atitudine, implică-te în singurul proiect din România care nu acceptă traseiști politici !</p>
							<p><strong>Decide tu pentru tine !</strong></p>
							
						</div>
						
						<div class="col-lg-3 col-md-3 col-sm-6 animate-onscroll">
							
							<h4>Informații utile </h4>
								
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 menu-container">
								
								<ul class="menu">
									<li><a href="#">Cine suntem !</a></li>
									<li><a href="#">Președinte</a></li>
									<li><a href="#">Știri</a></li>
									<li><a href="#">Semnează !</a></li> 
									<li><a href="#">Donații</a></li>
									<li><a href="#">Înscrie-te !</a></li>
								</ul>
								
							</div>
							
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 menu-container">
								
								<ul class="menu">
									<li><a href="#">Misiune</a></li>
									<li><a href="#">Strategie</a></li>
									<li><a href="#">Comunicate</a></li>
									<li><a href="#">Alegeri</a></li> 
									<li><a href="#">Cotizații</a></li>
									<li><a href="#"><?php echo $this->lang->line('contact_us') ?></a></li>
								</ul>
								
							</div>
							
						</div>
						
						<div class="col-lg-3 col-md-3 col-sm-6 twitter-widget-area animate-onscroll">
							
							<h4></h4>
							
							<div class="twitter-widget">
                                
							</div>
							
							<a href="#" class="button twitter-button">Follow us on twitter</a>
							
						</div>
						
						<div class="col-lg-3 col-md-3 col-sm-6 animate-onscroll">
							
							<h4></h4>
							
							<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpartidulnouaromanie.ro&amp;width=270&amp;height=240&amp;colorscheme=dark&amp;header=false&amp;show_faces=true&amp;stream=false&amp;show_border=false" style="border:none; overflow:hidden; width:100%; height:240px;"></iframe>
							
						</div>
						
					</div>
					
				</div>
				<!-- /Main Footer -->
				
				
				
				
				<!-- Lower Footer -->
				<div id="lower-footer">
					
					<div class="row">
						
						<div class="col-lg-4 col-md-4 col-sm-4 animate-onscroll">
						
							<p class="copyright">© 2016 Partidul Noua Românie. </br> Toate drepturile sunt rezervate.</p>
							
						</div>
						
						<div class="col-lg-8 col-md-8 col-sm-8 animate-onscroll">
							
							<div class="social-media">
								<ul class="social-icons">
									
									<li class="email"><a href="mailto:office@partidulnouaromanie.ro" class="tooltip-ontop" title="Email"><i class="icons icon-mail"></i></a></li>
								</ul>
							
							</div>
							
						</div>
						
					</div>
					
				</div>
				<!-- /Lower Footer -->
				
			
			</footer>
			<!-- /Footer -->
			
			
			
			<!-- Back To Top -->
			<a href="#" id="button-to-top"><i class="icons icon-up-dir"></i></a>
			
			
			
		
		
		</div>
		<!-- /Container -->
	
	
		<!-- JavaScript -->
		
		<!-- Bootstrap -->
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		
		<!-- Modernizr -->
		<script type="text/javascript" src="js/modernizr.js"></script>
		
		<!-- Sliders/Carousels -->
		<script type="text/javascript" src="js/jquery.flexslider-min.js"></script>
		<script type="text/javascript" src="js/owl.carousel.min.js"></script>
		
		<!-- Revolution Slider  -->
		<script type="text/javascript" src="js/revolution-slider/js/jquery.themepunch.plugins.min.js"></script>
		<script type="text/javascript" src="js/revolution-slider/js/jquery.themepunch.revolution.min.js"></script>
		
		<!-- Calendar -->
		<script type="text/javascript" src="js/responsive-calendar.min.js"></script>
		
		<!-- Raty -->
		<script type="text/javascript" src="js/jquery.raty.min.js"></script>
		
		<!-- Chosen -->
		<script type="text/javascript" src="js/chosen.jquery.min.js"></script>
		
		<!-- jFlickrFeed -->
		<script type="text/javascript" src="js/jflickrfeed.min.js"></script>
		
		<!-- InstaFeed -->
		<script type="text/javascript" src="js/instafeed.min.js"></script>
		
		<!-- Twitter -->
		<script type="text/javascript" src="php/twitter/jquery.tweet.js"></script>
		
		<!-- MixItUp -->
		<script type="text/javascript" src="js/jquery.mixitup.js"></script>
		
		<!-- JackBox -->
		<script type="text/javascript" src="jackbox/js/jackbox-packed.min.js"></script>
		
		<!-- CloudZoom -->
		<script type="text/javascript" src="js/zoomsl-3.0.min.js"></script>
		
		<!-- ColorPicker -->
		<script type="text/javascript" src="js/colorpicker.js"></script>
		
		<!-- Main Script -->
		<script type="text/javascript" src="js/script.js"></script>
		
		
		<!--[if lt IE 9]>
			<script type="text/javascript" src="js/jquery.placeholder.js"></script>
			<script type="text/javascript" src="js/script_ie.js"></script>
		<![endif]-->
		
		
	</body>

</html>