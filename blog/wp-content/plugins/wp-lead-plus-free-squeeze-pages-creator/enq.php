<?php
    /* REGISTER THE SCRIPTS */
//register the libraries

    //FRONT END
    function vgt_front_end_scripts() {
        wp_register_script('vgt_front_script', plugins_url('js/front.js', __FILE__));
        wp_register_script('vgt_bg_stretch', plugins_url('js/backs.js', __FILE__));
        wp_register_style('vgt_front_style', plugins_url('css/front.css', __FILE__));
        wp_register_style('vgt_button_styles', plugins_url('css/button-styles.css', __FILE__));

        wp_enqueue_script('jquery');

        wp_enqueue_script('vgt_front_script');
        wp_enqueue_script('vgt_bg_stretch');
        wp_enqueue_style('vgt_front_style');
        wp_enqueue_style('vgt_button_styles');
    }

    add_action( 'wp_enqueue_scripts', 'vgt_front_end_scripts' );

    //ADMIN

	add_action('admin_enqueue_scripts', 'vgt_enq_register_all_scripts');

	function vgt_enq_register_all_scripts()
	{
		wp_register_script('sq_custom_ebox', plugins_url('js/ebox.js', __FILE__));
		wp_register_script('editscript', plugins_url('js/edit.js', __FILE__));
		wp_register_script('widgetscript', plugins_url('js/widget.js', __FILE__));
		wp_register_script('sq_gallery_script', plugins_url('js/gallery.js', __FILE__));
        wp_register_script('vgt_code_processing', plugins_url('js/code_process.js', __FILE__));
		wp_register_script('sqcommon', plugins_url('js/common.js', __FILE__));
        wp_register_script('vgt_modal', plugins_url('js/modal/jquery.modal.min.js', __FILE__));
		wp_register_script('lightcase', plugins_url('js/lc/lc.js', __FILE__));
		wp_register_script('base64code', plugins_url('js/base64.js', __FILE__));
		wp_register_script('sq_custom_tinymce', plugins_url('js/tinymce/tinymce.min.js', __FILE__)); //wp_deregister_script
		wp_register_script('sq_custom_jui', plugins_url('js/jui-min.js', __FILE__)); //jquery ui	
		wp_register_script('sq_custom_jq', plugins_url('js/jq.js', __FILE__)); //jquery ui
        wp_register_script('vgt_bg_stretch', plugins_url('js/backs.js', __FILE__));
        wp_register_script('vgt_ab', plugins_url('js/ab.js', __FILE__));
        wp_register_script('vgt_tracking_backend', plugins_url('js/tracking_backend.js', __FILE__));
        wp_register_script('vgt_bootstrap', plugins_url('css/bs/js/bootstrap.min.js', __FILE__));

	}
