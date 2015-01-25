<?php 
/* 	News Press's Index Page to hsow Blog Posts
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/

get_header(); ?>
<div id="content">
<?php if (have_posts()) : while (have_posts()) : the_post();?>
<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
 <h3 class="subtitle"><?php echo get_post_meta($post->ID, 'np_subtitle', 'true'); ?></h3>
 <h2 class="post-title <?php if (get_post_meta( $post->ID, 'np_vih', true ) == 'on' ): echo 'vi-heading'; endif; ?> "><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>
 <?php newspress_author_meta(); ?>
 <div class="content-ver-sep"> </div>
 <div class="entrytext"><?php the_post_thumbnail(); ?>
  <?php $newspress_excerptlength= '90'; newspress_content(); ?>
 <div class="clear"> </div>
 <?php newspress_post_meta(); ?>
 </div></div>
 
 <?php endwhile; newspress_page_nav(); ?>
 
 <?php else: 
 newspress_404(); 
 endif; ?>

</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>