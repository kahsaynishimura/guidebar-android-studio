<?php
/* 	News Press's Footer Sidebar Area
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/
	
			
	// If we get this far, we have widgets. Let do this.
?>
<div id="footer-sidebar">
	<div class="first-widget"><?php dynamic_sidebar( 'sidebar-3' ); ?></div><!-- #first .widget-area -->
	<div class="widgets"><?php dynamic_sidebar( 'sidebar-4' ); ?></div><!-- #second .widget-area -->
	<div class="widgets"><?php dynamic_sidebar( 'sidebar-5' ); ?></div><!-- #third .widget-area -->
	<div class="widgets"><?php dynamic_sidebar( 'sidebar-6' ); ?></div><!-- #third .widget-area -->
</div><!-- #footerwidget -->

