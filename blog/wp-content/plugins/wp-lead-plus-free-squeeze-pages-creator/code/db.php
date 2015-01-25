<?php

	include_once 'const.php';


/* ==========================================================================
   				----------------------------------------------

   					POPUP WIDGET RELATED FUNCTIONS

   				----------------------------------------------
   ========================================================================== */
	//add a poup or widget to db, return id
	function vgt_db_add_popup_widget($name, $type, $wpdb, $id = 0)
	{
		$data = array(
			"name" => $name,
			"type" => $type
		);


		if ($id == 0) // if id == 0, this is the first time adding the popup/widget to db so use insert
		{
			$wpdb->insert(
				VGT_POPUP_WIDGET_TABLE,
				$data
			);

			return $wpdb->insert_id;
		} else //update current popup/widget instead
		{
			$where = array("id" => $id);

			$wpdb->update(VGT_POPUP_WIDGET_TABLE, $data, $where);

			return $id;
		}

	}

	//add a property
	function vgt_db_add_popup_widget_property($key, $value, $id, $wpdb) //$id: popup/widget id
	{
		//update the property value if it already exists, otherwise, insert
		$where = array(
			"popup_widget_id" => $id,
			"property_name" => $key
		);

		$update = array(
			"property_value"	=> $value
		);

		$data = array(
			"popup_widget_id"   => $id,
			"property_name" 	=> $key,
			"property_value"	=> $value
		);

        //check if the key exists for the popup/wiget ID, if yes, update, othewise, insert
        $check_query = "SELECT COUNT(*) FROM ". VGT_POPUP_WIDGET_PROPERTIES_TABLE . " WHERE popup_widget_id = '" . $id . "' AND property_name = '". $key."'";

        if ($wpdb->get_var($check_query) == 0)
        {
            $wpdb->insert(VGT_POPUP_WIDGET_PROPERTIES_TABLE, $data);

        } else
        {
            $wpdb->update(VGT_POPUP_WIDGET_PROPERTIES_TABLE, $update, $where);


        }

        return $wpdb->get_var($check_query);

	}

    //FUNCTIONS RELATED TO OPTIONS
    function vgt_db_insert_popup_widget_option($option_id, $type, $title, $wpdb)
    {
        $data = array(
            "option_title" => $title,
            "option_for"    => $type
        );

        $update = array(
            "option_title" => $title
        );

        $where = array(
            "id" => $option_id
        );

        if ($option_id == 0)
        {
            $wpdb->insert(VGT_POPUP_WIDGET_OPTIONS_TABLE, $data);
            return $wpdb->insert_id;
        } else
        {
            $wpdb->update(VGT_POPUP_WIDGET_OPTIONS_TABLE, $update, $where);
        }

        return $option_id;
    }

	//insert a single option to option table
	function vgt_db_add_popup_widget_option_property($option_id, $option_name, $option_value, $wpdb)
	{
		//value to update
		$update = array(
			"option_value" => $option_value
		);

		//where to update, option_id and option_name should be unique
		$where = array(
            "option_id" => $option_id,
			"option_name" => $option_name
		);

		$data = array(
			"option_id" => $option_id,
			"option_name" => $option_name,
			"option_value" => $option_value
		);

        //check if the option_name for this particular option exists, if yes, insert
        $check_query = "SELECT COUNT(*) FROM " . VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE . " WHERE option_id='". $option_id."' AND option_name = '".$option_name."'" ;

        if ($wpdb->get_var($check_query) == 0)
        {
            $wpdb->insert(VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE, $data);
            return "INSERT";
        } else
        {
            $wpdb->update(VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE, $update, $where);
            return "UPDATE";
        }

	}

	//function to pair popup/widget to an option
	function vgt_db_pair_popup_widget_to_option($item_id, $option_id, $wpdb) //$item_id: popup/widget id
	{
		$data = array(
			"option_id" => $option_id,
			"popup_widget_id" => $item_id

		);
        //try to update first since item_id could be changed
        $update = array("popup_widget_id" => $item_id);
        $where  = array("option_id" => $option_id);

        if ($wpdb->update(VGT_POPUP_WIDGET_OPTION_PAIR_TABLE, $update, $where) === FALSE)
        {
            $wpdb->insert(VGT_POPUP_WIDGET_OPTION_PAIR_TABLE, $data);
            return "INSERT";
        }

        return "UPDATE";

	}

	//get popup/widget code and properties based on id and type
	function vgt_db_get_popup_widget_properties($id, $wpdb)
	{
		$query = "SELECT property_name, property_value FROM " . VGT_POPUP_WIDGET_PROPERTIES_TABLE . " WHERE popup_widget_id = " . $id;

        $data = $wpdb->get_results($query, ARRAY_A);

        if ($data == NULL)
        {
            return false;
        }
        $item_details = array();
        for ($i = 0; $i < count($data); $i++)
        {
            $item_details[$data[$i]["property_name"]] = $data[$i]["property_value"];
        }
		return $item_details;
	}


    //get all available popups or widget
    function vgt_db_get_available_popups_widgets($wpdb, $type)
    {
        $query = "SELECT id, name FROM " . VGT_POPUP_WIDGET_TABLE . " WHERE type='". $type."'";

        //NEED TO RE-DO, MOVE DB CODE TO DB.PHP

        $result = $wpdb->get_results($query, "ARRAY_A");

        $popup_array = array();

        for ($i = 0; $i < count($result); $i++)
        {
            $popup_array[$i]["id"] = $result[$i]["id"];
            $popup_array[$i]["name"] = $result[$i]["name"];
        }
        return VGT_UNIQUE_WRAPER.json_encode($popup_array).VGT_UNIQUE_WRAPER;
    }

    //get all options for popups/widget
    function vgt_db_get_available_options($wpdb, $type)
    {
        $query = "SELECT id, option_title FROM ". VGT_POPUP_WIDGET_OPTIONS_TABLE . " WHERE option_for = '". $type . "'";

        $result = $wpdb->get_results($query, "ARRAY_A");

        $option_array = array();

        for ($i = 0; $i < count($result); $i++)
        {
            $option_array[$i]["id"] = $result[$i]["id"];
            $option_array[$i]["title"] = $result[$i]["option_title"];
        }

        return ($option_array);
    }


    //get option details
    function vgt_db_get_option_details($option_id, $wpdb)
    {
        $query = "SELECT option_name, option_value FROM ". VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE . " WHERE option_id = " . $option_id;
        $results = $wpdb->get_results($query, "ARRAY_A");

        $option_details = array();

        for ($i = 0; $i < count($results); $i++)
        {
            $option_details[$results[$i]["option_name"]] = $results[$i]["option_value"];
        }

        return $option_details;

    }

    //get option id based on popup/widget id (item_id)
    function vgt_db_get_options_id_by_item_id($item_id, $wpdb)
    {
        $query = "SELECT option_id FROM ". VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE . " WHERE option_name = 'item_id' AND option_value = '$item_id'";

        $results = $wpdb->get_results($query);

        $options = array();

        for ($i = 0; $i < count($results); $i++)
        {
            $options[] = $results[$i]->option_id;
        }

        return $options;

        return $option_details;

    }


    //get option's title based on ID
    function vgt_db_get_option_title($option_id, $wpdb)
    {
        $query = "SELECT option_title FROM ". VGT_POPUP_WIDGET_OPTIONS_TABLE . " WHERE id = '$option_id'";

        return $wpdb->get_var($query);
    }

    //deactivate all option to make sure there is one option activated
    function vgt_db_deactivate_all_popup($wpdb)
    {
        $where = array("option_value" => "activated",
                        "option_name" => "vgt_active_popup");
        $update = array("option_value" => "deactivated");

        return $wpdb->update(VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE, $update, $where);

    }

    function vgt_db_deactivate_all_widget($wpdb)
    {
        $where = array("option_value" => "activated",
                        "option_name" => "vgt_active_widget");
        $update = array("option_value" => "deactivated");

        return $wpdb->update(VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE, $update, $where);

    }


    //get activated option
    function vgt_db_get_activated_option($type, $wpdb)
    {
        if ($type == "popup")
        {
            $query = "SELECT option_id FROM " . VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE . " WHERE option_name = 'vgt_active_popup' AND option_value = 'activated'";
        } else if ($type == "widget")
        {
            $query = "SELECT option_id FROM " . VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE . " WHERE option_name = 'vgt_active_widget' AND option_value = 'activated'";
        }


        return $wpdb->get_var($query);
    }

    //get all properties of an option
    function vgt_db_get_all_option_properties($option_id, $wpdb)
    {
        $query = "SELECT option_name, option_value FROM " . VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE . " WHERE option_id = ". $option_id;

        $temp = $wpdb->get_results($query);

        $properties = array();

        for ($i = 0; $i < count($temp); $i++)
        {
            $properties[$temp[$i]->option_name] = $temp[$i]->option_value;
        }

        return $properties;
    }

    //delete a popup/widget
    function vgt_db_delete_popup_widget($id, $wpdb)
    {
        $wpdb->delete(VGT_POPUP_WIDGET_TABLE, array("id"=> $id));
        $wpdb->delete(VGT_POPUP_WIDGET_PROPERTIES_TABLE, array("popup_widget_id"=> $id));
        return;
    }

    //delete an option
    function vgt_db_delete_popup_widget_option($id, $wpdb)
    {
        $wpdb->delete(VGT_POPUP_WIDGET_OPTIONS_TABLE, array("id" => $id));
        $wpdb->delete(VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE, array("option_id" => $id));

    }

    //get display location of popup/widget
    function vgt_db_get_display_location($option_id, $wpdb)
    {
        $query = "SELECT option_value FROM ". VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE . " WHERE option_id = '$option_id' AND option_name = 'vgt_display_location'";

        return $wpdb->get_var($query);

    }


    //check if two or more options have same display location
    function vg_db_check_match_location($options, $type, $wpdb) //#$options: array
    {
        //get the location of the first option
        $first_location = vgt_db_get_display_location($options[0], $type, $wpdb);

        for ($i = 1; $i < count($options); $i++)
        {
            if (vgt_db_get_display_location($options[0], $type, $wpdb) != $first_location)
            {
                return false;
            }
        }

        return true;
    }



