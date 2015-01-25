jQuery(document).ready(function(){
    //set a localStorage (vgt_current_popup_option_id) variable to store the ID of current option, reset this ID to 0 when
    // * user click on create new option
    // Change this ID based on option select change (vgt_list_of_options)

    /*
     Load current popups and populate in the dropdown //
     */

    var data = {
        action: "vgt_get_all_popups_widgets",
        type: "popup"
    };
    var popup_array;
    jQuery.post(ajaxurl, data, function(response){
        popup_array = vgt_parse_json_output(response);

        //populate the poups to the popup selector
        var options = "<option value=''></option>";
        for (var i = 0; i < popup_array.length; i ++)
        {
            options += '<option value="'+popup_array[i].id+'">'+vgt_de_serialize_data(popup_array[i].name)+'</option>';
        }

        jQuery("#vgt_current_popup").text("");
        jQuery("#vgt_current_popup").append(options);
    });


    vgt_get_all_categories("#on_category_value");
    /* ==========================================================================
     Functions for CREATING popups options
     ========================================================================== */

    jQuery("#vgt_create_option").click(function(){

        jQuery("#vgt_edit_option").removeClass("vgt-header-active");
        jQuery(this).removeClass("vgt-header-active");
        jQuery(this).addClass("vgt-header-active");

        localStorage.setItem("vgt_current_popup_option_id", 0);
        jQuery("#vgt_manager_left input, #vgt_manager_left select").val("");


        jQuery("#vgt_option_label").text("Give a name for this option");
        jQuery("#vgt_created_options").fadeOut();
        jQuery("#vgt_option_settings").fadeIn();

        jQuery("#vgt_get_shortcode").fadeOut();
        jQuery("#vgt_delete_option").fadeOut();

    });


    /* ==========================================================================
     Functions for EDITING popups options
     ========================================================================== */
    jQuery("#vgt_edit_option").click(function(){
        jQuery("#vgt_create_option").removeClass("vgt-header-active");
        jQuery(this).addClass("vgt-header-active");

        jQuery("#vgt_option_label").text("Current option's name");

        //get all available popups and put into the option selection to start editing
        var data = {
            action: "vgt_get_popup_widget_options",
            type: "popup"
        };

        jQuery.post(ajaxurl, data, function(response){
            var options_array = vgt_parse_json_output(response);


            var options = "<option value=''></option>";
            for (var i = 0; i < options_array.length; i ++)
            {
                options += '<option value="'+options_array[i].id+'">'+vgt_de_serialize_data(options_array[i].title)+'</option>';
            }

            jQuery("#vgt_list_of_options").html("");
            jQuery("#vgt_list_of_options").append(options);
            //present the UI to use
            jQuery("#vgt_created_options").fadeIn();
            jQuery("#vgt_option_settings").fadeIn();


            //reset values for all fields
            jQuery("#vgt_manager_left input, #vgt_manager_left select").val("");
        });

        jQuery("#vgt_get_shortcode").fadeOut();
        jQuery("#vgt_delete_option").fadeOut();

    });

    //populate single options to the list of options


    /* ==========================================================================
     SAVE BUTTON BEHAVIOR. USE FOR BOTH CREATE AND EDIT OPTIONS
     ========================================================================== */
    //show the list of categories when users select the popup to be shown on specific categories
    jQuery(document).on("change", "#vgt_display_location", function(){
        if (jQuery(this).val() == "on_category")
        {
            jQuery("#on_category_value").parent().fadeIn();
        } else
        {
            jQuery("#on_category_value").parent().fadeOut();
        }
    });

    //set background for the popup
    jQuery(document).on("change", "#vgt_popup_background", function(){

        var this_value = jQuery(this).val();
        var this_input_id = this_value + "_value";

        /*
        if user selects trans black or trans, don't show extra box, other show the equivalent boxes for the selected option
        since the id of the boxes = value of the option + _value
         */
        if (this_value == "transparent_black" || this_value == "transparent")
        {
            jQuery("#vgt_for_popup_bg div").hide();

        } else
        {

            jQuery("#vgt_for_popup_bg div").hide();
            jQuery("#"+this_input_id).parent().show();

        }

    });

    jQuery(document).on("change", "#vgt_when_to_show", function(){

        var this_val = jQuery(this).val();
        var this_input_id = this_val + "_value";
        if (jQuery(this).val() == "mouse_exits")
        {
            jQuery("#vgt_for_showing_behavior div").hide();


        } else
        {
            jQuery("#vgt_for_showing_behavior div").hide();
            jQuery("#"+this_input_id).parent().show();
        }

    });


    //SAVE OPTION TO DB
    jQuery("#vgt_popup_save_option").click(function(){

        if (vgt_check_input_in_manage("#vgt_option_settings input, #vgt_option_settings select") == "stop")
        {

            vgt_general_notification("warning", "You forgot to set an option", 3);
            return false;
        }

        var bg_type = jQuery("#vgt_popup_background").val();
        var bg_value = "";


        if (jQuery("#"+bg_type +"_value").length != 0)
        {
            if (jQuery("#"+bg_type +"_value").val() == "")
            {
                jQuery("#"+bg_type +"_value").css("border", "1px dashed #ff0000");
                vgt_general_notification("warning", "You forgot to set an option", 3);
                vgt_remove_attr(jQuery("#"+bg_type +"_value"), "style", 2);
                return;
            } else
            {
                bg_value = jQuery("#"+bg_type +"_value").val();
            }
        }

        var trigger_type = jQuery("#vgt_when_to_show").val();
        var trigger_value = 0;
        if (jQuery("#"+trigger_type +"_value").length != 0)
        {
            if (jQuery("#"+trigger_type +"_value").val() == "")
            {
                jQuery("#"+trigger_type +"_value").css("border", "1px dashed #ff0000");
                vgt_general_notification("warning", "You forgot to set an option", 3);
                vgt_remove_attr(jQuery("#"+trigger_type +"_value"), "style", 2);
                return;
            } else
            {
                trigger_value = jQuery("#"+trigger_type +"_value").val();
            }

        }
        //end checking values
        console.log("passed test");

        //get values of all settings
        var option_title        = jQuery("#vgt_option_name").val();
        var popup_id            = jQuery("#vgt_current_popup").val();
        var display_position    = jQuery("#vgt_popup_position").val();
        var appear_location     = jQuery("#vgt_display_location").val();
        var appear_categories   = vgt_serialize_data(JSON.stringify(vgt_get_checked_categories("#on_category_value")));
        var animation           = jQuery("#vgt_popup_animation").val();
        var frequency           = jQuery("#vgt_popup_display_frequency").val();
        var option_id           = localStorage.getItem("vgt_current_popup_option_id");
        var activated           = jQuery("#vgt_active_popup").val();

        var data = {
            action              : "vgt_save_popup_option",
            option_title        : vgt_serialize_data(option_title),
            popup_id            : popup_id,
            appear_location     : appear_location,
            appear_categories   : appear_categories,
            display_position    : display_position,
            popup_background    : bg_type,
            background_value    : bg_value,
            popup_trigger_type  : trigger_type,
            popup_trigger_value : trigger_value,
            animation           : animation,
            frequency           : frequency,
            option_id           : option_id,
            activated           : activated


        };
        //send POST request to save this option
        jQuery.post(ajaxurl, data, function(response){
            var return_data = vgt_parse_json_output(response);

            console.log(return_data);
            console.log(return_data["option_id"]);

            vgt_general_notification('success', return_data["message"], 4);
            localStorage.setItem("vgt_current_popup_option_id", return_data["option_id"]);


            jQuery("#vgt_get_shortcode").fadeIn();
            jQuery("#vgt_delete_option").fadeIn();
        });

        //flush the list of currrent option to the latest values (in case user changes the option's title)


    });

    /* ==========================================================================
     EDIT OPTIONS
     ========================================================================== */
    jQuery(document).on("change", "#vgt_list_of_options", function(){
        /*
        0. Set current option id to selected option id
        1. Get option ID
        2. Get option values
        3. Append values to appropriate boxes
         */
        var option_id = jQuery(this).val();
        localStorage.setItem("vgt_current_popup_option_id", option_id);

        var data = {
            action      : "vgt_get_option_details",
            option_id   : option_id
        };

        jQuery.post(ajaxurl, data, function(response){

            //set current option name to current option name, hah
            var selected_option_id = jQuery("#vgt_list_of_options").val();
            if (selected_option_id == "")
            {
                return;
            }

            jQuery("#vgt_option_name").val(jQuery("#vgt_list_of_options option[value="+selected_option_id+"]").text());

            //reset current option ID
            localStorage.setItem("vgt_current_popup_option_id", selected_option_id);

            var return_data = vgt_parse_json_output(response);

            for (var key in return_data)
            {
                if (jQuery("#"+key).length != 0)
                {
                    jQuery("#"+key).val(return_data[key]);

                    //set value for the background when available
                    if (key == "vgt_popup_background")
                    {
                        jQuery("#"+return_data[key]+"_value").val(return_data["background_value"]);
                        jQuery("#"+return_data[key]+"_value").parent().fadeIn();

                    } else

                    //set value for trigger type when avaialble
                    if (key == "vgt_when_to_show")
                    {
                        jQuery("#"+return_data[key]+"_value").val(return_data["trigger_value"]);
                        jQuery("#"+return_data[key]+"_value").parent().fadeIn();
                    } else

                    if (key == "vgt_display_location")
                    {
                        jQuery("#"+key).val(return_data[key]);

                        //when the option was set to on_category, get the selected options and make them selected in the select box
                        if (return_data[key] == "on_category" )
                        {
                            jQuery("#on_category_value").parent().fadeIn();
                            var selected_categories = JSON.parse(vgt_de_serialize_data(return_data["vgt_display_categories"]));

                            vgt_check_checked_categories(selected_categories, "#on_category_value");

                        }
                    }


                } else if (key == "item_id")
                {
                    jQuery("#vgt_current_popup").val(return_data[key]);
                }
            }


            jQuery("#vgt_get_shortcode").fadeIn();
            jQuery("#vgt_delete_option").fadeIn();
        });
    });



});