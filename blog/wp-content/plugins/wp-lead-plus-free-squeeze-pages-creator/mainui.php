<?php
	//include_once 'activate.php';
	//some basic functions to deal with optional variables
	include_once 'code/functions.php';
	include_once 'code/const.php';
	//function to add post meta


	/*FUNCTIONS THAT LOAD THE UI****************************************************** */
	//load the main page, activ4tion stuffs
	function vgt_ui_welcome_page()
	{

		
		echo '
			<div id="vgt_main_page" class="container">
				<div id="thankyou">
					<h2>Thanks for using WP Lead Plus</h2>
					<p>We hope you enjoy the plugin. If you have any suggestion, request, please click the support icon at the top right of your screen to send us messages.</p>
					<p>We will get back to you soon</p>
					<p>The best way to get support is to check out the video tutorials, which are available <a target= "_blank" href="http://www.youtube.com/playlist?list=PL6rw2AEN42Eoyq6_ht0TA-SM55jUWU8m5"><strong>here</strong></a></p>
				</div>
			</div>';
		
		//include common code into the page
		
		include_once 'code/common.txt';
	}

    
	//build the main page
	function vgt_ui_create_squeeze_page()
		{?>
			
			<div id="main_container">
				<div id="left_panel">
				<?php echo vgt_activation_reminder();?>
                <?php //@vgt_func_get_list_data(); ?>

				<div id="site_info">
					<input type="text" name="title"  class="form-control"  id="page_title" placeholder="Set page's title (required)" />

				</div>
	
	
				</div>
				
				<div id="site_area">
				</div>
				<div style="clear:both;"></div>
				
			</div>
			<div id="vgt_gallery" class="vgt_gallery">
				<!--  Place to put themes thumnails  -->
				<div id="nooptin_gallery">
					<?php vgt_wpl_load_themes("nooptin", "squeeze"); ?>
				</div>
				
				<div id="optin_gallery">
					<?php vgt_wpl_load_themes("optin", "squeeze"); ?>
				</div>
				
			</div>


			
			<!-- Get the footer panel -->		
			<div id="insert_code">
				<?php include_once 'code/editcode.txt'; include_once 'code/common.txt'; ?>
				
			</div>
			
		<?php }

	//PUBLISH THE PAGE
	add_action('wp_ajax_vgt_publish_post', 'vgt_publish_squeeze_page');

	function vgt_publish_squeeze_page()
	{

        $post = array(
            "post_content" => vgt_de_serialize_data($_POST[VGT_PAGE_CONTENT]),
            "post_name" => vgt_de_serialize_data($_POST[VGT_PAGE_TITLE]),
            "post_title" => vgt_de_serialize_data($_POST[VGT_PAGE_TITLE]),
            "post_status" => $_POST["page_status"],
            "post_type" => "page",
            "page_template" => VGT_PAGE_TEMPLATE
        );


        if ($_POST[VGT_PAGE_ID] != 0)
        {

            $post["ID"] = $_POST[VGT_PAGE_ID];
        }


        $page_id = wp_insert_post($post, true);

        //update post meta
        update_post_meta($page_id, VGT_CSS_CONTENT, $_POST[VGT_CSS_CONTENT]);
        update_post_meta($page_id, VGT_CUSTOM_JS_CODE, $_POST[VGT_CUSTOM_JS_CODE]);
        update_post_meta($page_id, VGT_CUSTOM_JS_CODE_POSITION, $_POST[VGT_CUSTOM_JS_CODE_POSITION]);
        update_post_meta($page_id, VGT_CUSTOM_CSS_CODE, $_POST[VGT_CUSTOM_CSS_CODE]);
        update_post_meta($page_id, VGT_AR_CODE, $_POST[VGT_AR_CODE]);

        update_post_meta($page_id, VGT_PAGE_CONTENT, $_POST[VGT_PAGE_CONTENT]);

        update_post_meta($page_id, VGT_INNER_BACKGROUND, $_POST[VGT_INNER_BACKGROUND]);
        update_post_meta($page_id, VGT_INNER_BACKGROUND_TYPE, $_POST[VGT_INNER_BACKGROUND_TYPE]);
        update_post_meta($page_id, VGT_OUTER_BACKGROUND, vgt_de_serialize_data($_POST[VGT_OUTER_BACKGROUND]));
        update_post_meta($page_id, VGT_OUTER_BACKGROUND_TYPE, $_POST[VGT_OUTER_BACKGROUND_TYPE]);
        update_post_meta($page_id, VGT_PAGE_OUTER_ID, $_POST[VGT_PAGE_OUTER_ID]);

        $return_data = array("page_id" => $page_id, "message" => "Done!");

        echo VGT_UNIQUE_WRAPER.json_encode($return_data).VGT_UNIQUE_WRAPER;

        die();
	}

	//END PUBLISHING THE PAGE

	//SHOW POSTS TO EDIT
	add_action('wp_ajax_vgt_get_created_pages', 'vgt_show_created_pages_cb');
	
	function vgt_show_created_pages_cb()
	{
        global $wpdb;
        $pages_id = vgt_db_get_all_squeeze_page($wpdb);
        $html_string = array();

        for ($i = 0; $i < count($pages_id); $i ++)
        {
            if (get_post_status($pages_id[$i]) != 'trash')
            {
                $html_string[] = '<div class="vgt_single_list_item"><input name="vgt_created_item" type="radio" item_id="'.$pages_id[$i].'" /> <span class="vgt_single_item_name">  '. get_the_title($pages_id[$i]).'</span> <span class="vgt_delete_item small">  Delete</span></div>';
            }

        }

        echo VGT_UNIQUE_WRAPER.implode("",$html_string).VGT_UNIQUE_WRAPER;

        die();
		
	}
	
	//END SHOWING POSTS TO EDIT	
	
	//EDIT CREATED POST
	add_action('wp_ajax_vgt_edit_created_page', 'vgt_edit_created_page_callback');
	
	function vgt_edit_created_page_callback()
	{
        $page_id = $_POST["page_id"];
        global $wpdb;
        echo VGT_UNIQUE_WRAPER.json_encode(vgt_db_get_single_page_details($page_id, $wpdb)).VGT_UNIQUE_WRAPER;

        die();
	}

    //get all squeeze page
    add_action("wp_ajax_vgt_get_all_squeeze", "vgt_get_all_squeeze_cb");

    function vgt_get_all_squeeze_cb()
    {
        global $wpdb;
        $pages_id = vgt_db_get_all_squeeze_page($wpdb);

        $pages = array();
        for ($i = 0; $i < count($pages_id); $i ++)
        {
            $pages[$i]["id"] = $pages_id[$i];
            $pages[$i]["title"] = get_the_title($pages_id[$i]);
        }

        echo VGT_UNIQUE_WRAPER.json_encode($pages).VGT_UNIQUE_WRAPER;

        die();
    }