/* ==========================================================================
   				----------------------------------------------

   					SQUEEZE PAGE RELATED FUNCTIONS

   				----------------------------------------------
   ========================================================================== */

    //get all squeeze page
    function vgt_db_get_all_squeeze_page($wpdb)
    {
        $query = "SELECT post_id FROM " . $wpdb->prefix. "postmeta" . " WHERE meta_value = 'vgt_page_template.php'";
        $posts = $wpdb->get_results($query);

        $pages = array();
        for ($i = 0; $i < count($posts); $i++)
        {
            if (get_post_status($posts[$i]->post_id) == "publish")
            {
                $pages[] = $posts[$i]->post_id;
            }

        }

        return $pages;

    }

    //get a single squeeze page and its properties
    function vgt_db_get_single_page_details($page_id, $wpdb)
    {
        $query = "SELECT meta_key, meta_value FROM " . $wpdb->prefix."postmeta" . " WHERE post_id = $page_id";

        $temp = $wpdb->get_results($query);

        $post_details = array();

        for ($i = 0; $i < count($temp); $i++)
        {
            $post_details[$temp[$i]->meta_key] = $temp[$i]->meta_value;
        }
        $post_details["page_title"] = get_the_title($page_id);
        return $post_details;
    }




/* ==========================================================================
   				----------------------------------------------

   					TRACKING RELATED FUNCTIONS

   				----------------------------------------------
   ========================================================================== */
    function vgt_db_record_tracking_pair($item_id, $option_id, $item_type, $ab_test_id, $wpdb)
    {
        $data = array(
            "item_id" => $item_id,
            "option_id" => $option_id,
            "ab_test_id" => $ab_test_id,
            "item_type" => $item_type
        );

        if ($wpdb->insert(VGT_TRACKING_TABLE, $data) === FALSE)
        {
            $query = "SELECT id FROM ". VGT_TRACKING_TABLE . " WHERE item_id = '$item_id' AND option_id ='$option_id'";

            return $wpdb->get_var($query);

        } else
        {
            return $wpdb->insert_id;
        }


    }

    //INSERT A SINGLE TRACKING RECORD
    function vgt_db_insert_tracking_value($tracking_id, $key, $value, $identifier, $wpdb) //$identifier is an unique ID that unique for each record, generated each time a record request is made
    {
        $data = array(
            "tracking_id" => $tracking_id,
            "tracking_key" => $key,
            "tracking_value" => $value,
            "tracking_identifier" => $identifier
        );

        $wpdb->insert(VGT_TRACKING_DETAILS_TABLE, $data);
        return;
    }

    //GET TRACKING ID BASED ON ITEM_ID, OPTION_ID, AB_ID
    function vgt_db_get_tracking_id($option_id, $ab_test_id, $item_type, $wpdb)
    {
        if ($item_type == "squeeze")
        {
            $query = "SELECT id FROM ". VGT_TRACKING_TABLE . " WHERE item_id = '$option_id' AND ab_test_id = '$ab_test_id'";
        } else
        {
            $query = "SELECT id FROM ". VGT_TRACKING_TABLE . " WHERE option_id = '$option_id' AND ab_test_id = '$ab_test_id'";
        }

        return $wpdb->get_var($query);
    }

    //get Count (view or click) based on tracking ID
    function vgt_db_get_count_by_tracking_id($tracking_id, $action_type = "view", $wpdb)
    {
        $count_query = "SELECT COUNT(*) FROM " . VGT_TRACKING_DETAILS_TABLE . " WHERE tracking_id = '$tracking_id' AND   tracking_value = '$action_type'";

        return $wpdb->get_var($count_query);
    }

    //get view details of an option
    /* there $action_type = view/close/click
     *
     *
     */
    function vgt_db_tracking_get_option_action_count($option_id, $ab_test_id = 0, $action_type = "view", $page_type = "", $wpdb)
    {
        /*
         * 1. Get tracking id
         * 2. Count base on action_type
         */
        //1. Get tracking ID

        if ($page_type == "squeeze")
        {
            $get_tracking = "SELECT id FROM ". VGT_TRACKING_TABLE ." WHERE item_id = '$option_id' AND ab_test_id = '$ab_test_id'";
        } else
        {
            $get_tracking = "SELECT id FROM ". VGT_TRACKING_TABLE ." WHERE option_id = '$option_id' AND ab_test_id = '$ab_test_id'";
        }


        $tracking_id = $wpdb->get_var($get_tracking);

        if ($tracking_id !== NULL)
        {
            return vgt_db_get_count_by_tracking_id($tracking_id, $action_type, $wpdb);

        } else
        {
            return 0;
        }

    }

    //get view count of a popup/widget/squeeze based on popup/widget id
    function vgt_db_tracking_get_item_action_count($item_id, $item_type, $action_type = "view", $wpdb)
    {
        $get_tracking_id = "SELECT id FROM " .VGT_TRACKING_TABLE . " WHERE item_id = '$item_id' AND item_type = '$item_type'";

        $options_id_list = $wpdb->get_results($get_tracking_id, ARRAY_A);

        if (count($options_id_list) == 0)
        {
            return;
        }

        $counter = 0;

        for ($i = 0; $i < count($options_id_list); $i++)
        {
            $counter += vgt_db_get_count_by_tracking_id($options_id_list[$i]["tracking_id"], $action_type, $wpdb);
        }

        return $counter;
    }

    //get clicked event details
    /*
     * Normally, in view and close event, only 1 record will be inserted in the db, however, in click event
     * there are more than one record will be inserted, first, get the tracking identifier then use that key to
     * get the rest
     */
    /*
     * This function get all clicked elements based on tracking ID
     */
    function vgt_db_get_clicked_elements($tracking_id, $wpdb)
    {
        $query = "SELECT DISTINCT (tracking_value) FROM " . VGT_TRACKING_DETAILS_TABLE . " WHERE tracking_id = '$tracking_id' AND tracking_key = 'clicked_element_id'";

        return $wpdb->get_results($query, ARRAY_A);
    }

    //get number of click a single button/link gets
    function vgt_db_get_element_click_count($element_id, $tracking_id, $wpdb)
    {
        $query = "SELECT COUNT(*) FROM " . VGT_TRACKING_DETAILS_TABLE . " WHERE tracking_key = 'clicked_element_id' AND tracking_value = '$element_id' AND tracking_id = '$tracking_id'";
        return $wpdb->get_var($query);
    }

    //get elements details based on element_id
    function vgt_get_element_details($element_id, $tracking_id, $wpdb)
    {
        $wpdb->show_errors();
        /*
         * 1. Get tracking identifier
         * 2. Get details based on tracking identifier
         */
        $query = "SELECT tracking_identifier FROM " . VGT_TRACKING_DETAILS_TABLE . " WHERE tracking_value = '$element_id' AND tracking_key = 'clicked_element_id' AND tracking_id = '$tracking_id'";

        //only need to get one row (even if there are more than one row)
        $tracking_identifier = $wpdb->get_var($query);


        //get element's details
        $query = "SELECT tracking_key, tracking_value FROM " . VGT_TRACKING_DETAILS_TABLE . " WHERE tracking_identifier = '$tracking_identifier'";

        $element_details = $wpdb->get_results($query, ARRAY_A);

        $data = array();
        for ($i = 0; $i < count($element_details); $i++)
        {
            $x = $element_details[$i];

            if ($x["tracking_key"] == "clicked_element_text")
            {
                $data["clicked_element_text"] = vgt_serialize_data($x["tracking_value"]);
            } else
            if ($x["tracking_key"] == "clicked_element_tag")
            {
                $data["clicked_element_tag"] = $x["tracking_value"];
            }

        }

        $data["clicked_element_id"] = $element_id;

        return $data;

    }

    //count close button clicked (popup only)
    function vgt_db_get_close_button_clicked($tracking_id, $wpdb)
    {
        $query = "SELECT COUNT(*) FROM " . VGT_TRACKING_DETAILS_TABLE . " WHERE tracking_id = '$tracking_id' AND tracking_key = 'event_type' AND tracking_value = 'close'";

        return $wpdb->get_var($query);
    }


    //
