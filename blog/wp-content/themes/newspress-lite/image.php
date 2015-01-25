<?php

/* 	 Single Page to display Image
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/


get_header(); ?>

<div id="content" class="single-image-show">
          
		  <?php if ( have_posts() ): while ( have_posts() ) : the_post();   ?>
            <h1 class="page-title"><?php the_title(); ?></h1>
            <?php newspress_author_meta(); ?>
            <div class="content-ver-sep"> </div>
            <div class="single-page-image">
            <?php echo wp_get_attachment_image( $attachment_id, 'single-page' ); the_excerpt(); ?>
            </div>
            <div class="clear"> </div>
           <div class="floatleft"><?php previous_image_link( false, '&laquo; ' . 'Previous Image' ); ?></div>
			<div class="floatright"><?php next_image_link( false,  'Next Image' . ' &raquo;' ); ?></div> 
            <div class="clear"> </div><br />
            <?php newspress_post_meta(); ?><br />
			<?php endwhile; endif; ?>
          	            
          <!-- End the Loop. -->          
  
 <div class="content-ver-sep"> </div>
 <?php comments_template();?>

            
</div>			
<?php get_sidebar(); ?>
<?php get_footer(); ?>