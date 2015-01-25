<?php
/**
 * NewsPress Options Page
 * @ Copyright: D5 Creation, www.d5creation.com
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet
	$themename = 'newspress';
	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'newspresslite'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {
	
// General Options	
	$options[] = array(
		'name' => 'NewsPress Options', 
		'type' => 'heading');
		
	$newspress_theme_data = wp_get_theme(); 
	$newspress_author_uri = $newspress_theme_data->get( 'AuthorURI' );
	$newspress_theme_uri = $newspress_theme_data->get( 'ThemeURI' );
	$newspress_author_uri_clean = parse_url($newspress_author_uri, PHP_URL_HOST);
		
	$options[] = array(
		'desc' => '<div class="infohead"><span class="donation">If you like this FREE Theme You can consider for a small Donation to us. Your Donation will be spent for the Disadvantaged Children and Students. You can visit our <a href="'.$newspress_author_uri.'donate/" target="_blank"><strong>DONATION PAGE</strong></a> and Take your decision.</span><br /><br /><span class="donation"> We appreciate an <a href="http://wordpress.org/support/view/theme-reviews/newspress-lite" target="_blank">Honest Review</a> of this Theme if you Love our Work.</span><br /> 
		<span class="donation">Need More Features and Options including Unlimited Advertisements, Slides, News Items, Galleries, Links and 100+ Advanced Features? Try <a href="'.$newspress_theme_uri.'" target="_blank"><strong>NewsPress Extend</strong></a>.</span><br /> <br /><span class="donation"> You can Visit the NewsPress Extend Demo <a href="http://demo.'.$newspress_author_uri_clean.'/wp/themes/newspress/" target="_blank"><strong>Here</strong></a>.</span><a href="'.$newspress_theme_uri.'" target="_blank" class="extendlink"> </a></div>',
		'type' => 'info');
		
	$options[] = array(
		'name' => 'Set News Style Front Page without considering the WP Reading Settings', 
		'desc' => 'If you select This Options the WordPress Settings > Reading will not be considered and the News Style Front Page will be displayed. This is recommended for News Sites.', 
		'id' => 'fpostex',
		'std' => '0',
		'type' => 'checkbox');

	$numslinks = 5;
	foreach (range(1, $numslinks ) as $numslinksn) {
		
	$options[] = array(
		'name' => 'Social Link - '. $numslinksn, 
		'desc' => 'Input Your Social Page Link. Example: <b>http://profiles.wordpress.org/d5creation/</b>.  If you do not want to show anything here leave the box blank. This Version supports only WordPress, Dribbble, Github, Tumblr, YouTube, Flickr, Vimeo, Instagram, Codepen and LinkedIn  ', 
		'id' => 'sl' . $numslinksn,
		'std' => '#',
		'type' => 'text');	
	}
	
		
	$options[] = array(
		'name' => 'Image: Left of Logo', 
		'desc' => 'Upload/Select an Image. Recommended Size: 250px X 90px', 
		'id' => 'adv03',
		'std' => get_template_directory_uri() . '/images/ad3.png',
		'type' => 'upload' );
		
	$options[] = array(
		'name' => 'Image: Right of Logo', 
		'desc' => 'Upload/Select an Image. Recommended Size: 250px X 90px', 
		'id' => 'adv04',
		'std' =>  get_template_directory_uri() . '/images/ad3.png',
		'type' => 'upload' );
		

	return $options;
}

/*
 * This is an example of how to add custom scripts to the options panel.
 * This example shows/hides an option when a checkbox is clicked.
 */

add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');

function optionsframework_custom_scripts() { ?>

<?php
}