/* ==========================================================================
   				----------------------------------------------

   					POPUP WIDGET RELATED FUNCTIONS

   				----------------------------------------------
   ========================================================================== */







/* ==========================================================================
   				----------------------------------------------

   					AB RELATED FUNCTIONS

   				----------------------------------------------
   ========================================================================== */
    //get active ab test, this query return at max 1 result
    function vgt_db_get_active_ab($wpdb, $page_type)
    {
        $query = "SELECT id FROM ". VGT_AB_TEST_TABLE . " WHERE status = 'active' AND page_type = '$page_type'";
        return $wpdb->get_var($query); //return NULL on no result
    }

    //get all a/b tests
    function vgt_db_get_all_ab_tests($wpdb, $type = "")
    {
        if ($type == "")
        {
            $query = "SELECT * FROM ". VGT_AB_TEST_TABLE ;
        } else
        {
            $query = "SELECT * FROM ". VGT_AB_TEST_TABLE . " WHERE page_type = '$type'";
        }



        $data = $wpdb->get_results($query, ARRAY_A);

        return $data;
    }

    //get ab test details
    function vgt_db_get_ab_test_details($test_id, $wpdb)
    {
        $query = "SELECT * FROM ". VGT_AB_TEST_DETAILS_TABLE . " WHERE test_id='$test_id'";
        $query2 = "SELECT * FROM ". VGT_AB_TEST_TABLE . " WHERE id='$test_id'";

        $data = $wpdb->get_results($query, ARRAY_A);

        $data2 = $wpdb->get_row($query2, ARRAY_A);

        if ($data == NULL || $data2 == NULL)
        {
            return false;
        }

        $results = array();
        for ($i = 0; $i < count($data); $i++)
        {
            $results[$data[$i]["test_key"]] = $data[$i]["test_value"];
        }

        foreach ($data2 as $key => $value)
        {
            $results[$key] = $data2[$key];
        }

        return $results;
    }


    //insert an option to the db
    function vgt_db_insert_ab_test($id, $test_name, $page_type, $status, $wpdb)
    {
        $data = array(
            "test_name" => $test_name,
            "page_type" => $page_type,
            "status"    => $status
        );

        $where = array(
            "id" => $id
        );

        if ($id == 0) //insert
        {
            $wpdb->insert(VGT_AB_TEST_TABLE, $data);
            return $wpdb->insert_id;

        } else //update
        {
            $wpdb->update(VGT_AB_TEST_TABLE, $data, $where);

            return $id;

        }
    }

    //insert pair of key, value to ab test details table
    function vgt_db_add_ab_option($test_id, $test_key, $test_value, $wpdb, $force_update = TRUE)
    {
        //value to update
        $update = array(
            "test_value" => $test_value
        );

        //where to update, option_id and option_name should be unique
        $where = array(
            "test_id" => $test_id,
            "test_key" => $test_key
        );

        $data = array(
            "test_id" => $test_id,
            "test_key" => $test_key,
            "test_value" => $test_value
        );

        //check if the option_name for this particular option exists, if yes, insert
        $check_query = "SELECT COUNT(*) FROM " . VGT_AB_TEST_DETAILS_TABLE . " WHERE test_id='". $test_id."' AND test_key = '".$test_key."'" ;

        if ($wpdb->get_var($check_query) == 0)
        {
            $wpdb->insert(VGT_AB_TEST_DETAILS_TABLE, $data);
            return "INSERT";
        } else
        {
            if ($force_update == FALSE) //this option is used to not update date created
            {
                return "NO UPDATE";
            }
            $wpdb->update(VGT_AB_TEST_DETAILS_TABLE, $update, $where);
            return "UPDATE";
        }

    }

    //deactivate all ab option to make sure there is one option activated
    function vgt_db_deactivate_all_ab($wpdb, $type)
    {
        $where = array("page_type" => $type,
            "status" => "active");
        $update = array("status" => "disabled");

        return $wpdb->update(VGT_AB_TEST_TABLE, $update, $where);

    }

    //DELETE AB OPTION
    function vgt_db_delete_ab_test($test_id, $page_type, $wpdb)
    {
        if ($page_type == "squeeze")
        {
            //get page details and delete the page too
            $test_details = vgt_db_get_ab_test_details($test_id, $wpdb);
            wp_delete_post($test_details["ab_squeeze_id"]);

        }

        $where = array(
            "test_id" => $test_id
        );

        $where2 = array(
            "id"    => $test_id
        );

        //delete page associated with this test

        $wpdb->delete(VGT_AB_TEST_DETAILS_TABLE, $where);
        return $wpdb->delete(VGT_AB_TEST_TABLE, $where2);
    }