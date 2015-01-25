<?php
/**
 * The template for displaying search forms in Aldehyde
 *
 * @package Aldehyde
 */
?>
<form role="search" method="get" class="row search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<button type="submit" class="btn btn-default search-submit"><i class="icon-search"> </i></button>
	<label>
		<span class="screen-reader-text"><?php _ex( 'Search for:', 'label', 'aldehyde' ); ?></span>
		<input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Search for anything on this site...', 'placeholder', 'aldehyde' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
</form>
