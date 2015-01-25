<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package alexandria
 */
if ( ! function_exists( 'alexandria_body_classes' ) ) :
/**
 * Adds custom classes to the array of body classes.
 */
function alexandria_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
endif; // alexandria_body_classes
add_filter( 'body_class', 'alexandria_body_classes' );

if ( ! function_exists( 'alexandria_enhanced_image_navigation' ) ) :
/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 */
function alexandria_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
endif; // alexandria_enhanced_image_navigation
add_filter( 'attachment_link', 'alexandria_enhanced_image_navigation', 10, 2 );

if ( ! function_exists( 'alexandria_wp_title' ) ) :
/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 */
function alexandria_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'alexandria' ), max( $paged, $page ) );

	return $title;
}
endif; // alexandria_wp_title

if ( ! function_exists( '_wp_render_title_tag' ) ) :
add_filter( 'wp_title', 'alexandria_wp_title', 10, 2 );
endif; // alexandria_wp_title