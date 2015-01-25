<?php							 
	//error_reporting(E_ALL ^ E_NOTICE);
	include_once 'code/const.php';
	function vgt_activation_tasks(){
		update_option("sq_bgt_plugin_path", plugins_url(__FILE__));
		sq_activate_add_sq_widget_popup_table();

        sq_activate_create_ab_test_table();
        sq_activate_create_ab_test_details_table();

        sq_activate_create_tracking_details_table();
        sq_activate_create_tracking_table();

        //record ajaxurl and url to the plugin
        update_option("vgt_wpl_plugin_url",plugins_url("",__FILE__));
        update_option("vgt_wpl_plugin_path", plugin_dir_path(__FILE__));
        update_option("vgt_custom_ajax_url", admin_url("admin-ajax.php"));
	};


/**********************************DB FUNCTIONS*****************************************/
	//CREATE POPUP AND WIDGETS TABLES
	function vgt_activate_create_popup_widget_table()
	{
		$myquery = 'CREATE TABLE IF NOT EXISTS '.VGT_POPUP_WIDGET_TABLE.'(
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(255),
		`type` VARCHAR(50),
		PRIMARY KEY(`id`)
		);';
		global $wpdb;
		$wpdb->query($myquery);
	}

	function vgt_activate_create_popup_widget_properties_table()
	{
		$myquery = 'CREATE TABLE IF NOT EXISTS '.VGT_POPUP_WIDGET_PROPERTIES_TABLE.'(
		`id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
		`popup_widget_id` int(11),
		`property_name` VARCHAR(100),
		`property_value` text,
		UNIQUE(`property_name`, `popup_widget_id`)
		);';
		global $wpdb;
		$wpdb->query($myquery);
	}


    function vgt_activate_create_popup_widget_options_table()
    {
        $myquery = 'CREATE TABLE IF NOT EXISTS '.VGT_POPUP_WIDGET_OPTIONS_TABLE.'(
		`id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
		`option_title` VARCHAR(255),
		`option_for`   VARCHAR(50)
		);';
        global $wpdb;
        $wpdb->query($myquery);
    }

	
	function vgt_activate_create_popup_widget_options_values_table()
	{
		$myquery = 'CREATE TABLE IF NOT EXISTS '.VGT_POPUP_WIDGET_OPTIONS_VALUES_TABLE.'(
		`id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
		`option_id` INTEGER,
		`option_name` VARCHAR(100),
		`option_value` text,
		UNIQUE(`option_id`,`option_name`)
		);';
		global $wpdb;
		$wpdb->query($myquery);
	}

	//ACTUALLY CREATE THE TABLE OF WIDGET AND POPUP
	function sq_activate_add_sq_widget_popup_table()
	{
		try
		{

			vgt_activate_create_popup_widget_table();
			vgt_activate_create_popup_widget_properties_table();
            vgt_activate_create_popup_widget_options_table();
			vgt_activate_create_popup_widget_options_values_table();
				
		} catch (Exception $e)
		{
			//var_dump($e);
		}
	}

    //CREATE TRACKING TABLE
    function sq_activate_create_tracking_table()
    {
        $myquery = 'CREATE TABLE IF NOT EXISTS '.VGT_TRACKING_TABLE.'(
		`id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
		`item_id` INTEGER,
		`option_id` INTEGER,
        `ab_test_id` INTEGER,
		`item_type` VARCHAR(255),
		UNIQUE(`item_id`,`option_id`, `ab_test_id`)
		);';

        global $wpdb;
        $wpdb->query($myquery);
    }

    function sq_activate_create_tracking_details_table()
    {
        $myquery = 'CREATE TABLE IF NOT EXISTS '.VGT_TRACKING_DETAILS_TABLE.'(
		`id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
		`tracking_id` INTEGER,
		`tracking_key` VARCHAR(255),
		`tracking_value` VARCHAR(255),
		`tracking_identifier` VARCHAR(255)
		);';

        global $wpdb;
        $wpdb->query($myquery);
    }

    //CREATE AB TESTING TABLE
    function sq_activate_create_ab_test_table()
    {
        $myquery = 'CREATE TABLE IF NOT EXISTS '.VGT_AB_TEST_TABLE.'(
		`id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
		`test_name` VARCHAR(255),
		`page_type` VARCHAR(25),
		`status`    VARCHAR(25)
		);';

        global $wpdb;
        $wpdb->query($myquery);

    }

    function sq_activate_create_ab_test_details_table()
    {
        $myquery = 'CREATE TABLE IF NOT EXISTS '.VGT_AB_TEST_DETAILS_TABLE.'(
		`id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
		`test_id` INTEGER,
		`test_key` VARCHAR(255),
		`test_value` VARCHAR(255),
		UNIQUE (`test_id`, `test_key`)
		);';

        global $wpdb;
        $wpdb->query($myquery);
    }

	//function to add a column to a table
	function sq_activate_add_column_if_not_exists($table, $column, $type, $wpdb)
	{
		try 
		{
		
			$test_query = "SHOW COLUMNS FROM $table";
			
			$all_columns = $wpdb->get_results($test_query, "ARRAY_A");
			
			for ($i = 0; $i < count($all_columns); $i++)
			{
				//if the column exists, return
				if ($all_columns[$i]["Field"] == $column)
				{
					return;
				}
			}
			
			$add_query = "ALTER TABLE $table ADD $column $type NOT NULL";

			$wpdb->query($add_query);

		} catch (Exception $e)
		{
			print($e);
		}
		
	}