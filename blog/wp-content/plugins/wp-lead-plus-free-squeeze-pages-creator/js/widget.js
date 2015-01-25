jQuery(document).ready(function(){
    //set a localStorage (vgt_current_widget_option_id) variable to store the ID of current option, reset this ID to 0 when
    // * user click on create new option
    // Change this ID based on option select change (vgt_list_of_options)

    /*
     Load current widgets and populate in the dropdown //
     */

    localStorage.setItem("vgt_current_widget_option_id", 0);
    var data = {
        action: "vgt_get_all_popups_widgets",
        type: "widget"
    };

    var widget_array;
    jQuery.post(ajaxurl, data, function(response){
        widget_array = vgt_parse_json_output(response);

        //populate the poups to the widget selector
        var options = "<option value=''></option>";
        for (var i = 0; i < widget_array.length; i ++)
        {
            options += '<option value="'+widget_array[i].id+'">'+vgt_de_serialize_data(widget_array[i].name)+'</option>';
        }
        jQuery("#vgt_selected_widget_id").text("");
        jQuery("#vgt_selected_widget_id").append(options);
    });

    vgt_get_all_categories("#on_category_value");


    /* ==========================================================================
     Functions for CREATING widgets options
     ========================================================================== */

    jQuery("#vgt_create_option").click(function(){
        jQuery("#vgt_edit_option").removeClass("vgt-header-active");
        jQuery(this).addClass("vgt-header-active");

        localStorage.setItem("vgt_current_widget_option_id", 0);
        jQuery("#vgt_manager_left input, #vgt_manager_left select").val("");


        jQuery("#vgt_option_label").text("Give a name for this option");
        jQuery("#vgt_created_options").fadeOut();
        jQuery("#vgt_option_settings").fadeIn();

        jQuery("#vgt_get_shortcode").fadeOut();
        jQuery("#vgt_delete_option").fadeOut();

    });


    /* ==========================================================================
     Functions for EDITING widgets options
     ========================================================================== */
    jQuery("#vgt_edit_option").click(function(){
        jQuery("#vgt_create_option").removeClass("vgt-header-active");
        jQuery(this).addClass("vgt-header-active");

        jQuery("#vgt_get_shortcode").fadeOut();
        jQuery("#vgt_delete_option").fadeOut();


        jQuery("#vgt_option_label").text("Current option's name");

        //get all available widgets and put into the option selection to start editing
        var data = {
            action: "vgt_get_popup_widget_options",
            type: "widget"
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



    });

    //populate single options to the list of options


    /* ==========================================================================
     SAVE BUTTON BEHAVIOR. USE FOR BOTH CREATE AND EDIT OPTIONS
     ========================================================================== */

    //show the list of categories when users select the widget to be shown on specific categories
    jQuery(document).on("change", "#vgt_display_location", function(){
        if (jQuery(this).val() == "on_category")
        {
            jQuery("#on_category_value").parent().fadeIn();
        } else
        {
            jQuery("#on_category_value").parent().fadeOut();
        }
    });

    //Save the option
    jQuery("#vgt_widget_save_option").click(function(){


        if (vgt_check_input_in_manage(("#vgt_option_settings input, #vgt_option_settings select")) == "stop")
        {
            vgt_general_notification("warning", "You forgot to set an option", 3);
            return false;
        }

        if (jQuery("#vgt_display_location").val() == "on_category" && vgt_get_checked_categories("#on_category_value").length == 0)
        {
            vgt_general_notification("warning", "You forgot to set category", 3);
            return false;
        }

        console.log("passed test");


        //get values of all settings
        var vgt_option_title                = jQuery("#vgt_option_title").val();
        var vgt_selected_widget_id          = jQuery("#vgt_selected_widget_id").val();
        var vgt_display_location     = jQuery("#vgt_display_location").val();
        var vgt_display_categories   = vgt_serialize_data(JSON.stringify(vgt_get_checked_categories("#on_category_value")));
        var vgt_widget_position_in_post     = jQuery("#vgt_widget_position_in_post").val();
        var vgt_active_widget               = jQuery("#vgt_active_widget").val();
        var type                            = "widget";

        var data = {
            action                         : "vgt_save_widget_option",
            vgt_option_title               : vgt_serialize_data(vgt_option_title),
            vgt_selected_widget_id         : vgt_selected_widget_id,
            vgt_display_location    : vgt_display_location,
            vgt_display_categories  : vgt_display_categories,
            vgt_widget_position_in_post    : vgt_widget_position_in_post,
            vgt_active_widget              : vgt_active_widget,
            type                           : type,
            option_id                      : localStorage.getItem("vgt_current_widget_option_id")

        };
        //send POST request to save this option
        jQuery.post(ajaxurl, data, function(response){
            var return_data = vgt_parse_json_output(response);

            console.log(return_data);
            console.log(return_data["option_id"]);

            vgt_general_notification('success', return_data["message"], 4);
            localStorage.setItem("vgt_current_widget_option_id", return_data["option_id"]);

            jQuery("#vgt_get_shortcode").fadeIn();
            jQuery("#vgt_delete_option").fadeIn();

        });

        //flush the list of current option to the latest values (in case user changes the option's title)

    });

    /* ==========================================================================
     EDIT OPTIONS
     ========================================================================== */
    jQuery(document).on("change", "#vgt_list_of_options", function(){
        /*
         1. Get option ID
         2. Get option values
         3. Append values to appropriate boxes
         */
        var option_id = jQuery(this).val();

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

            //reset current option ID
            localStorage.setItem("vgt_current_widget_option_id", selected_option_id);

            var return_data = vgt_parse_json_output(response);

            for (var key in return_data)
            {
                //display_location has special additional value, so it needs a different case
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

                } else if (jQuery("#"+key).length != 0)
                {
                    jQuery("#"+key).val(return_data[key]);


                } else if (key == "item_id")
                {
                    jQuery("#vgt_current_widget").val(return_data[key]);
                }
            }

            jQuery("#vgt_option_title").val(jQuery("#vgt_list_of_options option[value="+selected_option_id+"]").text());


            jQuery("#vgt_get_shortcode").fadeIn();
            jQuery("#vgt_delete_option").fadeIn();
        });
    });




});