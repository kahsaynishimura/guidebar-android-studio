<?php
	include_once 'const.php';
	
	//function to load index.html
	function sq_bgt_curl_theme_loader($path)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			
		$index_file = curl_exec($ch);
		curl_close($ch);
		return $index_file;
	}
	
	//replace http with https in sites require so
	function sq_bgt_use_https($url)
	{
		if (get_option("sq_bgt_enable_https") == "yes")
		{
			return str_replace("http:", "https:", $url);
		} else 
		{
			return $url;
		}
	}
	
	//get the popup based on the id
	function get_popup_from_id($id)
	{
		$query = "SELECT * FROM ". BGT_POPUP_CODE_TABLE ." WHERE popup_id = '$id'";
		global $wpdb;
		
		$result = $wpdb->get_row($query, 'ARRAY_A');
		
		return $result;
	}
	
	//get the widget based on id
	function get_widget_from_id($id)
	{
		$query = "SELECT * FROM ". BGT_WIDGET_TABLE ." WHERE id = '$id'";
		global $wpdb;
		
		$result = $wpdb->get_row($query, 'ARRAY_A');
		
		return $result;		
	}
	
	//check if the user agent is mobile
	function sq_bgt_check_mobile($user_agent)
	{
		$is_mobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $user_agent);
		if ($is_mobile)
		{
			return "mobile";
		} else 
		{
			return "desktop";
		}
	}
	
	function sq_bgt_theme_download_error()
	{
		echo "<h2>There was a problem when downloading templates, please contact support <a target = 'blank' href='http://wpleadplus.com/contact/'>here</a></h2>";
	}
	
	add_action('switch_theme', 'sq_bgt_theme_file_not_exists');
	
	function sq_bgt_theme_file_not_exists()
	{
		
		//copy the blank theme file to current theme dir
		copy(bgt_get_plugins_location().'/wpleadplus/code/sq_ddx_blankpage.php', get_template_directory().'/sq_ddx_blankpage.php');
		copy(bgt_get_plugins_location().'/wpleadplus/code/sq_ddx_blankpage_ab.php', get_template_directory().'/sq_ddx_blankpage_ab.php');

	}
	
	