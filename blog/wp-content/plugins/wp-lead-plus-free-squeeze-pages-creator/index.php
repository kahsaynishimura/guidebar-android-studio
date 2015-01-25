<?php
	/* Plugin Name: WP Lead Plus Responsive
	 * Plugin URI: http://wpleadplus.com/
	 * Author: Gato Vago
	 * Author URI: http://gatovago.com/
	 * Description: Create your reponsive landing pages, squeeze pages, popup widgets within minutes. Working well on mobile, desktop, tablets
	 * Version: 1.7.4
	 */

	include_once ('settings.php');
	include_once ('activate.php');
	include_once 'code/const.php';


	register_activation_hook(__FILE__, 'vgt_on_plugin_activation');

	function vgt_on_plugin_activation()
	{
		//copy the template to the current activate theme
		copy(plugin_dir_path(__FILE__).'code/vgt_page_template.php', get_template_directory().'/vgt_page_template.php' );
		//do the db things
		vgt_activation_tasks();
	} 