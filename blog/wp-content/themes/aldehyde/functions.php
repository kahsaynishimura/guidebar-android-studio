<?php
/**
 * Aldehyde functions and definitions
 *
 * @package Aldehyde
 */
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

/**
 * Initialize Options Panel
 */
if ( !function_exists( 'optionsframework_init' ) ) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
	require_once dirname( __FILE__ ) . '/inc/options-framework.php';
}

if ( ! function_exists( 'aldehyde_setup' ) ) :

function aldehyde_setup() {

	load_theme_textdomain( 'aldehyde', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_image_size('homepage-banner',750,300,true);

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'aldehyde' ),
	) );

	add_theme_support( 'custom-background', apply_filters( 'aldehyde_custom_background_args', array(
		'default-color' => 'eeeeee',
		'default-image' => '',
	) ) );
}
endif; // aldehyde_setup
add_action( 'after_setup_theme', 'aldehyde_setup' );

function aldehyde_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'aldehyde' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'aldehyde_widgets_init' );

add_action('optionsframework_custom_scripts', 'aldehyde_custom_scripts');

function aldehyde_custom_scripts() { ?>

<script type="text/javascript">
jQuery(document).ready(function() {

	jQuery('#example_showhidden').click(function() {
  		jQuery('#section-example_text_hidden').fadeToggle(400);
	});
	
	if (jQuery('#example_showhidden:checked').val() !== undefined) {
		jQuery('#section-example_text_hidden').show();
	}
	
});
</script>
 
<?php
}

function aldehyde_scripts() {
	wp_enqueue_style( 'aldehyde-fonts', '//fonts.googleapis.com/css?family=Open+Sans:400,700,600' );
	wp_enqueue_style( 'aldehyde-basic-style', get_stylesheet_uri() );
	if ( (function_exists( 'of_get_option' )) && (of_get_option('sidebar-layout', true) != 1) ) {
		if (of_get_option('sidebar-layout', true) ==  'right') {
			wp_enqueue_style( 'aldehyde-layout', get_template_directory_uri()."/css/layouts/content-sidebar.css" );
		}
		else {
			wp_enqueue_style( 'aldehyde-layout', get_template_directory_uri()."/css/layouts/sidebar-content.css" );
		}	
	}
	else {
		wp_enqueue_style( 'aldehyde-layout', get_template_directory_uri()."/css/layouts/content-sidebar.css" );
	}
	wp_enqueue_style( 'aldehyde-bootstrap-style', get_template_directory_uri()."/css/bootstrap.min.css", array('aldehyde-layout') );
		
	wp_enqueue_style( 'aldehyde-main-style', get_template_directory_uri()."/css/main.css", array('aldehyde-layout','aldehyde-fonts') );
	
	wp_enqueue_script( 'aldehyde-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'aldehyde-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	if ( (function_exists( 'of_get_option' )) && (of_get_option('slider_enabled') != 0) ) {
		wp_enqueue_style( 'aldehyde-nivo-slider-default-theme', get_template_directory_uri()."/css/nivo/themes/default/default.css" );
	
		wp_enqueue_style( 'aldehyde-nivo-slider-style', get_template_directory_uri()."/css/nivo/nivo.css" );
	}
	
	wp_enqueue_style('aldehyde-nivo-lightbox', get_template_directory_uri()."/css/nivo-lightbox.css" );
	
	wp_enqueue_style( 'aldehyde-nivo-lightbox-default-theme', get_template_directory_uri()."/css/themes/default/default.css" );
	
	wp_enqueue_script('aldehyde-timeago', get_template_directory_uri() . '/js/jquery.timeago.js', array('jquery') );
	
	if ( (function_exists( 'of_get_option' )) && (of_get_option('slider_enabled') != 0) ) {
		wp_enqueue_script( 'aldehyde-nivo-slider', get_template_directory_uri() . '/js/nivo.slider.js', array('jquery') );
	}
	wp_enqueue_script( 'aldehyde-superfish', get_template_directory_uri() . '/js/superfish.js', array('jquery','hoverIntent') );
	
	wp_enqueue_script( 'jquery-effects-bounce' );
	
	wp_enqueue_script( 'aldehyde-mm', get_template_directory_uri() . '/js/mm.js', array('jquery') );
	
	wp_enqueue_script( 'aldehyde-bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery') );
	
	wp_enqueue_script( 'aldehyde-lightbox-js', get_template_directory_uri() . '/js/nivo-lightbox.min.js', array('jquery') );
	
	wp_enqueue_script( 'aldehyde-custom-js', get_template_directory_uri() . '/js/custom.js', array('jquery','aldehyde-nivo-slider','aldehyde-timeago','aldehyde-superfish') );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'aldehyde-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'aldehyde_scripts' );

function aldehyde_custom_head_codes() {
 if ( (function_exists( 'of_get_option' )) && (of_get_option('headcode1', true) != 1) ) {
	echo of_get_option('headcode1', true);
 }
 if ( (function_exists( 'of_get_option' )) && (of_get_option('style2', true) != 1) ) {
	echo "<style>".of_get_option('style2', true)."</style>";
 }
 if ( (function_exists( 'of_get_option' )) && (of_get_option('slider_enabled') != 0) ) {
	echo "<script>jQuery(window).load(function() { jQuery('#slider').nivoSlider({effect:'".of_get_option('trans_effect')."', pauseTime:".of_get_option('pause_time').",animSpeed:'".of_get_option('anim_speed')."',randomStart:'".of_get_option('random_start')."'}); });</script>";
 }
if ( get_header_image() ) : 
 	echo "<style>#parallax-bg { background: url('".get_header_image()."') center top repeat-x; }</style>";
 endif;

}	
add_action('wp_head', 'aldehyde_custom_head_codes');

function aldehyde_nav_menu_args( $args = '' )
{
    $args['container'] = false;
    return $args;
} // function
add_filter( 'wp_page_menu_args', 'aldehyde_nav_menu_args' );

function aldehyde_pagination() {
	global $wp_query;
	$big = 12345678;
	$page_format = paginate_links( array(
	    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	    'format' => '?paged=%#%',
	    'current' => max( 1, get_query_var('paged') ),
	    'total' => $wp_query->max_num_pages,
	    'type'  => 'array'
	) );
	if( is_array($page_format) ) {
	            $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
	            echo '<div class="pagination"><div><ul>';
	            echo '<li><span>'. $paged . ' of ' . $wp_query->max_num_pages .'</span></li>';
	            foreach ( $page_format as $page ) {
	                    echo "<li>$page</li>";
	            }
	           echo '</ul></div></div>';
	 }
}

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates. Import Widgets
 */
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/widgets.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
