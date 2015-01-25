<?php 
/* 	News Press's Category Page
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/

get_header(); ?>

<div id="content" class="arc-content">

<h1 class="arc-post-title"><?php single_cat_title(); ?></h1>
	<?php if(trim(category_description()) != "<br />" && trim(category_description()) != ''): ?>
		<div class="post-meta"><?php echo category_description(); ?></div><br />
    <?php endif; 

 if (have_posts()) : ?>
 <div class="fsubhcontainer">
 <?php 	$newspress_counterc =0; while (have_posts()) : the_post(); $counter++; ?>
 <?php if ( $newspress_counterc == 2 ): echo '<div class="clear"></div>'; $newspress_counterc = 1;  else: $newspress_counterc++;  endif; ?>
 <div class="fsubheading" >
 <div <?php post_class(); ?> id="post-<?php the_ID(); ?>"> 
 <a href="<?php the_permalink(); ?>">
 
 <h2 class="post-title"><?php the_title();?></h2>
 <div class="entrytext"><?php the_post_thumbnail('post-page');  $newspress_excerptlength= '30';  the_excerpt(); ?></div></a>
 <div class="clear"> </div>
 </div></div>
 
 <?php endwhile; newspress_page_nav(); ?>
 </div>
 <?php else: newspress_404();  endif; ?>
 
</div><!--close content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
