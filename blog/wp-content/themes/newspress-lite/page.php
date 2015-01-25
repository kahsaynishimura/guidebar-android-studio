<?php
/* 	News Press's General Page to display all Pages
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/

 get_header(); ?>

	<div id="content">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		<h1 class="page-title"><?php the_title(); ?></h1>
			<div class="content-ver-sep"> </div>
            <div class="entrytext">
				<?php the_post_thumbnail('single-page'); ?>
 				<?php newspress_content(); ?>
				<?php  wp_link_pages( array( 'before' => '<div class="page-link"><span>' .  'Pages:' . '</span>', 'after' => '</div>' ) ); ?><br />
			</div>
		</div>
		<?php endwhile; ?><div class="clear"> </div>
	<?php edit_post_link('', '<p>', '</p>'); ?>
	<?php comments_template(); ?>
	<?php else: ?>
		<p>Sorry, no pages matched your criteria.</p>
	<?php endif; ?>
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>