<?php 
/* 	 Search Page
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/

get_header(); ?>
<div id="content" class="arc-content">
	<?php if (have_posts()) : ?>
       <h1 class="arc-post-title"><?php echo 'Search Results'; ?></h1>
		
		<?php $counter = 0; global $more; $more = 0; ?>
		
		<?php while (have_posts()) : the_post();
			if($counter == 0) {
				$numposts = $wp_query->found_posts; // count # of search results ?>
				<h3 class="arc-src"><span><?php echo 'Search Term'; ?>: </span><?php the_search_query(); ?></h3>
				<h3 class="arc-src"><span><?php echo 'Number of Results'; ?>: </span><?php echo $numposts; ?></h3><br />
				<?php } //endif ?>
			
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
 			
 			<h2 class="post-title <?php if (get_post_meta( $post->ID, 'np_vih', true ) == 'on' ): echo 'vi-heading'; endif; ?> "><a href="<?php the_permalink(); ?>"><?php the_title		();?></a></h2>
			<?php newspress_author_meta(); ?>
 			<div class="content-ver-sep"> </div>
 			<div class="entrytext"><?php the_post_thumbnail(); ?>
  			<?php $newspress_excerptlength= '90';  newspress_content(); ?>
 			<div class="clear"> </div>
 			<?php newspress_post_meta(); ?>
 			</div></div>
				
		<?php $counter++; ?>
 		
		<?php endwhile; newspress_page_nav(); ?>
        
		  
		<?php else:  newspress_404();  endif; ?>


</div><!--close content-->   


<?php get_sidebar(); ?>

<?php get_footer(); ?>