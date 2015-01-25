<?php
        include_once 'widget.php';
		include_once 'enq.php';
		include_once 'mainui.php';
        include_once 'code/common.php';
        include_once 'code/extras.php';


	//add background and buttons to db


	//add the menu to dashboard
	add_action('admin_menu', 'vgt_register_menu_left');
	function vgt_register_menu_left()
	{
		$main_page = add_menu_page('WP Lead Plus Responsive Home', 'WP Lead Plus Responsive', 'manage_options', 'wpl_lead_menu_item', 'vgt_ui_welcome_page');
		
		
		$edit_page = add_submenu_page('wpl_lead_menu_item', 'Add New/ Edit Page', '<span style="color: #d8ff00;">Create Squeeze Page</span>', 'manage_options', 'sub_squeezers_new', 'vgt_ui_create_squeeze_page');

        //$popup_create = add_submenu_page('wpl_lead_menu_item', 'Create Popup', '<span style="color: #25a0ff;">Create Popup</span>', 'manage_options', 'sub_squeezers_popup_create', 'vgt_ui_popup_page');
        //$popup_manage = add_submenu_page('wpl_lead_menu_item', 'Manage Popup', '<span style="color: #25a0ff;">Manage Popup</span>', 'manage_options', 'sub_squeezers_popup_manage', 'vgt_ui_popup_manage_page');

        $widget_page = add_submenu_page('wpl_lead_menu_item', 'Create Widget', '<span style="color: #ff5a00;">Create Widget</span>', 'manage_options', 'sub_squeezers_widget_create', 'vgt_ui_widget_page');
        $widget_manage = add_submenu_page('wpl_lead_menu_item', 'Manage Widget', '<span style="color: #ff5a00;">Manage Widget</span>', 'manage_options', 'sub_squeezers_widget_manage', 'vgt_ui_widget_manage_page');

        //$tracking_page = add_submenu_page('wpl_lead_menu_item', 'WP Lead Plus Tracking', '<span style="color: #d200ff">Tracking</span>', 'manage_options', 'sub_vgt_tracking', 'sub_vgt_tracking_cb');

        //$lab_ab = add_submenu_page('wpl_lead_menu_item', 'WP Lead Plus A/B Lab', '<span style="color: #d200ff">A/B Lab</span>', 'manage_options', 'sub_squeezers_ab_lab', 'sub_squeezers_ab_lab_cb');

		

        $make_money = add_submenu_page('wpl_lead_menu_item', 'Make Money', '<span style="color: #1eff00">Make Money</span>', 'manage_options', 'sub_squeezers_make_money', 'vgt_make_sales');
        $go_pro = add_submenu_page('wpl_lead_menu_item', 'Go Pro', '<span style="color: #0078ff">Go Pro</span>', 'manage_options', 'sub_squeezers_go_pro', 'vgt_pro_features');
		
		$settings_page = add_submenu_page('wpl_lead_menu_item', 'WP Lead Plus Settings', 'Settings', 'manage_options', 'sub_squeezers_set', 'vgt_settings_page_ui');		
		
		add_action( 'admin_print_styles-' . $main_page, 'vgt_enq_enqueue_custom_styles');
		add_action( 'admin_print_styles-' . $edit_page, 'vgt_enq_enqueue_custom_styles');
		add_action( 'admin_print_styles-' . $settings_page, 'vgt_enq_enqueue_custom_styles');
		add_action( 'admin_print_styles-' . $make_money, 'vgt_enq_enqueue_custom_styles');
        add_action( 'admin_print_styles-' . $go_pro, 'vgt_enq_enqueue_custom_styles');
		
		add_action( 'admin_print_styles-' . $widget_page, 'vgt_enq_enqueue_widget_styles');
		add_action( 'admin_print_styles-' . $widget_manage, 'vgt_enq_enqueue_widget_styles');



		add_action( 'admin_print_styles-' . $main_page, 'vgt_enq_load_scripts_default');
		add_action( 'admin_print_styles-' . $main_page, 'vgt_enq_load_scripts_squeeze_page');
		
		add_action( 'admin_print_styles-' . $edit_page, 'vgt_enq_load_scripts_default');
		add_action( 'admin_print_styles-' . $settings_page, 'vgt_enq_load_scripts_default');
		add_action( 'admin_print_styles-' . $settings_page, 'vgt_enq_load_scripts_squeeze_page');
		
		add_action( 'admin_print_styles-' . $edit_page, 'vgt_enq_load_scripts_squeeze_page');
		
		add_action( 'admin_print_styles-' . $widget_page, 'vgt_enq_load_scripts_widget');
		add_action( 'admin_print_styles-' . $widget_manage, 'vgt_enq_load_scripts_widget');

	}


    function vgt_settings_page_ui()
    { ?>
        <div id="vgt_settings">
            <h4 class="">Tracking code</h4>
            <p>If you want to use tracking code (Google Analytics/Statcounter...) on squeeze pages, put your full code here</p>
            <textarea rows="10" class="form-control" id="vgt_settings_tracking_code"></textarea>

            <button class="btn btn-success" id="vgt_settings_save_tracking">Save tracking code</button>


        </div>


    <?php include_once "code/common.txt"; }