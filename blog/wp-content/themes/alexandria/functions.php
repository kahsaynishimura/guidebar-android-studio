<?php
/**
 * alexandria functions and definitions
 *
 * @package alexandria
 */
 
/* 
 * Loads the Options Panel
 */
define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/admin/' );
require_once dirname( __FILE__ ) . '/admin/options-framework.php';

if ( ! function_exists( 'alexandria_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function alexandria_setup() {

	/**
	 * Set the content width based on the theme's design and stylesheet.
	 */
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 640; /* pixels */
	}
	
	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on alexandria, use a find and replace
	 * to change 'alexandria' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'alexandria', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails on posts and pages
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'alexandriathumb', 450, 300, true );
  	add_image_size( 'alexandriasingle', 1200, 500, true );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'alexandria' ),
	) );

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	/**
	 * Setup the WordPress core custom background feature.
	 */
	 
	  if ( of_get_option('skin_style') && of_get_option('skin_style') !== 'child' ) {
		$custombgargsskin = of_get_option('skin_style');
	  }else {
		$custombgargsskin = 'alexandria';
	  }
	  
	  if ( get_stylesheet_directory() == get_template_directory() ) {
		  $custombgargs = array(
			'default-color' => 'ebeef1',
			'default-image' => get_template_directory_uri() . '/skins/images/'.$custombgargsskin.'/page_bg.png',
			);
			
	  }else {
		  $custombgargs = array(
			'default-image' => get_stylesheet_directory_uri() . '/images/page_bg.png',
			);	  
	  }
	  add_theme_support( 'custom-background', $custombgargs );	 
	  add_editor_style();

	/**
	 * Enable support for Title tag
	 */	  
	  add_theme_support( 'title-tag' );
	  
}
endif; // alexandria_setup
add_action( 'after_setup_theme', 'alexandria_setup' );

if ( ! function_exists( '_wp_render_title_tag' ) ) :
    function alexandria_render_title() {
?>
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php
    }
    add_action( 'wp_head', 'alexandria_render_title' );
endif;

if ( ! function_exists( 'alexandria_widgets_init' ) ) :
/**
 * Register widgetized area and update sidebar with default widgets
 */
