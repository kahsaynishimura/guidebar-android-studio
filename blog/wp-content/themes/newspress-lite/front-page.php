<?php
/*
	Template Name: Front Page
	NewsPress Theme's Front Page to Display the Home Page if Selected
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/
?>

<?php get_header();  

if (esc_html(of_get_option('fpostex', '0')) == '1' || 'posts' != get_option( 'show_on_front' ) ):

?>

<div id="content" class="frnt-page">

<?php 

 $newspress_args = array(
 	'type'            => 'post',
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'post_status'     => 'publish',
	'ignore_sticky_posts'=> 1,
	'posts_per_page'  => 9,
	'suppress_filters' => true );
 
 $newspress_query = new WP_Query($newspress_args); $newspress_counter = 0;
 if ($newspress_query->have_posts()) : 
 while ( $newspress_query->have_posts()) : $newspress_query->the_post(); $newspress_counter++; 
 
 if ( $newspress_counter == 1 || $newspress_counter == 2 ): ?>
 <!-- Start of Slide Container -->  
 <?php if ( $newspress_counter == 1 ): ?>
 <div id="slide-container">
 	<div class="slider-wrapper">
    	<div class="slider">
        	<div class="fs_loader"></div>
 <?php endif; ?>
			<div class="slide" >
            
				<?php if ( has_post_thumbnail() ):  $newspress_thumburl = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'single-page'); endif; ?>
				<img class="slide-full" src="<?php if (!empty($newspress_thumburl)): echo $newspress_thumburl['0']; endif; ?>" data-position="0,0" data-in="fade" data-step="0" data-out="fade"/>
 				<div class="slide-des" data-position="200,20" data-in="right" data-step="1" data-out="fade" >
					<?php $newspress_excerptlength='20'; the_excerpt(); ?>
                </div>
 				<a class="slide-title" href="<?php echo the_permalink(); ?>" data-position="120,20" data-in="left" data-step="1" data-out="fade">
                	<h2><?php echo the_title(); ?></h2>
                </a> 
 				<p data-position="270,20" data-in="bottom" data-step="2" data-out="fade" >
                	<a href="<?php echo the_permalink(); ?>" class="read-more cat-read-more">
						<?php echo 'Read More'; ?>
                    </a>
                    <span class="rarrow"> </span>
                </p>
  
			</div>
 <?php if ( $newspress_counter == 2 ): ?>
		</div>
	</div>
 </div> 
 <br /><div class="content-ver-sep"> </div>
 <?php endif; ?>
 <!--  End of Slide Container --> 
 
<?php elseif ( $newspress_counter == 3 ): ?>

<!-- Heading  -->
 <div class="fpheading">
 	<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
 		<a href="<?php the_permalink(); ?>">
 			<h1 class="page-title"><?php the_title();?></h1> 
 			<div class="entrytext"><?php the_post_thumbnail('single-page' , array('class' => 'attachment-single-page')); $newspress_excerptlength= '100'; the_excerpt(); ?>
 			</div>
        </a>
    </div>
 </div>
 <!--  End of Heading --> 

<?php else: ?> 
<!-- Sub Heading  -->
 <?php if ( $newspress_counter == 4 ): ?>
 <div class="fsubhcontainer">
 <?php endif; ?>
 	<div class="fsubheading">
 		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>"> 
 			<a href="<?php the_permalink(); ?>">
 				<h2 class="post-title"><?php the_title();?></h2>
 				<div class="entrytext"><?php the_post_thumbnail('post-page'); $newspress_excerptlength= '30'; the_excerpt(); ?></div>
           	</a>
 		</div>
    </div>
    <?php if ( $newspress_counter == 5 || $newspress_counter == 7  ): ?><div class="clear"> </div><?php endif; ?>
 <?php if ( $newspress_counter == 9 ): ?>
 </div>
 <?php endif; ?>
 <?php endif; ?>
 <!--  End of Sub Heading -->
 
 <?php endwhile; endif; ?>
 
 </div>  <!-- End of Contents -->
 
 <?php get_sidebar('frontpage'); ?>
 <div class="content-ver-sep"> </div> 
 
 
 <!-- Categories -->
 
<?php 
$newspress_cat_args = array(
	'type'                     => 'post',
	'child_of'                 => '',
	'parent'                   => '',
	'orderby'                  => 'slug',
	'order'                    => 'ASC',
	'hide_empty'               => 1,
	'hierarchical'             => 1,
	'depth'					   => 1,
	'walker' 				   => 'object',
	'exclude'                  => '',
	'include'                  => '',
	'number'                   => '',
	'taxonomy'                 => 'category',
	'pad_counts'               => false );

echo '<div class="fccontainer">';
$newspress_cats = get_categories($newspress_cat_args);  $newspress_countercc = 0;
foreach ($newspress_cats as $newspress_cat) :
if ($newspress_cat->category_parent == 0 ) {

if ( $newspress_countercc == 4 ): echo '<div class="clear-cat"></div>'; $newspress_countercc = 1;  else: $newspress_countercc++;  endif; 

echo '<div class="fpage-cat" >'; 

 $newspress_args = array(
'orderby'         => 'post_date',
'order'           => 'DESC', 
'cat'             => $newspress_cat->term_id
);

 $newspress_query = new WP_Query( $newspress_args); 

	 if ($newspress_query->have_posts()) : $newspress_counter = 0;
	 
	 echo '<a href="'.get_category_link($newspress_cat->cat_ID).'" target="_blank"><h2 class="fcname">' . $newspress_cat->name . '</h2></a>';
	 
	 while (  $newspress_query->have_posts()) :   $newspress_query->the_post(); $newspress_counter++;  ?>	
	
<?php if ($newspress_counter == 1 ) : ?>
<a href="<?php the_permalink() ?>" ><?php the_post_thumbnail('cat-page', array('class' => 'fi-full-width-cat')); ?>
<h3 class="fcpt"><?php the_title(); ?></h3>
<?php $newspress_excerptlength= '15'; the_excerpt(); ?> </a> <?php else: ?>
<h4 class="fcpt"><li><a href="<?php the_permalink() ?>" ><?php the_title(); ?></a></li></h4></a>
<?php endif; endwhile; ?>
	<a class="read-more cat-read-more" href="<?php echo get_category_link($newspress_cat->cat_ID); ?>" target="_blank"><?php echo 'Read All'; ?></a><span class="rarrow"> </span>
 	<?php else : 
		echo '<h2>No Posts for '.$newspress_cat->name.' Category</h2>';				
	 endif; 
	 
	 wp_reset_postdata(); 
	
echo '</div> <!--end of fpage-cat-->';
}
endforeach; wp_reset_postdata(); ?>
</div><div class="clear"></div>

<?php endif; if (esc_html(of_get_option('fpostex', '0')) != '1' ): get_template_part( 'front-page-content' ); endif; ?>

<?php get_footer(); ?>

