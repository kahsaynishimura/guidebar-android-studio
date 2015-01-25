<?php
/**
Plugin Name: e-goi Mail List Builder
Description: Mail list database populator
Version: 1.0.5
Author: Indot
Author URI: http://indot.pt
Plugin URI: http://indot.pt/egoi-mail-list-builder.zip
License: GPLv2 or later
**/

/**  
	Copyright 2013  Indot  (email : info@indot.pt)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
**/

/**
 * Define some useful constants
**/
define('EGOI_MAIL_LIST_BUILDER_VERSION', '1.0.5');
define('EGOI_MAIL_LIST_BUILDER_DIR', plugin_dir_path(__FILE__));
define('EGOI_MAIL_LIST_BUILDER_URL', plugin_dir_url(__FILE__));
define('EGOI_MAIL_LIST_BUILDER_PLUGIN_KEY', 'ea5199d064c05237745156d5e4b82ef2');
define('EGOI_MAIL_LIST_BUILDER_API_KEY', '0038eca8c4fa8e4e140be9cd8f735d8cad634187');
define('EGOI_MAIL_LIST_BUILDER_XMLRPC_URL', 'http://api.e-goi.com/v2/xmlrpc.php');
define('EGOI_MAIL_LIST_BUILDER_AFFILIATE',' http://bo.e-goi.com/?action=registo&cID=232&aff=267d5afc22');

/**
 * Load files
**/
function egoi_mail_list_builder_activation() {
	set_include_path(EGOI_MAIL_LIST_BUILDER_DIR.'library/'. PATH_SEPARATOR . get_include_path());
	require_once(EGOI_MAIL_LIST_BUILDER_DIR.'includes/class.xmlrpc.php');
	require_once(EGOI_MAIL_LIST_BUILDER_DIR.'library/Zend/XmlRpc/Client.php');
    if(is_admin()) {
        require_once(EGOI_MAIL_LIST_BUILDER_DIR.'includes/admin.php');
	}
	
	require_once(EGOI_MAIL_LIST_BUILDER_DIR.'includes/class.egoi_mail_list_builder.php');
	$EgoiMailListBuilder = get_option('EgoiMailListBuilderObject');
	if($EgoiMailListBuilder) {
		if($EgoiMailListBuilder->isAuthed())	{
			require_once(EGOI_MAIL_LIST_BUILDER_DIR.'egoi-widget.php');
		}
	}

}
egoi_mail_list_builder_activation();

/**
 * Activation, Deactivation and Uninstall Functions
**/
register_activation_hook(__FILE__, 'egoi_mail_list_builder_activation');
register_deactivation_hook(__FILE__, 'egoi_mail_list_builder_deactivation');


/**
 * Filter Registration
**/

add_filter('plugin_action_links', 'egoi_mail_list_builder_settings_link', 10, 2);

/**
 * Add Egoi Mail List Builder Settings link to plugin page
**/
function egoi_mail_list_builder_settings_link($links, $file) {
	static $this_plugin;
	
	if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
	
	if ($file == $this_plugin) {
		$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=egoi-mail-list-builder-info">Settings</a>';
		array_unshift($links, $settings_link);
	}
	
	return $links;
}

function egoi_mail_list_builder_register_scripts() {
    wp_enqueue_style( 'egoi-mail-list-builder-admin-css', EGOI_MAIL_LIST_BUILDER_URL . 'assets/css/admin.css' );
}
add_action( 'admin_enqueue_scripts', 'egoi_mail_list_builder_register_scripts' );


function egoi_mail_list_builder_settings_plugin_link( $links, $file) 
{
	if($file == plugin_basename(EGOI_MAIL_LIST_BUILDER_DIR.'/indot-under.php')){
		$in = '<a href="admin.php?page=egoi-mail-list-builder-info">Settings</a>';
        array_unshift($links, $in);
	}
    return $links;
}
add_filter( 'plugin_action_links', 'egoi_mail_list_builder_settings_plugin_link', 10, 2 );

/**
 * Plugin deactivation code
**/
function egoi_mail_list_builder_deactivation() {  
	//delete_option('EgoiMailListBuilderObject');
}

function egoi_mail_list_builder_fields_logged_in($fields) {
	$EgoiMailListBuilder = get_option('EgoiMailListBuilderObject');
	if($EgoiMailListBuilder->subscribe_enable){
		global $current_user;
		get_currentuserinfo();
		$status = $EgoiMailListBuilder->checkSubscriber($EgoiMailListBuilder->subscribe_list, $current_user->user_email);
		if($status == -1){
    		$fields .= "<input type='checkbox' name='egoi_mail_list_builder_subscribe' id='egoi_mail_list_builder_subscribe' value='subscribe' checked/> ".$EgoiMailListBuilder->subscribe_text;
    	}
	}
    return $fields;
}
add_filter('comment_form_logged_in','egoi_mail_list_builder_fields_logged_in');


function egoi_mail_list_builder_fields_logged_out($fields) {
	$EgoiMailListBuilder = get_option('EgoiMailListBuilderObject');
	if($EgoiMailListBuilder->subscribe_enable){
    	$fields["subscribe"] = "<input type='checkbox' name='egoi_mail_list_builder_subscribe' id='egoi_mail_list_builder_subscribe' value='subscribe' checked/> ".$EgoiMailListBuilder->subscribe_text;
	}
    return $fields;
}
add_filter('comment_form_default_fields','egoi_mail_list_builder_fields_logged_out');

function egoi_mail_list_builder_comment_process($commentdata) {
    if(isset($_POST['egoi_mail_list_builder_subscribe'])){
    	if($_POST['egoi_mail_list_builder_subscribe'] == "subscribe"){
    		//die();
    		$EgoiMailListBuilder = get_option('EgoiMailListBuilderObject');
			$result = $EgoiMailListBuilder->addSubscriber(
				$EgoiMailListBuilder->subscribe_list,
				$commentdata['comment_author'],
				'',
				$commentdata['comment_author_email']
			);
    	}
    }
    return $commentdata;
}
add_filter( 'preprocess_comment', 'egoi_mail_list_builder_comment_process' );

function egoi_mail_list_builder_register_user_scripts($hook) {
	wp_enqueue_script( 'jquery');
	wp_enqueue_script( 'jquery-ui-datepicker');
	wp_enqueue_style( 'indot-jquery-ui-css', EGOI_MAIL_LIST_BUILDER_URL . 'assets/css/jquery-ui.min.css');
	wp_enqueue_script( 'canvas-loader', EGOI_MAIL_LIST_BUILDER_URL . 'assets/js/heartcode-canvasloader-min.js');
}
add_action( 'wp_enqueue_scripts', 'egoi_mail_list_builder_register_user_scripts' );
?>