//load scripts for default page, create and edit and others	
	function vgt_enq_load_scripts_default()
	{
		if (is_admin())
		{
			wp_deregister_script('tiny_mce'); 
			
			global $wp_version;
			if ($wp_version > 3.6)
			{
				wp_enqueue_script('jquery');
			} else 
			{
				wp_deregister_script('jquery');
				wp_enqueue_script('sq_custom_jq');
			}
			
			wp_enqueue_script('sq_custom_tinymce');
			wp_enqueue_script('sq_custom_ebox', false, array('jquery'));

			wp_enqueue_script('sq_custom_jui');//
			wp_enqueue_script('lightcase');
            wp_enqueue_script('vgt_bg_stretch');

			wp_enqueue_script('base64code');
            wp_enqueue_script('vgt_code_processing');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('sqcommon');
		}
	}
	
	function vgt_enq_load_scripts_squeeze_page()
	{

        wp_enqueue_script('vgt_bootstrap');
        wp_enqueue_script('editscript');
        wp_enqueue_script('vgt_modal');
	}

    //load script for widget page
	function vgt_enq_load_scripts_widget()
	{
		
		if (is_admin())
		{
			wp_enqueue_script('sq_custom_tinymce');
			wp_enqueue_script('sq_custom_ebox', false, array('jquery'));

            wp_enqueue_script('vgt_bootstrap');
			wp_enqueue_script('sq_custom_jui');
            wp_enqueue_script('vgt_bg_stretch');
			wp_enqueue_script('lightcase');
			wp_enqueue_script('base64code');
			wp_enqueue_script('thickbox');
            wp_enqueue_script('vgt_code_processing');
			wp_enqueue_script('sqcommon');
            wp_enqueue_script('vgt_modal');
            wp_enqueue_script('widgetscript');

		}
	}	
	
    //load script for popup page
	function vgt_enq_load_scripts_popup()
	{
		if (is_admin())
		{
            wp_enqueue_script('vgt_bootstrap');
			wp_enqueue_script('sq_custom_jui');
			wp_enqueue_script('lightcase');
			wp_enqueue_script('base64code');
            wp_enqueue_script('vgt_bg_stretch');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('sq_custom_tinymce'); //mce
			wp_enqueue_script('sq_custom_ebox', false, array('jquery'));
            wp_enqueue_script('vgt_code_processing');
            wp_enqueue_script('vgt_modal');
			wp_enqueue_script('sqcommon');
            wp_enqueue_script('popupscript');
		}
	}

	
	//including the custom stylesheet
	add_action('admin_init', 'vgt_enq_add_style_sheet');

	
	function vgt_enq_add_style_sheet()
	{
		wp_register_style('editstyle', plugins_url('css/style.css', __FILE__));

        wp_register_style('vgt_button_styles', plugins_url('css/button-styles.css', __FILE__));
		
		wp_register_style('widgetstyle', plugins_url('css/widget.css', __FILE__));
		
		wp_register_style('popupstyle', plugins_url('css/popup.css', __FILE__));
		
		wp_register_style('sq_theme_gallery_style', plugins_url('css/gallery.css', __FILE__));

		wp_register_style('sq_ng_commonstyle', plugins_url('css/common.css', __FILE__));

        wp_register_style('vgt_modal', plugins_url('js/modal/jquery.modal.css', __FILE__));
		
		wp_register_style('sq_jui_min', plugins_url('css/jui-min.css', __FILE__));
		
		wp_register_style('lcstyle', plugins_url('js/lc/css/lc.css', __FILE__));//light case

		wp_register_style('sq_conv_style', plugins_url('css/conversion.css', __FILE__));//light case
		
        wp_register_style('sqsocialstyle', plugins_url('css/social.css', __FILE__));//social

        wp_register_style('sqbootstrapstyle', plugins_url('css/bs/css/bootstrap.min.css', __FILE__));//social

        wp_register_style('vgt_tracking_backend_style', plugins_url('css/tracking.css', __FILE__));//social

        wp_register_style('vgt_ab_style', plugins_url('css/ab.css', __FILE__));//social



	}
    
    
    
    	//load stylesheet for default page (within the plugin)
	function vgt_enq_enqueue_custom_styles()
	{
		wp_enqueue_style('lcstyle');
        wp_enqueue_style('sqbootstrapstyle');
		wp_enqueue_style('vgt_modal');
        wp_enqueue_style('sq_jui_min');
        wp_enqueue_style('sq_ng_commonstyle');
        wp_enqueue_style('vgt_button_styles');
        wp_enqueue_style('editstyle');

	}
	
	//load stylesheet for widget page
	function vgt_enq_enqueue_widget_styles()
	{
		wp_enqueue_style('sqbootstrapstyle');

        wp_enqueue_style('sq_jui_min');

		wp_enqueue_style('lcstyle');
		wp_enqueue_style('sq_ng_commonstyle');
        wp_enqueue_style('vgt_button_styles');
        wp_enqueue_style('vgt_modal');
        wp_enqueue_style('widgetstyle');

	}

	//load stylesheet for popup page
	function vgt_enq_enqueue_popup_styles()
	{
		wp_enqueue_style('sqbootstrapstyle');
        wp_enqueue_style('sq_jui_min');
		wp_enqueue_style('lcstyle');
		wp_enqueue_style('sq_ng_commonstyle');
        wp_enqueue_style('vgt_button_styles');
        wp_enqueue_style('vgt_modal');
        wp_enqueue_style('popupstyle');
	}

    //load stylesheet for popup page
    function vgt_enq_enqueue_ab_styles()
    {
        wp_enqueue_style('sqbootstrapstyle');
        wp_enqueue_style('vgt_modal');
        wp_enqueue_style('popupstyle');
        wp_enqueue_style('lcstyle');
        wp_enqueue_style('sq_ng_commonstyle');
        wp_enqueue_style('vgt_ab_style');
    }

    //load stylesheet for popup page
    function vgt_enq_enqueue_tracking_styles()
    {
        wp_enqueue_style('sqbootstrapstyle');
        wp_enqueue_style('vgt_modal');
        wp_enqueue_style('lcstyle');
        wp_enqueue_style('sq_ng_commonstyle');
        wp_enqueue_style('vgt_tracking_backend_style');
    }
