<?php

/* 	 Single Page to display Single Page or Post
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/


get_header(); ?>

<div id="content">

 		  <?php if ( have_posts() ):  while ( have_posts() ) : the_post();  ?>
            <h1 class="page-title"><?php the_title(); ?></h1>
            <?php newspress_author_meta(); ?>
            <div class="content-ver-sep"> </div>
            <div class="entrytext"><?php  the_post_thumbnail('single-page' , array('class' => 'attachment-single-page')); ?>
			<?php newspress_content(); ?>
            </div>
            <div class="clear"> </div>
            <?php newspress_post_meta(); ?><br />
            <?php  wp_link_pages( array( 'before' => '<div class="page-link"><span>' . 'Pages:' . '</span>', 'after' => '</div>' ) ); ?><br />
            <div class="floatleft"><b><?php previous_post_link('&laquo; %link ('. 'Previous News' . ')'); ?></b></div>
			<div class="floatright"><b><?php next_post_link('('. 'Next News' . ') %link &raquo;'); ?></b></div><br />
            <div class="clear"> </div>
            <?php if ( is_attachment() ): ?>
            <div class="floatleft"><?php previous_image_link( false, '&laquo; ' . 'Previous Image' ); ?></div>
			<div class="floatright"><?php next_image_link( false,  'Next Image' . ' &raquo;' ); ?></div> 
			<?php  endif; endwhile; endif; ?><br />
          	            
          <!-- End the Loop. -->          
          <div class="clear"></div><br />

 <!-- Related News -->
 
<?php
$category = get_the_category(); 
$args = array(
	'cat'        	  => $category[0]->cat_ID,
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'post_type'       => 'post',
    'post_status'     => 'publish',
	'ignore_sticky_posts' => 1,
	'posts_per_page'  => 10,
	'suppress_filters' => true );
	
$my_query = new WP_Query($args); if (have_posts()) : $counter =0;  ?>
 <div class="fpage-catspecial">
 <h2 class="fcname"><?php echo'Related News'; ?></h2>
 
 <?php while ( $my_query->have_posts()) :  $my_query->the_post(); $counter++; ?>
 <?php if ($counter == 1 || $counter == 2 ) : ?>
	<div class="special-cat-sub">
	<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail('cat-page', array('class' => 	'fi-full-width-cat')); ?>
	<h3 class="fcpt"><?php the_title(); ?></h3>
	<?php $newspress_excerptlength= '15'; the_excerpt(); ?></a></div><?php else: ?>

	<h4 class="fcpt"><li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li></h4></a>

 <?php endif; endwhile; wp_reset_postdata(); ?>
 </div> <!--end of fpage-catspecial-->
 <?php endif; ?>
 
 
 <div class="content-ver-sep"> </div>
 <?php comments_template(); ?>

            
</div>			
<?php get_sidebar(); ?>
<?php get_footer(); ?>