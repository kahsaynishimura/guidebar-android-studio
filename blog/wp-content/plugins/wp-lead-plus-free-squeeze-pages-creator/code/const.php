<?php
	
	include_once vgt_get_site_installation_path().'/wp-load.php';
	//get the document root
	function vgt_get_site_installation_path()
	{
		$full_path = getcwd();
		$ar = explode("wp-", $full_path);
		
		return $path = $ar[0];

	}
	
	function vgt_get_plugins_location()
	{
		$full_path = plugin_dir_path(__FILE__);
		$pattern = '|[/\\\]'.VGT_PLUGIN_NAME.'[/\\\]|';
		$ar = preg_split($pattern, $full_path);

		return $ar[0];
	}

    //get plugin folder
    function vgt_get_plugin_folder_name()
    {
        $path = plugin_dir_path(__FILE__);
        //replace backlash to forwardslash
        $path = str_replace("\\", "/", $path);
        $path = str_replace("/code", "", $path);

        //remove the last slash
        if (substr($path, strlen($path) - 1, 1 ) == "/")
        {
            $path = substr_replace($path, "", strlen($path) - 1, 1);

        }

        //get the name
        $array = explode("/", $path);

        return $array[count($array) - 1];
    }




    global $wpdb;
	//define some constants
	define('VGT_PLUGIN_NAME', vgt_get_plugin_folder_name());

	//widget and popup will use same tables
	
	define('VGT_POPUP_WIDGET_TABLE', $wpdb->prefix.'vgt_popups_widgets'); //id, item_name(varchar), type(popup/widget)
    define('VGT_POPUP_WIDGET_OPTIONS_TABLE', $wpdb->prefix.'vgt_popups_widgets_options'); //id, item_name(varchar), type(popup/widget)
	define('VGT_POPUP_WIDGET_PROPERTIES_TABLE', $wpdb->prefix.'vgt_popups_widgets_properties'); //id, popup_id(int), property_name(varchar), property_value(text) extra options for popup/widget such as button behavior, custom js, ac
	define('VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE', $wpdb->prefix.'vgt_popups_widgets_options_values');//id, option_id(varchar), option_name(varchar), option_value(text)
	define('VGT_POPUP_WIDGET_OPTION_PAIR_TABLE', $wpdb->prefix.'vgt_popups_widgets_options_pair');//id, option_id(var_char), popup_id(int)

    define('VGT_AB_TEST_TABLE', $wpdb->prefix.'vgt_ab_test');
    define('VGT_AB_TEST_DETAILS_TABLE', $wpdb->prefix.'vgt_ab_test_details');


    define('VGT_TRACKING_TABLE', $wpdb->prefix.'vgt_tracking');//id, option_id(varchar), item_id(varchar)
    define('VGT_TRACKING_DETAILS_TABLE', $wpdb->prefix.'vgt_tracking_details');//id, tracking_key(var_char), tracking_value(var_char), tracking_identifier(var_char)

	define('VGT_SERVER_URL', "http://wpleadplus.com/");

	define('VGT_PLUGIN_THEMES_PATH', vgt_get_plugins_location(). "/". VGT_PLUGIN_NAME . "/" . "themes/");
	define('VGT_PLUGIN_THEMES_URL', plugins_url(). "/". VGT_PLUGIN_NAME . "/" . "themes/");

	define('VGT_PLUGIN_SQUEEZE_PATH', vgt_get_plugins_location(). "/". VGT_PLUGIN_NAME . "/" . "themes/squeeze/");
	define('VGT_PLUGIN_SQUEEZE_URL', plugins_url(). "/". VGT_PLUGIN_NAME . "/" . "themes/squeeze/");
	
	define('VGT_PLUGIN_POPUP_PATH', vgt_get_plugins_location(). "/". VGT_PLUGIN_NAME . "/" . "themes/popup/");
	define('VGT_PLUGIN_POPUP_URL', plugins_url(). "/". VGT_PLUGIN_NAME . "/" . "themes/popup/");
	
	define('VGT_PLUGIN_WIDGET_PATH', vgt_get_plugins_location(). "/". VGT_PLUGIN_NAME . "/" . "themes/widget/");
	define('VGT_PLUGIN_WIDGET_URL', plugins_url(). "/". VGT_PLUGIN_NAME . "/" . "themes/widget/");


    define('VGT_PAGE_OUTER_ID', "vgt_page_outer_id");
    define('VGT_POPUP_WIDGET_CODE', "popup_widget_code");
	define('VGT_CSS_CONTENT', "vgt_css_content");
	define('VGT_CUSTOM_CSS_CODE', "vgt_custom_css_code");
	define('VGT_CUSTOM_JS_CODE', "vgt_custom_js_code");
    define('VGT_CUSTOM_JS_CODE_POSITION', "vgt_custom_js_code_position");

    define('VGT_AR_CODE', "vgt_ar_code");

    define('VGT_PAGE_CONTENT', "vgt_page_content");
    define('VGT_INNER_BACKGROUND', "vgt_inner_background");
    define('VGT_INNER_BACKGROUND_TYPE', "vgt_inner_background_type");
    define('VGT_OUTER_BACKGROUND', "vgt_outer_background");
    define('VGT_OUTER_BACKGROUND_TYPE', "vgt_outer_background_type");
    define('VGT_PAGE_ID', "vgt_page_id");
    define('VGT_PAGE_TITLE', "vgt_page_title");
    define('VGT_ITEM_TYPE', "vgt_item_type"); //FOR POPUP & WIDGET ONLY

    define('VGT_PAGE_TEMPLATE', "vgt_page_template.php");
    define('VGT_PAGE_TEMPLATE_AB', "vgt_page_template_ab.php");

    $return_data = array("page_id" => $page_id, "message" => "Done!");


	//define an unique string to wrap around json response
	define('VGT_UNIQUE_WRAPER', "vgt_unique_338742");

    define('VGT_POPUP_SHORTCODE', "wpl_show_popup");
    define('VGT_WIDGET_SHORTCODE', "wpl_show_widget");
    define('VGT_AB_POPUP_SHORTCODE', "wpl_ab_popup_test");
    define('VGT_AB_WIDGET_SHORTCODE', "wpl_ab_widget_test");
    define('VGT_AB_SQUEEZE_SHORTCODE', "wpl_ab_squeeze_test");

    define('AB_SQUEEZE_ID', 'ab_squeeze_id');