function alexandria_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'alexandria' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Left Sidebar', 'alexandria' ),
		'id'            => 'footer-left',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	if( !of_get_option('footer_layout') || of_get_option('footer_layout') == 'one' ) {
	register_sidebar( array(
		'name'          => __( 'Footer Center Sidebar', 'alexandria' ),
		'id'            => 'footer-center',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	}
	register_sidebar( array(
		'name'          => __( 'Footer Right Sidebar', 'alexandria' ),
		'id'            => 'footer-right',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );		
}
endif; // alexandria_widgets_init
add_action( 'widgets_init', 'alexandria_widgets_init' );

if ( ! function_exists( 'alexandria_scripts' ) ) :
/**
 * Enqueue scripts and styles
 */
function alexandria_scripts() {
	
	if ( get_stylesheet_directory() != get_template_directory() ) {
		wp_enqueue_style( 'alexandria-parent-style', get_template_directory_uri().'/style.css' );
	}
	
	wp_enqueue_style( 'alexandria-style', get_stylesheet_uri() );	
	
	if( of_get_option('skin_style') == 'radi' ) {
		wp_enqueue_style( 'alexandria-radi-style', get_template_directory_uri().'/skins/radi.css' );
	}
	
	if( of_get_option('skin_style') == 'green' ) {
		wp_enqueue_style( 'alexandria-green-style', get_template_directory_uri().'/skins/green.css' );
	}	
	
	if( of_get_option('skin_style') == 'purple' ) {
		wp_enqueue_style( 'alexandria-purple-style', get_template_directory_uri().'/skins/purple.css' );
	}
	
	if( of_get_option('skin_style') == 'brown' ) {
		wp_enqueue_style( 'alexandria-brown-style', get_template_directory_uri().'/skins/brown.css' );
	}
	
	if( of_get_option('skin_style') == 'orange' ) {
		wp_enqueue_style( 'alexandria-brown-style', get_template_directory_uri().'/skins/orange.css' );
	}
	
	if( of_get_option('skin_style') == 'yellow' ) {
		wp_enqueue_style( 'alexandria-yellow-style', get_template_directory_uri().'/skins/yellow.css' );
	}	
	
	if( of_get_option('skin_style') == 'aqua' ) {
		wp_enqueue_style( 'alexandria-aqua-style', get_template_directory_uri().'/skins/aqua.css' );
	}	
	
	if( of_get_option('skin_style') == 'grunge' ) {
		wp_enqueue_style( 'alexandria-maroon-style', get_template_directory_uri().'/skins/grunge.css' );
	}				

	if( of_get_option('skin_style') == 'pink' ) {
		wp_enqueue_style( 'alexandria-pink-style', get_template_directory_uri().'/skins/pink.css' );
	}
	
	if( of_get_option('skin_style') == 'ggrun' ) {
		wp_enqueue_style( 'alexandria-ggrun-style', get_template_directory_uri().'/skins/ggrun.css' );
	}
	
	if( of_get_option('skin_style') == 'oran' ) {
		wp_enqueue_style( 'alexandria-oran-style', get_template_directory_uri().'/skins/oran.css' );
	}
	
	if( of_get_option('skin_style') == 'ggren' ) {
		wp_enqueue_style( 'alexandria-oran-style', get_template_directory_uri().'/skins/ggren.css' );
	}
	
	if( of_get_option('skin_style') == 'margo' ) {
		wp_enqueue_style( 'alexandria-oran-style', get_template_directory_uri().'/skins/margo.css' );
	}
	
	if( of_get_option('skin_style') == 'marbo' ) {
		wp_enqueue_style( 'alexandria-marbo-style', get_template_directory_uri().'/skins/marbo.css' );
	}	
	
	if( of_get_option('skin_style') == 'ggrey' ) {
		wp_enqueue_style( 'alexandria-ggrey-style', get_template_directory_uri().'/skins/ggrey.css' );
	}	
	
	if( of_get_option('skin_style') == 'grebr' ) {
		wp_enqueue_style( 'alexandria-ggrey-style', get_template_directory_uri().'/skins/grebr.css' );
	}
	
	if( of_get_option('skin_style') == 'brne' ) {
		wp_enqueue_style( 'alexandria-brne-style', get_template_directory_uri().'/skins/brne.css' );
	}
	
	if( of_get_option('skin_style') == 'alge' ) {
		wp_enqueue_style( 'alexandria-alge-style', get_template_directory_uri().'/skins/alge.css' );
	}
	
	if( of_get_option('skin_style') == 'raft' ) {
		wp_enqueue_style( 'alexandria-raft-style', get_template_directory_uri().'/skins/raft.css' );
	}	
	
	if( of_get_option('skin_style') == 'brwne' ) {
		wp_enqueue_style( 'alexandria-brwne-style', get_template_directory_uri().'/skins/brwne.css' );
	}		
	
	global $wp_styles;
	$wp_styles->add('alexandria_ie9', get_template_directory_uri().'/fixed.css');
	$wp_styles->add_data('alexandria_ie9', 'conditional', 'lt IE 9');
	$wp_styles->add('alexandria_ie8', get_template_directory_uri().'/ie.css');
	$wp_styles->add_data('alexandria_ie8', 'conditional', 'lt IE 8');	
	$wp_styles->enqueue(array('alexandria_ie9', 'alexandria_ie8'));			
		
	wp_enqueue_script( 'alexandria-tinynav', get_template_directory_uri() . '/js/tinynav.min.js', array('jquery'), false, false );
	
	wp_enqueue_script( 'alexandria-general', get_template_directory_uri() . '/js/general.js', array(), false, true );
	wp_localize_script( 'alexandria-general', 'alexandria_tinynav_header', array( 'header' => __('Menu', 'alexandria' ) ) );

	wp_enqueue_script( 'alexandria-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'alexandria-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
endif; // alexandria_scripts
add_action( 'wp_enqueue_scripts', 'alexandria_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

if ( ! function_exists( 'alexandria_backupmenu' ) ) :
/**
 * Backup menu incase menu isn't set.
 */
function alexandria_backupmenu() {
	 	if ( current_user_can('edit_theme_options') ) {
				echo '<ul id="main-nav" class="menu">
							<li class="menu-item current-menu-item current_page_item menu-item-home">
								<a href="'.get_admin_url().'nav-menus.php">'.__("Select a Menu to appear here in Dashboard->Appearance->Menus ", "alexandria").'</a>
							</li>
		
						</ul>	';
		} else {
				echo '<ul id="main-nav" class="menu">
							<li class="menu-item current-menu-item current_page_item menu-item-home">
								<a href="'.esc_url( get_home_url() ).'">'.__("Home", "alexandria").'</a>
							</li>
		
						</ul>	';			
		}
}
endif; // alexandria_backupmenu