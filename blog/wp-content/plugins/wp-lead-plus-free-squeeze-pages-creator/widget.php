<?php
include_once 'code/const.php';
include_once 'code/common.php';
include_once 'code/manage_widget.php';
include_once 'code/functions.php';
include_once 'code/db.php';
include_once 'code/widget_functions.php';

//create popup page, this is the UI where user creates and manage the popup
function vgt_ui_widget_page()
{?>
    <div id="squeezer_popup">
        <div id="left_squeezer_popup" style="width: 20%; float: left;">
            <?php echo vgt_activation_reminder();?>
            <div id="site_info">
                <input type="text" placeholder="Set widget name (required)" class="form-control" id="pw_item_name" />

            </div>

        </div>

        <div id="site_area">
        </div>
        <div class="vgt_clear"></div>

        <!-- Display the themes -->

        <div id="vgt_gallery" class="vgt_gallery">
            <!--  Place to put themes thumnails  -->
            <div id="nooptin_gallery">
                <?php vgt_wpl_load_themes("nooptin", "popup"); ?>
            </div>

            <div id="optin_gallery">
                <?php vgt_wpl_load_themes("optin", "popup"); ?>
            </div>

        </div>
    </div>
    <?php include_once 'code/widgetcode.txt'; include_once 'code/common.txt';}

    add_action("wp_ajax_vgt_save_widget_option", "vgt_save_widget_option_cb");
    function vgt_save_widget_option_cb()
    {
        /*
         * 1. Get / create option_id
         * 2. Insert to option values table based on option id
         */
        global $wpdb;
        $option_id = $_POST["option_id"];

        $option_id = vgt_db_insert_popup_widget_option($option_id, $_POST["type"], $_POST["vgt_option_title"], $wpdb);

        //store the option to option table
        vgt_db_add_popup_widget_option_property($option_id, "vgt_selected_widget_id", $_POST["vgt_selected_widget_id"], $wpdb);
        vgt_db_add_popup_widget_option_property($option_id, "vgt_display_location", $_POST["vgt_display_location"], $wpdb);
        vgt_db_add_popup_widget_option_property($option_id, "vgt_widget_position_in_post", $_POST["vgt_widget_position_in_post"], $wpdb);
        vgt_db_add_popup_widget_option_property($option_id, "vgt_display_categories", $_POST["vgt_display_categories"], $wpdb);
        vgt_db_add_popup_widget_option_property($option_id, "item_id", $option_id, $wpdb);


        //if this option is activated, deactivate all other options first
        if ($_POST["vgt_active_widget"] == "activated")
        {
            vgt_db_deactivate_all_widget($wpdb);
        }

        vgt_db_add_popup_widget_option_property($option_id, "vgt_active_widget", $_POST["vgt_active_widget"], $wpdb);

        $data = array(
            "message" => "Option Saved",
            "option_id" => $option_id
        );

        echo VGT_UNIQUE_WRAPER.json_encode($data).VGT_UNIQUE_WRAPER;
        die();
    }



    //SHOW WIDGET FUNCTION
    add_filter("the_content", "vgt_display_widget");

    function vgt_display_widget($content)
    {
        /*
         * WHEN NOT TO SHOW THE WIDGET?
         * 1. there is widget shortcode on the page
         * 2. There is ab testing shortcode on the page
         * 3. There is active ab testing option in the db
         * 4. On home page
         * 5. On squeeze page (except using shortcode)
         */

        //don't show the widget if there is ab option available
        global $wpdb;

        $current_post_id = get_the_ID();
        //check the content if it has the shortcode to display popup, if yes, return false,

        //1. don't show the widget on home page
        if (is_home()) {
            return $content;
        }

        //2. don't show the widget on squeeze page
        if (get_post_meta($current_post_id, '_wp_page_template', true) == 'vgt_page_template.php') {
            return $content;
        }

        $option_id = vgt_db_get_activated_option("widget", $wpdb);

        if ($option_id == NULL)
        {
            return $content;
        }

        //check the shortcode
        if (vgt_check_shortcode_availability($content = get_post_field('post_content', $current_post_id), "in_widget_function"))
        {
            return;
        }

        //get all properties of the option
        $option_properties = vgt_db_get_all_option_properties($option_id, $wpdb);

        //check conflict with active ab test, if there is location conflict, return
        $active_ab = vgt_db_get_active_ab($wpdb, "widget");

        if ($active_ab != NULL)
        {
            if (!vgt_check_conflict($active_ab, $option_properties, $current_post_id))
            {
                return $content;
            }
        }

        //get the properties of the widget based on id
        $widget_properties = vgt_db_get_popup_widget_properties($option_properties["vgt_selected_widget_id"], $wpdb);

        //build the widget
        $widget_core = vgt_build_popup_widget($widget_properties).'<span item_type="widget" for_item="'.$widget_properties[VGT_PAGE_OUTER_ID].'" option_id="'.$option_id.'" style="display: none;" item_id="'.$option_properties["vgt_selected_widget_id"].'"></span>';

        //determine where to show the popup, based on the vgt_widget_position

        //decide to show the widget or not, based on $widget_properties["vgt_display_location"]

        if (vgt_check_display_location($option_properties, get_the_ID()))
        {

            return vgt_widget_build_full_post($content, $option_properties, $widget_core);

        } else
        {

            $return_code = $content;
        }

        //generate the content based on position of the widget in posts

        return ($return_code);
    }


    add_shortcode(VGT_WIDGET_SHORTCODE, "vgt_show_widget_shortcode");

    function vgt_show_widget_shortcode($atts)
    {

        //don't show the widget on home page
        global $wpdb;

        /* 1. Get the activated widget ID
         * 2. Check display option
         *
         */

        $option_id = $atts["option_id"];

        if ($option_id == NULL)
        {

            return;
        }

        //get post ID
        $current_post_id = get_the_ID();

        //check the shortcode
        if (vgt_check_shortcode_availability($content = get_post_field('post_content', $current_post_id), "in_widget_shortcode"))
        {
            return;
        }

        //get all properties of the option
        $option_properties = vgt_db_get_all_option_properties($option_id, $wpdb);

        //get the properties of the widget based on id
        $widget_properties = vgt_db_get_popup_widget_properties($option_properties["vgt_selected_widget_id"], $wpdb);

        //build the widget
        $widget_core = vgt_build_popup_widget($widget_properties).'<span item_type="widget" for_item="'.$widget_properties[VGT_PAGE_OUTER_ID].'" option_id="'.$option_id.'" style="display: none;" item_id="'.$option_properties["vgt_selected_widget_id"].'"></span>';

        return $widget_core;
    }