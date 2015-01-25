<?php
/**
 * @package Aldehyde
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class("row archive"); ?>>
	
	<div class="featured-thumb col-md-12 col-xs-12">
	<div class="img-meta">
		<div class="img-meta-link meta-icon"><a class='meta-link' href="<?php the_permalink() ?>"><i class="icon-link"></i></a></div>
		<?php if (has_post_thumbnail()) : 
					$thumb_id = get_post_thumbnail_id();
					$thumb_url = wp_get_attachment_image_src($thumb_id,'full', true);
		?>
		<div class="img-meta-img meta-icon"><a class='meta-link meta-link-img' title="<?php the_title(); ?>" href="<?php echo $thumb_url[0] ?>"><i class="icon-picture"></i></a></div>
		<?php endif; ?>
	</div>
	<a href="<?php the_permalink(); ?>">
	<?php if (has_post_thumbnail()) :
		the_post_thumbnail('homepage-banner');	
	endif; 
	?>
	</a>
	</div>
	<div class="article-rest col-md-12">
	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta">
			<?php aldehyde_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
	<?php if ( of_get_option('excerpt1', true) == 0 ) : ?>
		<?php the_content( __( 'Contine reading <span class="meta-nav">&rarr;</span>', 'aldehyde' ) ); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages: ', 'aldehyde' ),
				'after'  => '</div>',
			) );
		else :
			the_excerpt();
		endif;		
		?>
	</div><!-- .entry-content -->
	<?php endif; ?>
	</div>
</article><!-- #post-## -->