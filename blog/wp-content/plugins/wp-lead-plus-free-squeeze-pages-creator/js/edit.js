/*
localStorage variables for squeeze pages only
"vgt_outer_background"
"vgt_inner_background"
"vgt_outer_background_type" (solid/pattern/img)
"vgt_inner_background_type" (solid/pattern)

These value will be reset to "" on template load/edit

 */


jQuery(document).ready(function(){
    /* SELECT TEMPLATE */
        //done in common.js

    /* PUBLISH A PAGE */

    jQuery(document).on("click", "#publishb", function(){

        jQuery(".vgt_vertical_menu").fadeOut();

        var offset = jQuery(this).offset();

        jQuery("#save_buttons").offset({left: offset.left});
        jQuery("#save_buttons").fadeIn();

    });

    /* DRAG THE BOX */
    jQuery(document).on("click", "#vgt_enable_drag", function(){
        if (jQuery("#site_area div").length == 0)
        {
            return;
        }

        if (jQuery(this).val() == "Start Drag")
        {
            jQuery(this).val("Stop Drag");
            jQuery("#site_area > div").draggable();
        } else
        {
            jQuery(this).val("Start Drag");
            jQuery("#site_area > div").draggable("disable");
        }

    });

    jQuery(document).on("click", "#publish_post, #draft_post", function(){
        jQuery(this).parent().fadeOut();
        //in case user forgot to select a template
        if (jQuery.trim(jQuery('#site_area').html()) == "") {

        vgt_general_notification('warning', "Click on Select to select a template first", 4);

        return false;
        }
        //in case user forgot to set a title
        if (jQuery.trim(jQuery("#page_title").val()) == "")
        {
            vgt_general_notification('warning', "Please set a title", 4);
            jQuery("#page_title").css("background", "#ffd3d3");
            return false;
        }

        //prepare the content
        var content = jQuery("#site_area").clone();
        content                     = vgt_serialize_data(content.html());
        console.log(content);

        var post_status             = jQuery(this).attr("save_action");

        var page_title              = vgt_serialize_data(jQuery("#page_title").val());

        var page_id                 = localStorage.getItem(VGT_PAGE_ID);

        var autoresponder_code      = localStorage.getItem(VGT_AR_CODE);
        var custom_css_code         = localStorage.getItem(VGT_CUSTOM_CSS_CODE);
        var custom_js_code          = localStorage.getItem(VGT_CUSTOM_JS_CODE);
        var custom_js_code_position = localStorage.getItem(VGT_CUSTOM_JS_CODE_POSITION);
        var outer_background        = localStorage.getItem(VGT_OUTER_BACKGROUND);
        var outer_background_type   = localStorage.getItem(VGT_OUTER_BACKGROUND_TYPE);
        var inner_background        = localStorage.getItem(VGT_INNER_BACKGROUND);
        var inner_background_type   = localStorage.getItem(VGT_INNER_BACKGROUND_TYPE);


        var data = {
            action                  : "vgt_publish_post",
            page_status             : post_status,
            vgt_page_title              : page_title,
            vgt_page_id                 : page_id,
            vgt_ar_code      : autoresponder_code,
            vgt_css_content             : localStorage.getItem(VGT_CSS_CONTENT),
            vgt_custom_js_code          : custom_js_code,
            vgt_custom_js_code_position : custom_js_code_position,
            vgt_custom_css_code         : custom_css_code,
            vgt_outer_background        : outer_background,
            vgt_outer_background_type   : outer_background_type,
            vgt_inner_background        : inner_background,
            vgt_inner_background_type   : inner_background_type,
            vgt_page_content            : content,
            vgt_page_outer_id       : localStorage.getItem("vgt_page_outer_id")
        };

        jQuery.post(ajaxurl, data, function(response){
            var return_data = vgt_parse_json_output(response);

            vgt_general_notification("info", return_data.message, 4);
            localStorage.setItem("vgt_page_id", return_data.page_id);


        });




    });


    /* EDIT CREATED PAGES */
    jQuery(document).on("click", "#vgt_editpageb", function(){
        var data = {
            action: "vgt_get_created_pages"
        }

        jQuery.post(ajaxurl, data, function(response){
            var return_data = response.split(VGT_UNIQUE_WRAPER);
            jQuery(".vgt_gallery").hide();
            jQuery("#vgt_created_item_gallery").html(return_data[1]);
            jQuery("#vgt_created_item_gallery").fadeToggle();
        });

    });

    jQuery(document).on("click", "#editthispageb", function() {
        //clear localStorage variables
        localStorage.clear();

        jQuery(this).fadeOut();
        jQuery(".vgt_gallery").hide();

        var page_id = jQuery("input[name=vgt_created_item]:checked").attr("item_id");
        var data = {
            page_id: page_id,
            action: "vgt_edit_created_page"
        };

        jQuery.post(ajaxurl, data, function(response){

            var data = vgt_parse_json_output(response);

            localStorage.setItem(VGT_AR_CODE, data[VGT_AR_CODE]);
            localStorage.setItem(VGT_CUSTOM_CSS_CODE, data[VGT_CUSTOM_CSS_CODE]);
            localStorage.setItem(VGT_CUSTOM_JS_CODE, data[VGT_CUSTOM_JS_CODE]);
            localStorage.setItem(VGT_CUSTOM_JS_CODE_POSITION, data[VGT_CUSTOM_JS_CODE_POSITION]);
            localStorage.setItem(VGT_OUTER_BACKGROUND, data[VGT_OUTER_BACKGROUND]);
            localStorage.setItem(VGT_OUTER_BACKGROUND_TYPE, data[VGT_OUTER_BACKGROUND_TYPE]);
            localStorage.setItem(VGT_INNER_BACKGROUND, data[VGT_INNER_BACKGROUND]);
            localStorage.setItem(VGT_INNER_BACKGROUND_TYPE, data[VGT_INNER_BACKGROUND_TYPE]);
            localStorage.setItem(VGT_PAGE_ID, page_id);
            localStorage.setItem(VGT_CSS_CONTENT, data[VGT_CSS_CONTENT]);
            localStorage.setItem(VGT_PAGE_OUTER_ID, data[VGT_PAGE_OUTER_ID]);

            //restore the background image if any
            if (data[VGT_OUTER_BACKGROUND_TYPE] == "image")
            {
                jQuery("#site_area").css("background", "url("+data[VGT_OUTER_BACKGROUND]+") no-repeat");
                jQuery("#site_area").css("background-size", "cover");
            } else if (data[VGT_OUTER_BACKGROUND_TYPE] == "image_pattern")
            {
                jQuery("#site_area").css("background", "url("+data[VGT_OUTER_BACKGROUND]+")");
            }
            else if (data[VGT_OUTER_BACKGROUND_TYPE] == "color")
            {
                jQuery("#site_area").css("background", data[VGT_OUTER_BACKGROUND]);
            }

            //restore CSS
            var css_content = vgt_de_serialize_data(data[VGT_CSS_CONTENT]);
            var theme_content = vgt_de_serialize_data(data[VGT_PAGE_CONTENT]);

            jQuery("head").children("style.vgt_theme_css").remove();
            //set the class so it will be easier to remove later
            jQuery("<style class='vgt_theme_css'>"+css_content+"</style>").appendTo("head");

            //put content in site_area
            jQuery("#site_area").html(theme_content);

            //jQuery("#site_area *").not("a,li,h1,h2,h3,h4,h5,h6,p,input,button").contents().filter(function(){	return (this.nodeType == 3); }).wrap("<span class='editable'></span>");
            //jQuery("#site_area h1, #site_area h2, #site_area h3, #site_area h4, #site_area h5, #site_area h6, #site_area p, #site_area ul").addClass("editable");

            //remove blanks spans
            jQuery(".editable").filter(function(){return ((jQuery.trim(jQuery(this).text())).length == false);}).remove();

            jQuery("#page_title").val(data["page_title"]);

            //show the publish button
            jQuery("#publishb").fadeIn();

            vgt_wpl_enable_tinymce();

        });

    });

    /* SELECT TEMPLATE */

    /* SELECT TEMPLATE */
    /* SELECT TEMPLATE */

    /* SELECT TEMPLATE */

    /* SELECT TEMPLATE */

    /* SELECT TEMPLATE */

    /* SELECT TEMPLATE */

    //ENABLER
    jQuery(document).on("click", "#spring_submit", function(){

        var data = {
            action: "vgt_enable_plugin",
            email: jQuery("#spring_code").val(),
            receipt: jQuery("#spring_receipt").val()
        }

        jQuery.post(ajaxurl, data, function(response){
            var message = vgt_parse_json_output(response);
            vgt_general_notification("info", message.message, 5);
            console.log(message);

            if (message.message == "Activation Successful")
            {
                setTimeout(function(){location.reload()}, 2000);
            }


        });

    });


    //SETTING PAGE
    //save tracking code function
    jQuery(document).on("click", "#vgt_settings_save_tracking", function(){
        var code = jQuery("#vgt_settings_tracking_code").val();

        var data = {
            action: "vgt_save_options",
            option_value : code,
            option_name : "vgt_custom_tracking_code"
        }

        jQuery.post(ajaxurl, data, function(response){
            var message = vgt_parse_json_output(response);
            console.log(message);
            vgt_general_notification("info", message.message, 3);
        });



    });









});