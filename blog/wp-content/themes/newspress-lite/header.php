<?php

/* 	News Press's Header
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title() ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php 

wp_head(); ?>

</head>

<body <?php body_class(); ?> >
	
    <div id="site-container">
        
      <div id="top-menu-container">
      <nav id="newspress-top-menu"><?php if ( has_nav_menu( 'top-menu' ) ) :  wp_nav_menu( array( 'theme_location' => 'top-menu' )); endif; ?></nav>
      <?php get_search_form(); ?>  
      
	  <div id="social">
	  <?php $numslinks = of_get_option('nslinks', '5'); foreach (range(1, $numslinks ) as $numslinksn) { 
	  if ( of_get_option('sl' . $numslinksn, '#') != '' ): echo '<a href="'. esc_url(of_get_option('sl' . $numslinksn, '#')) .'" target="_blank"> </a>'; endif;
	  } ?>
      </div>
       </div>
      <div id ="header">
      <div id ="header-content">
      
		<!-- Site Titele and Description Goes Here -->
        <div class="topadlft"><img src="<?php echo esc_url(of_get_option('adv03', get_template_directory_uri() . '/images/ad3.png')); ?>" /></div> 
        
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php if ( get_header_image() !='' ): ?><img class="site-logo" src="<?php header_image(); ?>"/><?php else: ?><h1 class="site-title"><?php echo bloginfo( 'name' ); ?></h1><?php endif; ?></a>
                
        <h1 class="site-title-hidden"><?php echo bloginfo( 'name' ); ?></h1>
		<h2 class="site-title-hidden"><?php echo bloginfo( 'description' ); ?></h2>
        
        <div class="topadrt"><img src="<?php echo esc_url(of_get_option('adv04', get_template_directory_uri() . '/images/ad3.png')); ?>" /></div> 
        
        </div><!-- header-content -->
        <div class="heading-date"><?php echo date("l, F j, Y");  ?></div>  
        </div><!-- header -->    
        <!-- Site Main Menu Goes Here -->
        <nav id="newspress-main-menu">
		<?php if ( has_nav_menu( 'main-menu' ) ) :  wp_nav_menu( array( 'theme_location' => 'main-menu' )); else: wp_page_menu(); endif; ?>
       </nav>
      <div class="clear"> </div>
      <div id="container"> 
      
      
      
      
      
      
	  