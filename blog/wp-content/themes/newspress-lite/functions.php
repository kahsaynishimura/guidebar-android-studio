<?php
/* 	News Press's Functions
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/
   
// Load the D5 Framework Optios Page
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
	require_once dirname( __FILE__ ) . '/inc/options-framework.php';

// 	Tell WordPress for wp_title in order to modify document title content
	function newspress_filter_wp_title( $title ) {
    $site_name = get_bloginfo( 'name' );
    $filtered_title = $site_name . $title;
    return $filtered_title;
	}
	add_filter( 'wp_title', 'newspress_filter_wp_title' );
	
	function newspress_setup() {
		
//	Set the content width based on the theme's design and stylesheet.
	global $content_width;
	if ( ! isset( $content_width ) ) $content_width = 684;
	
	register_nav_menus( array( 'main-menu' => "Main Menu", 'top-menu' => "Top Menu" ) );

// 	Tell WordPress for the Feed Link
	add_theme_support( 'automatic-feed-links' );


    add_theme_support( 'post-thumbnails' );
	add_image_size( 'post-page', 350, 175, true );
	add_image_size( 'cat-page', 400, 200, true );
	add_image_size( 'single-page', 900, 450, true );
	set_post_thumbnail_size( 350, 175, true );
	
		
// 	WordPress 3.4 Custom Background Support	
	$newspress_custom_background = array(
	'default-color'          => 'FFFFFF',
	'default-image'          => '',
	);
	add_theme_support( 'custom-background', $newspress_custom_background );
	
// 	WordPress 3.4 Custom Header Support				
	$newspress_custom_header = array(
	'default-image'          => get_template_directory_uri() . '/images/logo.png',
	'random-default'         => false,
	'width'                  => 300,
	'height'                 => 90,
	'flex-height'            => false,
	'flex-width'             => false,
	'default-text-color'     => '000000',
	'header-text'            => false,
	'uploads'                => true,
	'wp-head-callback' 		 => '',
	'admin-head-callback'    => '',
	'admin-preview-callback' => '',
	);
	add_theme_support( 'custom-header', $newspress_custom_header );
	}
	add_action( 'after_setup_theme', 'newspress_setup' );

// 	Functions for adding script
	function newspress_enqueue_scripts() {
	wp_enqueue_style('newspress-style', get_stylesheet_uri(), false);	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) { 
		wp_enqueue_script( 'comment-reply' ); 
	}
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'newspress-menu-style', get_template_directory_uri(). '/js/menu.js' );
	wp_register_style ('newspress-gfonts1', '//fonts.googleapis.com/css?family=Oswald:400,300,700', false );
	wp_enqueue_style('newspress-gfonts1' );

	
	if (is_front_page()):
	wp_enqueue_script( 'newspress-main-slider', get_template_directory_uri() . '/js/jquery.fractionslider.min.js' );
	wp_enqueue_style('newspress-main-slider-css', get_template_directory_uri(). '/css/fractionslider.css' );
	endif;
	
	wp_enqueue_style('newspress-responsive', get_template_directory_uri(). '/style-responsive.css' ); 
	}
	add_action( 'wp_enqueue_scripts', 'newspress_enqueue_scripts' );
	
	function newspress_creditline () {
	$newspress_theme_data = wp_get_theme(); $newspress_author_uri = $newspress_theme_data->get( 'AuthorURI' );
	echo '&copy; ' . date("Y"). ': ' . get_bloginfo( 'name' ). '<span class="credit"> | NewsPress Theme by: <a href="'. $newspress_author_uri .'" target="_blank"> D5 Creation</a> | Powered by: <a href="http://wordpress.org" target="_blank">WordPress</a>';
    }

	
// 	Excerpt Length
	function newspress_excerpt_length( $length ) {
	global $newspress_excerptlength;
	if ($newspress_excerptlength) {
    return $newspress_excerptlength;
	} else {
    return 90; //default value
    } }
	add_filter( 'excerpt_length', 'newspress_excerpt_length', 999 );
	
	function newspress_excerpt_more($more) {
       global $post;
	return '<a href="'. get_permalink($post->ID) . '" class="read-more">' . 'Read More' . '</a>';
	}
	add_filter('excerpt_more', 'newspress_excerpt_more');
	
	function newspress_content() {
	if ( is_page() || is_single() ) : the_content('<span class="read-more">' . 'Read More' . '</span>');
	else: the_excerpt();
	endif;	
	}
	
	// 	Post Meta Design
	function newspress_post_meta() { ?>
	<div class="post-meta"><span class="post-edit"> <?php edit_post_link(''); ?></span></span>
	<span class="post-tag"> <?php the_tags('<span class="post-tag-icon"></span>', ', '); ?> </span><span class="post-category"> <?php the_category(', '); ?> </span> <span class="post-comments"> <?php comments_popup_link('No Comments' . ' &#187;', 'One Comment' . ' &#187;', '% ' . 'Comments' . ' &#187;', ' &#187;' . 'commentsbox',  'Comments are Off'); ?></span>
	</div> 
	
	<?php
	}
	
	// 	Post Author and Date Design
	function newspress_author_meta() {
	$archive_year  = get_the_time('Y'); 
	$archive_month = get_the_time('m'); 
	$archive_day   = get_the_time('d'); 
	?>
	<div class="post-author"><span class="post-author"><?php the_author_posts_link(); ?> | </span><span class="post-date"><a href="<?php echo get_day_link( $archive_year, $archive_month, $archive_day); ?>"><?php the_time('F j, Y'); ?></a></span></div> 
	<?php
	}

//	News Page Navigation
	function newspress_page_nav() { ?>
	<div id="page-nav">
    <div class="alignleft"><?php previous_posts_link('&laquo;  ' . 'Newer News' ) ?></div>
	<div class="alignright"><?php next_posts_link('Older News' .' &raquo;') ?></div>
	</div>
	<?php }

	
//	404 Error Content
	function newspress_404() { ?>
	<h1 class="arc-post-title page-404"><?php echo 'Sorry, we could not find anything that matched your search.'; ?></h1>
		<h3 class="arc-src"><span><?php echo 'You Can Try Another Search...'; ?></span></h3>
		<?php get_search_form(); ?>
		<p><a href="<?php echo home_url(); ?>" title="Browse the Home Page">&laquo; <?php echo 'Or Return to the Home Page'; ?></a></p><br />
	<?php }


//	Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link
	function newspress_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
	}
	add_filter( 'wp_page_menu_args', 'newspress_page_menu_args' );
	
// 	Functions for adding some custom code within the head tag of site
	function newspress_custom_code() {
?>
	
	<style type="text/css">
	.site-title a, 
	.site-title a:active, 
	.site-title a:hover { color: #<?php echo get_header_textcolor(); ?>; }
	</style>
	
<?php 
	}
	
	add_action('wp_head', 'newspress_custom_code');
	
	
//	Registers the Widgets and Sidebars for the site
	function newspress_widgets_init() {
		
	register_sidebar( array(
		'name' =>  'Front Page Sidebar', 
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' =>  'News Page Sidebar', 
		'id' => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' =>  'Footer Area One', 
		'id' => 'sidebar-3',
		'description' =>  'An optional widget area for your site footer', 
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' =>  'Footer Area Two', 
		'id' => 'sidebar-4',
		'description' =>  'An optional widget area for your site footer', 
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' =>  'Footer Area Three', 
		'id' => 'sidebar-5',
		'description' =>  'An optional widget area for your site footer', 
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
		'name' =>  'Footer Area Four', 
		'id' => 'sidebar-6',
		'description' =>  'An optional widget area for your site footer', 
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
			
	}
	add_action( 'widgets_init', 'newspress_widgets_init' );
	
// 	When the post has no post title, but is required to link to the single-page post view.
	add_filter('the_title', 'newspress_title');
	function newspress_title($title) {
        if ( '' == $title ) {
            return '(Untitled)';
        } else { return $title; } 
    }


	