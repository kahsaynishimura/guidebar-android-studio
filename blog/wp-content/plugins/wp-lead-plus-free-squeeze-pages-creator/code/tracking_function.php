<?php
	include_once 'common_functions.php';
	include_once 'const.php';

	//get all data of one type
	function sq_get_type_data($type)
	{
		global $wpdb;
		$table = $wpdb->prefix."sq_bgt_tracking";
		$query_get_id = "SELECT DISTINCT page_id FROM $table WHERE page_type='$type'";

		$list_of_pages = $wpdb->get_results($query_get_id, "ARRAY_A");
	
		if ($list_of_pages == NULL)
		{
			return;
		}

		$result = array("view" => array(), "conversion" => array());
		
		for ($i = 0; $i < count($list_of_pages); $i++)
		{
			if ($list_of_pages[$i]['page_id'] == false)
			{
				continue;
			}
			$result["page_id"][] = $list_of_pages[$i]['page_id'];
			$result["view"][] = sq_get_data_by_id($list_of_pages[$i]['page_id'], "view");
			$result["conversion"][] = sq_get_data_by_id($list_of_pages[$i]['page_id'], "conversion");
			
			if ($type == "squeeze_page")
			{
				$result["title"][] = get_the_title($list_of_pages[$i]['page_id']);
				$result["url"][] = get_permalink($list_of_pages[$i]['page_id']);
			} else if ($type == "popup")
			{
				$popup = get_popup_from_id($list_of_pages[$i]['page_id']);
				$result["title"][] = $popup['name'];
				$result["code"][] = $popup['code'];
				$result["css_url"][] = $popup['css_url'];
			} else if ($type == "widget")
			{
				$widget = get_widget_from_id($list_of_pages[$i]['page_id']);
				$result["title"][] = $widget['name'];
				$result["code"][] = $widget['code'];
				$result["css_url"][] = $widget['css_url'];
			}
			
		}
		//var_dump($list_of_pages[0]['page_id']);
		return json_encode($result);
	}
	
	//function to count view/conversion based on ID
	function sq_get_data_by_id($id, $type = "view")
	{
		global $wpdb;
		$table = $wpdb->prefix."sq_bgt_tracking";
		$query = "SELECT (page_id) FROM $table WHERE conversion_type = '$type' AND page_id = '$id'";
		
		$result =  $wpdb->get_results($query, 'ARRAY_N');
		
		return $wpdb->num_rows;
	}
	
	
	