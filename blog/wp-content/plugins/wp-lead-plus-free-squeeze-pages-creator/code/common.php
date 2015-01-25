<?php
//Common php functions for all the files
	//echo getcwd();
    //function to init the edit box
    include_once 'const.php';
    include_once 'functions.php';
	include_once 'db.php';
    include_once 'dom.php';



    //COMMON FUNCTIONS FOR ALL ITEMS
    add_action("wp_ajax_vgt_delete_item", "vgt_delete_item_cb");

    function vgt_delete_item_cb()
    {
        if ($_POST["item_type"] == "squeeze")
        {
            wp_delete_post($_POST["item_id"]);
        } else
        {
            global $wpdb;

            vgt_db_delete_popup_widget($_POST["item_id"], $wpdb);
            //also delete options associated with this particular item
            //1. Get the options associated with this item
            $options = vgt_db_get_options_id_by_item_id($_POST["item_id"], $wpdb);

            //2. Delete the options
            for ($i = 0; $i < count($options); $i++)
            {
                vgt_db_delete_popup_widget_option($options[$i], $wpdb);
            }

        }

        die();
    }

   //function to notify users to activate the plugin
   function vgt_activation_reminder()
   {
   		
   		return;
   }

    //add a div to footer to store ajaxurl and custom popup div
    add_action("wp_footer", "vgt_add_code_to_footer");

    function vgt_add_code_to_footer()
    {
        echo "<span class='vgt_ajax_url' style='display: none;'>".admin_url( 'admin-ajax.php' )."</span>";
        echo "<div id='vgt_custom_popup'></div>"; //What does this do?
    }

   /* PARSE THE EMAIL AND SEND BACK TO THE CLIENT */
   add_action('wp_ajax_vgt_parse_autoresponder', 'vgt_parse_autoresponder_callback');

    function vgt_parse_autoresponder_callback()
    {
        /* 1. check if the code is from Mailchimp
         * 2. Check if the code is from getresponse, which has hidden input fields (type=text_
         * 3. Parse the form to get inputs type="text"
         * 4. Parse the form to get inputs type="checkbox"
         * 5. Parse the form to get inputs type="radio"
         * 6. Parse the form to get textarea
         * 7. Parse the form to get inputs type="submit"/button
         * 8. Parse the form to get select boxes
         *
         */

        //remove the hidden box from mailchimp
        $pattern = '/<div style="position: absolute; left: -5000px;">.*?div\>/s';
        $code = preg_replace($pattern, '', vgt_de_serialize_data($_POST["code"]));

        //remove checking fields in mailpoet
        if (stripos($code, "wysija"))
        {
            $pattern = '/<span class="abs-req">.*?span\>/s';
            $code = preg_replace($pattern, '', $code);

        }


        $form_object = vgt_str_get_dom($code);
        $input_text = array();
        $input_radio = array();
        $input_checkbox = array();
        $submit = array();
        $input_hidden = array();
        $textarea = array();
        $select = array();

        $i = 0;
        $all = $form_object->select("input, button, textarea");

        foreach ($all as $a)
        {
            var_dump($a);
            $a->elem_order = $i;
            $i++;
        }



        $form = $form_object->select("form");

        $form_array = array();

        foreach ($form as $f)
        {
            $form_array["action"] = $f->action;
            $form_array["method"]   = $f->method;
        }

        //get all input text
        $object = $form_object->select("input[type=text], input[type=email], input[type=number], input[type=search], input[type=color]");


        foreach($object as $o)
        {
            if ($o->value == "")
            {
                $o->value = $o->name;
            }

            $tag = "<input elem-order='$o->elem_order' type='$o->type' value='$o->value' name='$o->name' placeholder='$o->placeholder' required='$o->required' />";
            $input_text[] = $tag;

        }

        //get all input hidden
        $object = $form_object->select("input[type=hidden]");

        foreach($object as $o)
        {
            $input_hidden[]       = $o->html();
        }
        //get all input radio
        $object = $form_object->select("input[type=radio]");

        foreach($object as $o)
        {
            $tag = "<li><input elem-order='$o->elem_order' type='$o->type' value='$o->value' name='$o->name' checked='$o->checked' placeholder='$o->placeholder' required='$o->required' /> $o->value</li>";
            $input_radio[] = $tag;
        }

        //get all input checkbox
        $object = $form_object->select("input[type=checkbox]");

        foreach($object as $o)
        {
            $tag = "<li><input elem-order='$o->elem_order' type='$o->type' value='$o->value' name='$o->name' checked='$o->checked' placeholder='$o->placeholder' required='$o->required' /> $o->value</li>";
            $input_checkbox[] = $tag;
        }

        //get all select
        $object = $form_object->select("select");

        foreach($object as $o)
        {
            $select[] = $o->html();

        }

        //get all textarea
        $object = $form_object->select("textarea");

        foreach($object as $o)
        {
            $tag = "<textarea elem-order='$o->elem_order' name='$o->name' required='$o->required' maxlength='$o->maxlength' placeholder='$o->placeholder'>$o->getInnerText()</textarea>";
            $textarea[] = $tag;
        }

        //get submit button

        $object = $form_object->select("input[type=submit], input[type=image], input[type=button]");

        foreach($object as $o)
        {

            if ($o->tag == "button")
            {
                $tag = "<input elem-order='$o->elem_order' type='submit' name='$o->name' value='$o->getInnerText()'  />";
            } else
            {
                $tag = "<input elem-order='$o->elem_order' type='submit' name='$o->name' value='$o->value'  />";
            }

            $submit[] = $tag;
        }

        //in case there is no submit button (use link and js to submit form)
        if (count($submit) == 0)
        {
            $submit[] = "<input elem-order='100' type='submit' name='Submit' value='Submit'  />";
        }

        if (count($input_radio) > 0)
        {
            $input_radio = "<ul>".implode("",$input_radio)."</ul>";
        } else
        {
            $input_radio = "";
        }

        if (count($input_checkbox) > 0)
        {
            $input_checkbox = "<ul>".implode("",$input_checkbox)."</ul>";
        } else
        {
            $input_checkbox = "";
        }
        $return_array = array(
            "input_text"    => implode("",$input_text),
            "input_hidden"  => implode("",$input_hidden),
            "input_radio"   => $input_radio,
            "input_checkbox"    => $input_checkbox,
            "textarea"          => implode("",$textarea),
            "select"            => implode("",$select),
            "form"              => $form_array,
            "submit"            => implode("",$submit)

        );
        echo VGT_UNIQUE_WRAPER.json_encode($return_array).VGT_UNIQUE_WRAPER;
        die();

    }


	add_action('wp_ajax_theme_loader', 'vgt_load_a_theme');
	//function to load themes, for squeeze pages, popups and widgets
	function vgt_load_a_theme()
	{
		/*
		 * 1. Load index.html
		 * 2. Load style.css
		 * 3. Generate unique ID
		 * 4. Replace vgt_outer_container with the unique ID (in both html content and CSS content)
		 * 5. Replace the relative path with absolute path
		 *
		 *
		 *
		 */

        $theme_id = $_POST['theme_id'];
	
		$theme_type = trim($_POST['theme_type']); //optin, no optin

		$theme_path = VGT_PLUGIN_THEMES_PATH . "templates/" .$theme_type . "/".$theme_id;
		$theme_url = VGT_PLUGIN_THEMES_URL . "templates/" .$theme_type . "/". $theme_id;
		echo $theme_url;

        //1. Load theme index.html and style.css
		$theme_files = vgt_load_theme_file($theme_path.'/index.html', $theme_path.'/assets/style.css');
        $theme_body = vgt_str_get_dom($theme_files[0]);
        $theme_object = $theme_body->select("body");
        foreach($theme_object as $t)
        {
            $theme_body = $t->getInnerText();
        }

        //2. Generate unique ID
        $unique_id = "vgt_unique_".rand(0,99990);

        //3. Replace the default ID with the unique id
        $theme_body = str_replace("vgt_outer_container", $unique_id, $theme_body);
        $css_content = str_replace("vgt_outer_container", $unique_id, $theme_files[1]);

		//4. change the relative path to absolute
		$theme_body = str_replace("assets/", $theme_url."/assets/", $theme_body);
        $css_content = str_replace("url(imgs/", "url(".$theme_url."/assets/imgs/", $css_content);

        //replace
		//pass the style sheet
		$return_content[VGT_CSS_CONTENT] = vgt_serialize_data($css_content);
	
		//pass content body
		$return_content[VGT_PAGE_CONTENT] = vgt_serialize_data($theme_body);

        //pass unique ID
        $return_content[VGT_PAGE_OUTER_ID] = $unique_id;
		//pass to the site in json format, need to add extra characters because maybe users' page generate extra character too
		echo (VGT_UNIQUE_WRAPER.json_encode($return_content).VGT_UNIQUE_WRAPER);
		die();
	}
	
	//function to load gallery with-optin and without optin
	function vgt_wpl_load_themes($optin_type = "optin")
	{
		
		$thumnail_folder = VGT_PLUGIN_THEMES_PATH . "/thumbnails/".$optin_type . "/";
		$thumbnails_url = plugins_url()."/". VGT_PLUGIN_NAME . "/themes/thumbnails/".$optin_type ."/";


		$thumbnails = scandir($thumnail_folder);
		
		for ($i = 0; $i < count($thumbnails); $i++)
		{
			if (stripos($thumbnails[$i], "jpg") == false)
			{
				continue;
			}
			
			echo "<div class='vgt_thumbnail'>";
			echo "<a href='".$thumbnails_url. $thumbnails[$i]."' rel='lightcase'><img src='".$thumbnails_url. $thumbnails[$i]."' /></a>";
			echo "<input  theme_type='".$optin_type."' type='radio' name='thumbnail' theme_id = '".intval($thumbnails[$i])."' />";
			echo "</div>";
		}

	}
	
	add_action("wp_ajax_popup_widget_save_to_db", "popup_widget_save_to_db_cb");
	function popup_widget_save_to_db_cb()
	{
		global $wpdb;

		//insert the poup/widget to table
		$popup_widget_id = vgt_db_add_popup_widget($_POST[VGT_PAGE_TITLE], $_POST[VGT_ITEM_TYPE], $wpdb, $_POST[VGT_PAGE_ID]);

		//add property to widget/popup
		vgt_db_add_popup_widget_property(VGT_POPUP_WIDGET_CODE, $_POST[VGT_PAGE_CONTENT], $popup_widget_id, $wpdb);

        vgt_db_add_popup_widget_property(VGT_PAGE_OUTER_ID, $_POST[VGT_PAGE_OUTER_ID], $popup_widget_id, $wpdb);

		vgt_db_add_popup_widget_property(VGT_CSS_CONTENT, $_POST[VGT_CSS_CONTENT], $popup_widget_id, $wpdb);

		vgt_db_add_popup_widget_property(VGT_CUSTOM_CSS_CODE, $_POST[VGT_CUSTOM_CSS_CODE], $popup_widget_id, $wpdb);

		vgt_db_add_popup_widget_property(VGT_CUSTOM_JS_CODE, $_POST[VGT_CUSTOM_JS_CODE], $popup_widget_id, $wpdb);

        vgt_db_add_popup_widget_property(VGT_AR_CODE, $_POST[VGT_AR_CODE], $popup_widget_id, $wpdb);

		$return_array = array(
			"item_id" => $popup_widget_id,
			"message" => "Saved"

		);
		echo VGT_UNIQUE_WRAPER. json_encode($return_array).VGT_UNIQUE_WRAPER;
		die();
	}


	//GET LIST OF POPUP/WIDGET TO EDIT
	add_action("wp_ajax_vgt_popup_widget_get_created", "vgt_popup_widget_get_created_cb");

	function vgt_popup_widget_get_created_cb()
	{
		global $wpdb;

		$type = trim($_POST["type"]);
		$query = "SELECT * FROM ". VGT_POPUP_WIDGET_TABLE . " WHERE type = '" . $type."'";

		$result = $wpdb->get_results($query, "ARRAY_A");

		$html_string = array();

		for ($i = 0; $i < count($result); $i++)
		{
			$html_string[] = '<div class="vgt_single_list_item"><input name="vgt_created_item" type="radio" item_id="'.$result[$i]["id"].'" item_type="'.$result[$i]["type"].'" /> <span class="vgt_single_item_name">'.vgt_de_serialize_data($result[$i]["name"]).'</span> <span class="vgt_delete_item small">  Delete</span></div>';
		}

		echo VGT_UNIQUE_WRAPER. implode("",$html_string).VGT_UNIQUE_WRAPER;

		die();


	}

	//GET THE DETAILS OF SELECTED ITEM TO EDIT
	add_action("wp_ajax_vgt_popup_widget_load_created_item", "vgt_popup_widget_load_created_item_cb");

	function vgt_popup_widget_load_created_item_cb()
	{
		$item_id = $_POST["item_id"];
		global $wpdb;

		$return_data = vgt_db_get_popup_widget_properties($item_id, $wpdb);

		echo VGT_UNIQUE_WRAPER.json_encode($return_data).VGT_UNIQUE_WRAPER;

		die();
	}

    //GET LIST OF ALL POPUPS OR WIDGETS
    add_action("wp_ajax_vgt_get_all_popups_widgets", "vgt_get_all_popups_widgets_cb");

    function vgt_get_all_popups_widgets_cb()
    {
        global $wpdb;

        echo vgt_db_get_available_popups_widgets($wpdb, $_POST["type"]);

        die();
    }


    //GET LIST OF ALL OPTIONS, WIDGET OR POPUP
    add_action("wp_ajax_vgt_get_popup_widget_options", "vgt_get_popup_widget_options_cb");

    function vgt_get_popup_widget_options_cb()
    {
        global $wpdb;

        echo VGT_UNIQUE_WRAPER.json_encode(vgt_db_get_available_options($wpdb, $_POST["type"])).VGT_UNIQUE_WRAPER;
        die();
    }

    //GET OPTION DETAILS
    add_action("wp_ajax_vgt_get_option_details", "vgt_get_option_details_cb");

    function vgt_get_option_details_cb()
    {
        /*
         * 1.
         */

        global $wpdb;

        //get options details
        $option_details = vgt_db_get_option_details($_POST["option_id"], $wpdb);
        echo VGT_UNIQUE_WRAPER.json_encode($option_details).VGT_UNIQUE_WRAPER;
        die();
    }

    //get all categories
    add_action("wp_ajax_vgt_get_all_categories", "vgt_get_all_categories_cb");

    //this function is used to response to ajax request
    function vgt_get_all_categories_cb()
    {
        echo VGT_UNIQUE_WRAPER.json_encode(vgt_get_all_categories()).VGT_UNIQUE_WRAPER;
        die();
    }

    //get all categories
    function vgt_get_all_categories()
    {
        $taxonomies = array(
            'category'
        );

        $args = array(
            'orderby'           => 'name',
            'order'             => 'ASC',
            'hide_empty'        => false);

        $terms = get_terms($taxonomies, $args);

        $data = array();
        for ($i = 0; $i < count($terms); $i++)
        {
            $data[$i]["id"] = $terms[$i]->term_id;
            $data[$i]["name"] = $terms[$i]->name;
        }

        return $data;
    }




    //CONSTRUCT POPUP WIGET FROM PROPERTIES GET FROM DB
    function vgt_build_popup_widget($properties_array)
    {
        $content = vgt_de_serialize_data($properties_array[VGT_POPUP_WIDGET_CODE]);
        $css_code = vgt_css_in_head(vgt_de_serialize_data($properties_array[VGT_CSS_CONTENT]));
        $js_code = vgt_wrap_jquery_ready(vgt_de_serialize_data($properties_array[VGT_CUSTOM_JS_CODE]));
        $custom_css_code = vgt_css_in_head(vgt_de_serialize_data($properties_array[VGT_CUSTOM_CSS_CODE]));
        return $content.$css_code.$js_code.$custom_css_code;
    }

    //generate javascript code for stylesheet URL
    function vgt_css_in_head($css_code)
    {
        $css_code = preg_replace('/[\r\n]/', '', $css_code);
        $css_head = "<style>".addslashes($css_code)."</style>";
        //remove all line-break in the CSS code

        return vgt_wrap_jquery_ready(' jQuery("'.$css_head.'").appendTo("head");');
    }

    //wrap jquery ready around code
    function vgt_wrap_jquery_ready($code)
    {
        return '<script> jQuery(document).ready(function(){'.$code.'});  </script>';
    }


    //function to decide where to show the popup (page/post/all/category
    function vgt_check_display_location($option_properties, $post_id)
    {
        $show = true;

        if ($option_properties["vgt_display_location"] == "on_posts") {
            //check if current post is a post
            if (get_post_type($post_id) != "post") {
                $show = false;
            } else
            {
                if (is_home())
                {
                    $show = false;
                }
            }


        } else if ($option_properties["vgt_display_location"] == "on_pages") {
            //check if current post is a page
            if (get_post_type($post_id) != "page") {
                $show = false;
            }

        } else if ($option_properties["vgt_display_location"] == "on_category") {
            //get current category, check if the widget is allowed to display here
            $show = vgt_check_category_belong($option_properties, $post_id);

        } else if ($option_properties["vgt_display_location"] == "on_shortcode") {
            $show = false;
        } else if ($option_properties["vgt_display_location"] == "on_home")
        {
            if (!is_home())
            {
                $show = false;
            }
        }

        return $show;
    }

    //DELETE A POPUP/WIDGET OPTION
    add_action("wp_ajax_vgt_delete_option", "vgt_delete_option_cb");

    function vgt_delete_option_cb()
    {
        global $wpdb;

        vgt_db_delete_popup_widget_option($_POST["item_id"], $wpdb);

        die();
    }


    //AB FUNCTIONS
    /*
     * This function is used to check and compare the display location of current popup/widget options to the current options of
     * active ab test
     */
    function vgt_check_conflict($ab_id, $popup_widget_option, $post_id)
    {
        /*
         * 1. Get position of ab
         * 2. Compare position of AB with the popup/widget option
         * 3. Possible cases
         *      - AB: everywhere, return false
         *      - AB: on posts, return false on post
         *      - AB: on pages, return false on pages
         *      - AB: on home (popup only), return false on home
         *      - AB: on categories, return false on matched categories
         */
        $show = true;
        global $wpdb;
        $ab_details = vgt_db_get_ab_test_details($ab_id, $wpdb);

        if ($ab_details["vgt_display_location"] == "everywhere")
        {
            $show = false;
        } else if ($ab_details["vgt_display_location"] == "on_posts")
        {

            if (get_post_type($post_id) != "post") {
                $show = false;
            }

        } else if ($ab_details["vgt_display_location"] == "on_pages")
        {
            if (get_post_type($post_id) != "page") {
                $show = false;
            }

        } else if ($ab_details["vgt_display_location"] == "on_home")
        {
            if (is_home())
            {
                $show = false;
            }

        } else if ($ab_details["vgt_display_location"] == "on_shortcode")
        {
            $show = true;
        } else if ($ab_details["vgt_display_location"] == "on_category")
        {

            $ab_categories = json_decode(vgt_de_serialize_data($ab_details["vgt_display_categories"]));

            $popup_widget_categories = json_decode(vgt_de_serialize_data($popup_widget_option["vgt_display_categories"]));

            for ($i = 0; $i < count($popup_widget_categories); $i ++)
            {
                //remove the categories that are included in $ab_categories
                //the remaining elements are the categories that the popup/widget is allowed to show
                if (in_array($popup_widget_categories[$i], $ab_categories))
                {
                    unset($popup_widget_categories[$i]);
                }

            }

            //if there is still category left, check if the current post belong to that category
            if (count($popup_widget_categories) > 0)
            {
                //get current post's category
                $cat = get_the_category($post_id);

                //check if the category of the post is in the list of allowed categories
                foreach ($cat as $c)
                {
                    if (in_array($c->term_id, $popup_widget_categories))
                    {
                        return true;
                    }
                }
            }

            $show = false;


        }

        return $show;
    }

    //check if a post belong to a list of categories
    function vgt_check_category_belong($options_properties, $post_id)
    {
        $allowed_categories = json_decode(vgt_de_serialize_data($options_properties["vgt_display_categories"]));

        if (count($allowed_categories) > 0) {
            //get current post's category
            $cat = get_the_category($post_id);

            //check if the category of the post is in the list of allowed categories
            foreach ($cat as $c) {
                if (in_array($c->term_id, $allowed_categories)) {
                    return true;
                }
            }
        }

        return false;
    }

    //check if there is shortcode on page
    function vgt_check_shortcode_availability($content, $type = "in_ab_function_popup")
    {
        //There are two places need to check shortcode, in AB code/functions and in popup widget function
        //in popup/widget functions, we need to check for ab and popup widget shortcode
        //in popup, widget shortcode, we need to check ab shortcode only
        //in addition, popup and widget shortcode should not interfere each other
        //$type == "ab_function_popup, this will be used to check in display popup in ab function not
        if ($type == "in_ab_function_popup" || $type == "in_popup_function")
        {
            if (stripos($content, VGT_AB_POPUP_SHORTCODE)  ||  stripos($content, VGT_POPUP_SHORTCODE))
            {
                return true;
            }
        } else

        //type == "ab_code_popup": ab popup shortcode, don't need to check anything
        if ($type == "in_popup_shortcode")
        {
            if (stripos($content, VGT_AB_POPUP_SHORTCODE))
            {
                return true;
            }

        } else

        if ($type == "in_ab_function_widget" || $type == "in_widget_function")
        {

            if (stripos($content, VGT_WIDGET_SHORTCODE)  ||  stripos($content, VGT_AB_WIDGET_SHORTCODE))
            {
                return true;
            }

        } else

        if ($type == "in_widget_shortcode")
        {
            if (stripos($content, VGT_AB_WIDGET_SHORTCODE))
            {
                return true;
            }
        }

        return false;
    }


    //BUTTONS' COLORS


    add_action("wp_ajax_vgt_get_buttons", "vgt_get_buttons");
    function vgt_get_buttons()
    {
        $styles = array(
            "vgt_btn_1",
            "vgt_btn_2",
            "vgt_btn_3",
            "vgt_btn_4"
        );

        $colors = array(
            "green",
            "blue",
            "violet",
            "grey",
            "yellow",
            "orange",
            "red",
            "silver"
        );

        $buttons = array(
            "styles" => $styles,
            "colors" => $colors

        );

        echo VGT_UNIQUE_WRAPER.json_encode($buttons).VGT_UNIQUE_WRAPER;
        die();
    }

    //SAVE SETTINGS VARIABLES
    add_action("wp_ajax_vgt_save_options", "vgt_save_options");

    function vgt_save_options()
    {

        update_option($_POST["option_name"], $_POST["option_value"]);

        echo VGT_UNIQUE_WRAPER. json_encode(array("message" => "Saved")). VGT_UNIQUE_WRAPER;
        die();
    }

    //ACTIVATION
    add_action("wp_ajax_vgt_enable_plugin", "vgt_enable_plugin");
    function vgt_enable_plugin()
    {

        $data = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array( 'email' => $_POST["email"], 'receipt' => $_POST["receipt"] ),
            'cookies' => array()
        );

        $url = VGT_SERVER_URL . "site/enable.php";

        $return = wp_remote_post($url, $data);
        $report = array();
        if (stripos($return["body"], "license_activated") !== FALSE)
        {
            update_option("vgt_wpl_plugin_activated", "true");
            update_option("vgt_wpl_user_email", $_POST["email"]);
            update_option("vgt_wpl_user_receipt", $_POST["receipt"]);
            update_option("vgt_wpl_server_url", $url);
            $report["message"] = "Activation Successful";
        } else
        {
            $report["message"] = "There was a problem activating the plugin. Please contact t2dx.inc@gmail.com for support.";
        }

        echo VGT_UNIQUE_WRAPER.json_encode($report).VGT_UNIQUE_WRAPER;

        die();

    }
