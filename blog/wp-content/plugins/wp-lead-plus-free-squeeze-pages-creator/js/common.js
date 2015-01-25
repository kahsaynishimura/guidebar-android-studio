
/*  ALL LOCALSTORAGE VARIABLES
 * localStorage.setItem("current_button_link_id", btn_id); //record current clicked button
 * localStorage.setItem("vgt_page_id", "0"); //use when load template, post ID, popup/widget ID, used to decide whether to update or create new post/widget/popup
 * localStorage.setItem("vgt_page_outer_id", ""); //the unique outer ID of the page/pop/wid, used mainly to show popup
 *
 * this variable will be reset when: load a new template, edit a created popup/widget
 * localStorage.setItem("vgt_custom_js_code");
 * localStorage.setItem("vgt_custom_js_code_position");
 * localStorage.setItem("vgt_custom_css_code");
 * localStorage.setItem("vgt_autoresponder_code");
 * vgt_page_type //type of page (squeeze, popup, widget)
 *  */


/*
 localStorage variables for squeeze pages only
 "vgt_outer_background"
 "vgt_inner_background"
 "vgt_outer_background_type" (solid/pattern/img)
 "vgt_inner_background_type" (solid/pattern)

 These value will be reset to "" on template load/edit
 /*
 var VGT_PAGE_OUTER_ID =  "vgt_page_outer_id";
 var VGT_POPUP_WIDGET_CODE = "popup_widget_code";
 var VGT_CSS_CONTENT = "vgt_css_content";
 var VGT_CUSTOM_CSS_CODE = "vgt_custom_css_code";
 var VGT_CUSTOM_JS_CODE = "vgt_custom_js_code";
 var VGT_CUSTOM_JS_CODE_POSITION = "vgt_custom_js_code_position";

 var VGT_AR_CODE = "vgt_ar_code";

 var VGT_PAGE_CONTENT = "page_content";
 var VGT_ITEM_TITLE = "page_title";
 var VGT_ITEM_TYPE = "item_type";
 var VGT_PAGE_CONTENT = "page_content";
 var VGT_INNER_BACKGROUND = "inner_background";
 var VGT_INNER_BACKGROUND_TYPE = "inner_background_type";
 var VGT_OUTER_BACKGROUND = "outer_background";
 var VGT_OUTER_BACKGROUND_TYPE = "outer_background_type";
 var VGT_PAGE_ID = "page_id";
 */
    var VGT_PAGE_OUTER_ID =  "vgt_page_outer_id";
    var VGT_POPUP_WIDGET_CODE = "popup_widget_code";
    var VGT_CSS_CONTENT = "vgt_css_content";
    var VGT_CUSTOM_CSS_CODE = "vgt_custom_css_code";
    var VGT_CUSTOM_JS_CODE = "vgt_custom_js_code";
    var VGT_CUSTOM_JS_CODE_POSITION = "vgt_custom_js_code_position";

    var VGT_AR_CODE = "vgt_ar_code";

    var VGT_PAGE_CONTENT = "vgt_page_content";
    var VGT_ITEM_TITLE = "vgt_page_title";
    var VGT_ITEM_TYPE = "vgt_item_type";
    var VGT_PAGE_CONTENT = "vgt_page_content";
    var VGT_INNER_BACKGROUND = "vgt_inner_background";
    var VGT_INNER_BACKGROUND_TYPE = "vgt_inner_background_type";
    var VGT_OUTER_BACKGROUND = "vgt_outer_background";
    var VGT_OUTER_BACKGROUND_TYPE = "vgt_outer_background_type";
    var VGT_PAGE_ID = "vgt_page_id";
    var VGT_CURRENT_BUTTON_LINK_ID = "vgt_current_button_link_id";
    var VGT_CURRENT_SELECTED_ITEM = "vgt_current_selected_item";
    var AB_SQUEEZE_ID = "ab_squeeze_id";
/* ============================================================================================================

 --------------------------------------------.:^:.--------------------------------------------------

                        COMMON FUNCTIONS FOR ALL TYPES OF PAGE

 --------------------------------------------.:^:.--------------------------------------------------

 ============================================================================================================ */
    //SERIALIZE DATA
    function vgt_serialize_data(str) {
        return encodeURIComponent(BASE64.encode(str));
    }

    //DE-SERIALIZE DATA
    function vgt_de_serialize_data(str)
    {
        return BASE64.decode(decodeURIComponent(str));
    }
    //DELETE ITEMS
    jQuery(document).on("click", ".vgt_delete_item", function(){

        jQuery(this).parent(".vgt_single_list_item").fadeOut();
        var data = {
            action: "vgt_delete_item",
            item_id: jQuery(this).siblings("input").attr("item_id"),
            item_type: jQuery("#vgt_page_type").text()
        }

        jQuery.post(ajaxurl, data, function(response){


        });
    });


    function vgt_reset_all_localStorage()
    {
        localStorage.setItem(VGT_PAGE_ID, "0");
        localStorage.setItem(VGT_CSS_CONTENT, "");
        localStorage.setItem(VGT_CURRENT_BUTTON_LINK_ID, "");
        localStorage.setItem(VGT_CUSTOM_JS_CODE, "");
        localStorage.setItem(VGT_CUSTOM_JS_CODE_POSITION, "");
        localStorage.setItem(VGT_CUSTOM_CSS_CODE, "");
        localStorage.setItem(VGT_AR_CODE, "");
        localStorage.setItem(VGT_OUTER_BACKGROUND, "");
        localStorage.setItem(VGT_OUTER_BACKGROUND_TYPE, "");
        localStorage.setItem(VGT_INNER_BACKGROUND, "");
        localStorage.setItem(VGT_INNER_BACKGROUND_TYPE, "");
        localStorage.setItem(VGT_PAGE_OUTER_ID, "");
    }
//FUNCTION IN MANAGE POPUP WIDGET AND IN AB TESTING
    function vgt_get_all_categories(where_to_append)
    {

        //Load the list of all categories, append to the list of categories
        var data = {
            action: "vgt_get_all_categories"
        };

        jQuery.post(ajaxurl, data, function(response){
            var data = vgt_parse_json_output(response);
            var checkbox_list = "";

            for (var i = 0; i < data.length; i++)
            {
                checkbox_list += '<span class="list_of_categories"> <input name="categories" class="form-control" type="checkbox" alt_id="'+data[i].id+'" value='+ data[i].id +' />'+data[i].name+'</span>';
            }

            jQuery(where_to_append).append(checkbox_list);

        });
    }

    //get checked categories
    function vgt_get_checked_categories(list_categories_id)
    {
        var checked_array = [];
        jQuery(list_categories_id + " input[type=checkbox]").each(function(){
            if (jQuery(this).is(":checked"))
            {
                checked_array.push(jQuery(this).attr("alt_id"));
            }
        });

        return checked_array;
    }

    //check checked categories
    /*
    This function will loop through the list of categories, based on the list of categories checked (previously saved), it will check
    the categories again. Used on editing options
     */
    function vgt_check_checked_categories(categories_array, categories_div)
    {

        jQuery(categories_div + " input[type=checkbox]").each(function(){
            for (var i = 0; i < categories_div.length; i++)
            {

                if (jQuery(this).attr("value") == categories_array[i])
                {
                    jQuery(this).attr("checked", "checked");
                }

            }

        });
    }


    //define a constant to match the VGT_UNIQUE_WRAPER
    var VGT_UNIQUE_WRAPER = "vgt_unique_338742";


    //function to process return JSON DATA
    function vgt_parse_json_output(response)
    {

        var return_data = response.split(VGT_UNIQUE_WRAPER);

        var code;
        try {

            code = (decodeURIComponent(return_data[1]));
        } catch(e )
        {
            code = return_data[1];
        }
        
        

        return JSON.parse(code);
    }

    //function to check input on manage popup and widget
    function vgt_check_input_in_manage(selector)
    {
        //localStorage.setItem("vgt_input_manage_check_string", ""); will be set so the
        localStorage.setItem("vgt_input_manage_check_string", "");

        jQuery(selector).each(function(){
            if (jQuery.trim(jQuery(this).val()) == "" && jQuery(this).attr("id") != undefined && jQuery(this).attr("id").indexOf("_value") == -1)
            {
                jQuery(this).css("border", "1px dashed #ff0000");
                localStorage.setItem("vgt_input_manage_check_string", "stop");
                vgt_remove_attr(jQuery(this), "style", 2);

            }
        });

        if (localStorage.getItem("vgt_input_manage_check_string") == "stop")
        {
            localStorage.setItem("vgt_input_manage_check_string", "");
            return "stop";
        } else
        {
            //reset vgt_input_manage_check_string for next time check
            localStorage.setItem("vgt_input_manage_check_string", "");
            return "ok";
        }

    }


    jQuery(document).ready(function(){
        jQuery('[rel^=lightcase]').lightcase('init');
    });

    jQuery('#vgt_button_settings_tab a').click(function (e) {
        e.preventDefault();
        jQuery(this).tab('show');
    })

    //enable and disable resize
    jQuery(document).on("click","#vgt_enable_resize", function(){
        if (jQuery(this).val() == "Enable Resize")
        {
            jQuery("#site_area > div").resizable();
            jQuery(this).val("Disable Resize");
        } else
        {
            jQuery("#site_area > div").resizable("destroy");
            jQuery(this).val("Enable Resize");
        }

    });

    function sq_smart_toggle(id) {
        if (jQuery('#'+id).is(":visible")) {
            jQuery('#'+id).fadeOut();
        } else
        {
            jQuery('#'+id).fadeIn();
        }
    }

    jQuery(document).ready(function(){
        localStorage.setItem("totalClassText", "");
        localStorage.setItem("buttonAndLinkTag", "");

        //Load button styles
        var data = {
            action: "vgt_get_buttons"

        };

        jQuery.post(ajaxurl, data, function(response){
            data = vgt_parse_json_output(response);
            var style_code = "";

            for (var i = 0; i < data.styles.length; i ++)
            {

                style_code += '<button alt_class="'+data.styles[i]+'" class="'+ data.styles[i] +' green" >Hello</button>';
            }

            style_code = style_code;

            var color_code = "";
            for (var i = 0; i < data.colors.length; i ++)
            {
                color_code += '<button alt_class="vgt_btn_1" class="vgt_btn_1 '+ data.colors[i] +'" >Hello</button>';
            }

            jQuery("#vgt_styles").html(style_code);


            jQuery("#vgt_colors").html(color_code);

        });


        //hide the editor when the click is not on editable elements
        jQuery(document).on("click", "body *" ,function(){

            /*
            totalClassText: When the user clicks on the page, record all class of the clicked element. If the user clicks on
            the MCE editor (which has classes start with mce-), the MCE editor will not be called.

            The MCE editor will be called only on .editable or link/button/a/image

             buttonAndLinkTag: this variable is used to check if one of the clicked element is a button/a. If yes, the text
             vgt_link_or_button will be appended to the variable. Later on setTimeout function, it will be used to check the position
             of vgt_link_or_button in its string content. If there is vgt_link_or_button string in the buttonAndLinkTag variable,
             show the MCE editor
             */
            //record tag to disable inline editor on input
            if ( jQuery(this).is("a") ||  jQuery(this).is("input") ||  jQuery(this).is("button")  )
            {
                localStorage.setItem("buttonAndLinkTag", localStorage.getItem("buttonAndLinkTag") + "vgt_link_or_button" );
            } else

            if (jQuery(this).attr("class") != undefined)
            {
                localStorage.setItem("totalClassText", localStorage.getItem("totalClassText") + jQuery(this).attr("class") );
            }


            if (localStorage.getItem("totalClassText") == null)
            {
                localStorage.setItem("totalClassText", "");
            }


            setTimeout(function(){
                if (localStorage.getItem("totalClassText").indexOf("mce-") != -1)
                {
                    return false;
                } else if (localStorage.getItem("totalClassText").indexOf("editable") == -1)
                {
                    jQuery(".mce-tinymce-inline").fadeOut();
                    jQuery(".editable").blur();//remove the focus of editable (a bug of chrome)

                } else
                {
                    vgt_wpl_enable_tinymce();
                    console.log("enabled editor");
                }

                if ((localStorage.getItem("buttonAndLinkTag") != null) && localStorage.getItem("buttonAndLinkTag").indexOf("vgt_link_or_button") == -1)
                {
                    vgt_remove_tinymce_on_buttons_n_links();

                }

            }, 50);

            setTimeout(function() {
                localStorage.setItem("buttonAndLinkTag", "");
                localStorage.setItem("totalClassText", "");
            }, 200);
        });
    });

    //function to show then hide a div after a certain amount of second (4), mostly sq_bgt_general_notification
    function vgt_general_notification(type, message, appear_seconds)
    {

        if (type == "warning")
        {
            bg_color = "#ff4141";
        }
        else if (type == "info")
        {
            bg_color = "#41b5ff";
        }

        else if (type == "success")
        {
            bg_color = "#6bda19";
        }

        var text_color = "#fff";

        var notification_id = "sq_bgt_general_notification";
        jQuery('#'+ notification_id).css('background-color', bg_color);
        jQuery('#'+ notification_id).css('color', text_color);
        jQuery('#'+ notification_id).html(message);
        jQuery('#' + notification_id).slideDown();
        setTimeout(function(){ jQuery('#' + notification_id).fadeOut(); }, appear_seconds * 1000);
    }


    //function to remove attribute after a specific time
    function vgt_remove_attr(element, attribute, time)
    {
        setTimeout(function(){ element.removeAttr(attribute); }, time * 1000);
    }


//EDIT THE TEXT OF THE THEME
jQuery(document).ready(function(){
	
		//reset position button
		jQuery('#vgt_resetb').click(function(){
			jQuery('#sq_box_container').css("top", "");
			jQuery('#sq_box_container').css("left", "");
		});
	
		
	jQuery(document).on("click", ".editable", function(){
		jQuery("#vgt_button_settings_button").fadeOut();
		vgt_wpl_enable_tinymce();

		
	});
	//END EDITING THE TEXT OF THE THEME

	jQuery(document).on("click", ".editable", function(){
		
		vgt_remove_tinymce_on_buttons_n_links();
	});
	
	jQuery(document).on("click", "#site_area", function(){
		setTimeout(function(){
			if (jQuery(".mce-tinymce-inline").is(":visible"))
			{
				vgt_remove_tinymce_on_buttons_n_links();
			}
			
		}, 10);
	});
	//editing the submit button
	jQuery(document).on("click", "#site_area a, #site_area input[type=submit], #site_area input[type=button] , #site_area button, #site_area input[type=image], #site_area input[type=text], #site_area input[type=email], #site_area input[type=number]", function(e){
		//insert a context editor near the button, if not exists already
		
		//show the button settings button if the element got clicked is a button
		if ( jQuery(this).is("button") || jQuery(this).is("input[type=button]") || jQuery(this).is("input[type=submit]") || jQuery(this).is("input[type=image]") )
		{
			jQuery("#vgt_button_settings_button").fadeIn();
            jQuery("#vgt_button_background_color").val("#000002"); //set this value, should be unique, so if the user doesn't set the value
            //for button's color, the color will not apply
		} else {
			jQuery("#vgt_button_settings_button").fadeOut();
		}
			
		jQuery("#crazy_vgt").append("<div id='button_editor'></div>");


        vgt_enable_tinymce_on_links_n_buttons();

		//get current position of the button/link, then append the editor below that
		var elem_offset = jQuery(this).offset();
		var elem_offset_top 	= elem_offset.top;
		var elem_offset_left 	= elem_offset.left;
		var elem_height			= jQuery(this).outerHeight();


		jQuery("#crazy_vgt").children(".mce-tinymce").offset({top: elem_offset_top + elem_height, left: elem_offset_left});

        jQuery("#crazy_vgt").children(".mce-tinymce").css("z-index", 90);
		


		//add an ID to the current button, if not exists

		if (jQuery(this).attr("id") == undefined)
		{
			//generate a random number to be the id of the button
			var btn_id = "id" + Math.round(Math.random()*1000000);
			jQuery(this).attr("id", btn_id);
		} else
		{
			btn_id = jQuery(this).attr("id");
		}
		
		//log current button id
        localStorage.setItem(VGT_CURRENT_BUTTON_LINK_ID, btn_id);




		//get the current editor
		var button_editor = tinyMCE.get("button_editor");
		var selected = jQuery(this);
		
		if (selected.is("input"))
		{
			//get the current style of button's text
			var btn_size 			= selected.css("font-size");
			var btn_color 			= selected.css("color");
			var btn_font_style 		= selected.css("font-style");
			var btn_font_weight 	= selected.css("font-weight");
			var btn_text_decoration = selected.css("text-decoration");


            var text = "";

            if (jQuery(this).attr("value") != "")
            {
                text = jQuery(this).attr("value");
            } else if (jQuery(this).attr("placeholder") != "")
            {
                text = jQuery(this).attr("placeholder");
            } else
            {
                text = "set your text";
            }

			
			var pass_to_editor_content = "<span style='font-size: "+btn_size+"; color: "+btn_color+"; font-style: "+btn_font_style+"; font-weight: "+btn_font_weight+"; text-decoration: "+btn_text_decoration+";'>"+text+"</span>";

			
		} else if (selected.is("a"))
		{
			var a_size 				= selected.css("font-size");
			var a_color 			= selected.css("color");
			var a_font_style 		= selected.css("font-style");
			var a_font_weight 		= selected.css("font-weight");
			var a_text_decoration 	= selected.css("text-decoration");
			var a_val 				= selected.attr("href");
			var a_target			= selected.attr("target");
			
			var pass_to_editor_content = "<a target='"+ a_target +"' href='"+ a_val +"' style='font-size: "+a_size+"; color: "+a_color+"; font-style: "+a_font_style+"; font-weight: "+a_font_weight+"; text-decoration: "+a_text_decoration+";>"+ selected.text() +"</a>";
		} else if (selected.is("button"))
        {
            var text_size 		= selected.css("font-size");
            var text_color 		= selected.css("color");
            var text_style 		= selected.css("font-style");
            var font_weight 	= selected.css("font-weight");
            var text_decoration = selected.css("text-decoration");
            var text_value 		= selected.text();
            var pass_to_editor_content = "<span style='font-size: "+text_size+"; color: "+text_color+"; font-style: "+text_style+"; font-weight: "+font_weight+"; text-decoration: "+text_decoration+";'>"+text_value+"</span>";

        }

		button_editor.setContent(pass_to_editor_content);

		return false;
	});

	//add ID and record ID when .editable/li/a is clicked
	jQuery(document).on("click", "#site_area a, #site_area li, #site_area .editable", function(){
		if (jQuery(this).attr("id") == undefined)
		{
			//generate a random id
			var rid = "rid"+ Math.round(Math.random()*2000000);
			jQuery(this).attr("id", rid);
		}
		
		localStorage.setItem(VGT_CURRENT_SELECTED_ITEM, jQuery(this).attr("id"));
		
	});


	
	//show and hide panels
	jQuery("#codeb").click(function(){
		jQuery(".vgt_vertical_menu").fadeOut();
		sq_smart_toggle('code_panel')
	});
	
	//edit button
	jQuery("#editb").click(function(){
		jQuery(".vgt_vertical_menu").fadeOut();
		sq_smart_toggle("editing_panel");
	});
	
	jQuery('#editorb').click(function(){
		jQuery(".vgt_vertical_menu").fadeOut();
		sq_smart_toggle('editor_control_panel');		
		
	});
	
		//open the gallery to select a template
	jQuery("#selectb").click(function(){
            jQuery(".vgt_gallery").fadeOut();
			jQuery(".vgt_vertical_menu").fadeOut();
			sq_smart_toggle("gallery_panel");
	});
		/*
		 * THIS SECTION IS FOR THE NEW VERSION. ALL NEW CODE IS WRITTEN BELOW
		 * THIS SECTION IS FOR THE NEW VERSION. ALL NEW CODE IS WRITTEN BELOW 
		 *  THIS SECTION IS FOR THE NEW VERSION. ALL NEW CODE IS WRITTEN BELOW
		 *  THIS SECTION IS FOR THE NEW VERSION. ALL NEW CODE IS WRITTEN BELOW
		 *  THIS SECTION IS FOR THE NEW VERSION. ALL NEW CODE IS WRITTEN BELOW
		 *  THIS SECTION IS FOR THE NEW VERSION. ALL NEW CODE IS WRITTEN BELOW
		 *  THIS SECTION IS FOR THE NEW VERSION. ALL NEW CODE IS WRITTEN BELOW
		 *  */

        //Display code boxes
        jQuery("#code_custom_css_btn").click(function(){

            jQuery("#vgt_custom_css_code_div").modal({fadeDuration: 500, clickClose: true});

        });

        jQuery("#code_custom_js_btn").click(function(){

            //hide the position selector in popup and widget pages
            if (jQuery("#vgt_page_type").text() != "squeeze")
            {
                jQuery("#custom_javascript_position").fadeOut();
                jQuery("#custom_javascript_position").siblings("h4").fadeOut();
            }

            jQuery("#vgt_custom_javascript_code_div").modal({fadeDuration: 500, clickClose: true});

        });


        jQuery("#code_mediab").click(function(){

            jQuery("#vgt_media_code_div").modal({fadeDuration: 500, clickClose: true});

        });


        jQuery("#code_emailb").click(function(){

            jQuery("#vgt_email_code_div").modal({fadeDuration: 500, clickClose: true});

        });


        //BACKGROUND BOXES
        jQuery("#vgt_outer_background").click(function(){
            jQuery(".bg_value").hide();

            jQuery("#vgt_outer_background_div").modal({fadeDuration: 500, clickClose: false});

        });

        jQuery("#vgt_inner_background").click(function(){
            jQuery(".bg_value").hide();

            jQuery("#vgt_inner_background_div").modal({fadeDuration: 500, clickClose: false});

        });

        jQuery(document).on("change", "#vgt_inner_background_div select, #vgt_outer_background_div select", function(){
                var for_value = jQuery(this).val() + "_value";

                if ( jQuery("[for="+for_value+"]").length > 0 )
                {
                    jQuery(this).siblings(".bg_value").hide();
                    jQuery("[for="+for_value+"]").parent().fadeIn();
                }

            });
        //END BACKGROUND BOXES



        jQuery(".code_box_close").click(function(){

            jQuery.modal.close();
        });

        //save button behavior
        jQuery(".code_box_save").click(function(){

            jQuery.modal.close();

            var code = jQuery(this).parent().siblings("textarea").val();
            var for_code = jQuery(this).attr("for-code");

            //if the code is CSS
            if (for_code == "css")
            {
                vgt_process_css_code(code);
            }

            //if the code is JS
            else if (for_code == "javascript")
            {
                var code_position = jQuery("#custom_javascript_position").val();
                vgt_process_js_code(code, code_position);
            }

            //if the code is HTML
            else if (for_code == "html")
            {
                vgt_process_html_code(code);
            }

            //if the code is autoresponder
            else if (for_code == "autoresponder")
            {
                vgt_process_autoresponder_code(code, "#site_area");
            }

            //if the code is media
            else if (for_code == "media")
            {
                vgt_process_media_code(code, "#site_area #media_box");
            }

            //if the code is outer background
            else if (for_code == "outer_bg")
            {

                var bg_type = jQuery(this).parent().siblings("select").val();

                var bg_value = jQuery(this).parent().siblings().children("[for="+bg_type+"_value]").val();

                vgt_process_outer_background_code(bg_type, bg_value);
            }

            else if (for_code == "inner_bg")
            {

                var bg_type = jQuery(this).parent().siblings("select").val();

                var bg_value = jQuery(this).parent().siblings().children("[for="+bg_type+"_value]").val();

                vgt_process_inner_background_code(bg_type, bg_value);

            }



        });


        //BACKGROUND BOXES


		//SELECTING A TEMPLATE
		jQuery("#withOptin").click(function(){
			jQuery(".vgt_gallery").fadeOut();
            jQuery("#nooptin_gallery").fadeOut();
			jQuery("#optin_gallery").fadeIn();
			jQuery("#vgt_gallery").fadeIn();
		});
		
		jQuery("#noOptin").click(function(){
            jQuery(".vgt_gallery").fadeOut();
			jQuery("#optin_gallery").fadeOut();
			jQuery("#nooptin_gallery").fadeIn();
			jQuery("#vgt_gallery").fadeIn();			
		});
		
		//response to radio-button click
		jQuery("#vgt_gallery input[type=radio]").click(function(){

            jQuery(".vgt_edit_btn").hide();
            jQuery("#editthisb").fadeIn();
			
		});
		
		//response to Edit this button clicked, this is when a new template is loaded, reset all localStorage values, also, codes in textarea
		jQuery("#editthisb").click(function(){
			jQuery(this).fadeOut();

            //clear current title
            jQuery("#pw_item_name, #page_title").val("");

			jQuery("#optin_gallery, #nooptin_gallery, #vgt_gallery, #gallery_panel").fadeOut();
			//get the selected theme
			var theme_id = jQuery('#vgt_gallery input[type="radio"]:checked').attr("theme_id");
			var theme_type = jQuery('#vgt_gallery input[type="radio"]:checked').attr("theme_type");
			var page_type = jQuery("#vgt_page_type").text();
			var data = {
					action: 'theme_loader',
					theme_id: theme_id,
					theme_type: theme_type,
					page_type: page_type
				};
			//processing the return data
			
			jQuery.post(ajaxurl, data, function(response){
                //reset all localStorage variables
                vgt_reset_all_localStorage();


				var return_array = vgt_parse_json_output(response);
				//decode the return values
				var css_content = vgt_de_serialize_data(return_array[VGT_CSS_CONTENT]);
				var theme_content = vgt_de_serialize_data(return_array[VGT_PAGE_CONTENT]);
				
				jQuery("head").children("style.vgt_theme_css").remove();
				//set the class so it will be easier to remove later
				jQuery("<style class='vgt_theme_css'>"+css_content+"</style>").appendTo("head");
					
				//put content in site_area
				jQuery("#site_area").html(theme_content);

                jQuery("#site_area *").not("a,h1,h2,h3,h4,h5,h6,p,input,button").contents().filter(function(){	return (this.nodeType == 3); }).wrap("<span class='editable'></span>");

                jQuery("#site_area h1, #site_area h2, #site_area h3, #site_area h4, #site_area h5, #site_area h6, #site_area p, #site_area ul").addClass("editable");
				
				//remove blanks spans
				jQuery(".editable").filter(function(){return ((jQuery.trim(jQuery(this).text())).length == false);}).remove();

				//show the publish button
				jQuery("#publishb").fadeIn();
                jQuery("#pwpublishb").fadeIn(); //for popup and widget
				vgt_wpl_enable_tinymce();

/*
                //add id to all buttons, link if not available
                jQuery("#site_area a, #site_area button, #site_area input").each(function(){
                    if (jQuery(this).attr("id") == undefined)
                    {
                        var random_id = "rid_" + Math.round(Math.random() * 999999);
                        jQuery(this).attr("id", random_id);

                    }

                });
*/
                //store the theme CSS content in base64 format, this string will be posted to server when saving the template
                localStorage.setItem(VGT_CSS_CONTENT, return_array[VGT_CSS_CONTENT]);

                //set the page_outer ID to the unique ID
                localStorage.setItem(VGT_PAGE_OUTER_ID, return_array[VGT_PAGE_OUTER_ID]);

			});
			
		});

		//SETTINGS FOR BUTTONS AND INPUTS
		jQuery("#vgt_button_settings_button").click(function(){
            //send ajax request to get the list of popup options, in case the user select the action of the button to show all the popups

            var data = {
                action: "vgt_get_popup_widget_options",
                type: "popup"
            };
            jQuery.post(ajaxurl, data, function(response){
                var options_array = vgt_parse_json_output(response);

                jQuery("#vgt_hidden_advanced_settings").fadeIn();
                jQuery("#vgt_button_settings").fadeIn();

                var options = "<option value=''></option>";
                for (var i = 0; i < options_array.length; i ++)
                {
                    options += '<option value="'+options_array[i].id+'">'+vgt_de_serialize_data(options_array[i].title)+'</option>';
                }

                jQuery("#vgt_popup_to_display").html("");
                jQuery("#vgt_popup_to_display").append(options);
            });
		});
		
		//close the button and link settings box
		jQuery(".vgt_close_btn").click(function(){
			jQuery("#vgt_hidden_advanced_settings").fadeOut();
		});
		
		//response to button behavior selection
		jQuery(document).on("change","#vgt_button_behavior",function(){
			var value = jQuery(this).val();
			console.log(value);
			
			if (value == "open_link")
			{
				jQuery("#vgt_popup_to_display").parent().fadeOut();
				jQuery("#vgt_url_to_open").fadeIn();
				
			} else if (value == "open_popup")
			{
				jQuery("#vgt_url_to_open").fadeOut();
				jQuery("#vgt_popup_to_display").parent().fadeIn();
				
			} else
			{
				jQuery("#vgt_url_to_open").fadeOut();
				jQuery("#vgt_popup_to_display").parent().fadeOut();
			}
			
		});

		//save settings for button
		jQuery("#vgt_save_button_settings").click(function(){
			jQuery("#vgt_hidden_advanced_settings").fadeOut();
			
			//get the id of current selected button
			var button_id = localStorage.getItem(VGT_CURRENT_BUTTON_LINK_ID);

            jQuery("#"+button_id).attr("vgt-action", "");

            if (jQuery("#vgt_button_behavior").val() == "open_link")
            {
                jQuery("#"+button_id).attr("vgt-action", "open-link");
                jQuery("#"+button_id).attr("vgt-action-value", jQuery("#vgt_url_to_open_value").val());

                if (jQuery("input[name=vgt_new_window]").is(":checked"))
                {
                    jQuery("#"+button_id).attr("vgt-new-window", "true");
                }

            } else if (jQuery("#vgt_button_behavior").val() == "open_popup")
            {
                jQuery("#"+button_id).attr("vgt-action", "open-popup");
                jQuery("#"+button_id).attr("vgt-action-value", jQuery("#vgt_popup_to_display").val());

                //add unique class, make it easier to trigger the popup later
                jQuery("#"+button_id).attr("vgt-popup-trigger-id", "vgt-trigger-"+jQuery("#vgt_popup_to_display").val());

            }

            //apply button style to selected button
            if (localStorage.getItem("vgt_selected_button_style") != "")
            {
                //remove current class first
                jQuery("#"+button_id).removeAttr("class");

                jQuery("#"+button_id).addClass(localStorage.getItem("vgt_selected_button_style"));

                //reset button style
                localStorage.setItem("vgt_selected_button_style", "");
            }


			
		});

    //POPUP AND WIDGET COMMON FUNCTIONS



    //GET POPUP/WIDGET CODE, save the code to db
    jQuery('#pwpublishb').click(function(){

        if (jQuery.trim(jQuery('#site_area').html()) == false) {
            return;
        }
        if (jQuery.trim(jQuery("#pw_item_name").val()) == "")
        {

            vgt_general_notification('warning', "Name required!", 4);
            jQuery("#pw_item_name").css("background", "#ffd3d3");

            setTimeout(function(){
                jQuery("#pw_item_name").css("background", "");

            }, 2000);

            return false;
        }



        //add class vgt_popup to the popup, this will be used to replace position class later
        jQuery("#site_area > div").addClass("vgt_popup");

        var page_content = vgt_serialize_data( jQuery('#site_area').html() );
        var item_name = vgt_serialize_data(jQuery.trim( jQuery('#pw_item_name').val()));

        var data = {
            action			    : 'popup_widget_save_to_db',
            vgt_page_content    : page_content,
            vgt_item_type		: jQuery("#vgt_page_type").text() ,
            vgt_page_title		: item_name,
            vgt_css_content	    : localStorage.getItem(VGT_CSS_CONTENT),
            vgt_custom_css_code	: localStorage.getItem(VGT_CUSTOM_CSS_CODE),
            vgt_custom_js_code	: localStorage.getItem(VGT_CUSTOM_JS_CODE),
            vgt_page_id         : localStorage.getItem(VGT_PAGE_ID),
            vgt_page_outer_id   : localStorage.getItem(VGT_PAGE_OUTER_ID),
            vgt_ar_code         : localStorage.getItem(VGT_AR_CODE)
        };

        //send the ajax call to server
        jQuery.post(ajaxurl, data, function(response){
            var return_data = vgt_parse_json_output(response);

            //update item_id to the new ID
            localStorage.setItem(VGT_PAGE_ID, return_data["item_id"]);
            vgt_general_notification('success', return_data["message"], 4);


        });
    });

    //SHOW LIST OF POPUP/WIDGET TO EDIT
    jQuery(document).on("click", "#vgt_pwedit", function(){
        var type = jQuery("#vgt_page_type").text();

        var data = {
            type: type,
            action: "vgt_popup_widget_get_created"
        };

        jQuery.post(ajaxurl, data, function(response){
            var return_data = response.split(VGT_UNIQUE_WRAPER);
            jQuery(".vgt_gallery").hide();
            jQuery("#vgt_created_item_gallery").html(return_data[1]);
            jQuery("#vgt_created_item_gallery").fadeIn();

        });

    });

    //show the Edit this button when user clicks on the radio button
    jQuery(document).on("click", "input[name=vgt_created_item]", function(){
        jQuery(".vgt_edit_btn").hide();//hide other edit buttons
        jQuery("#pwedit_created_item_b").fadeIn();
        jQuery("#editthispageb").fadeIn(); //for editing squeeze page
    });

    jQuery(document).on("click", "#pwedit_created_item_b", function(){

        //clear localStorage variables
        localStorage.clear();
        localStorage.setItem("totalClassText", "");
        localStorage.setItem("buttonAndLinkTag", "");

        jQuery(this).fadeOut();
        jQuery(".vgt_gallery").hide();

        var item_id = jQuery("input[name=vgt_created_item]:checked").attr("item_id");
        var data = {
            item_id     : item_id,
            action      : "vgt_popup_widget_load_created_item"
        };

        //set  to id of the item
        jQuery.post(ajaxurl, data, function(response){

            //set the title of the popup/widget
            jQuery("#pw_item_name").val(jQuery("input[name=vgt_created_item]:checked").siblings(".vgt_single_item_name").text());

            //record the id so the next time user press save button, the post will be updated, not insert to db
            localStorage.setItem(VGT_PAGE_ID, item_id);
            var return_array = vgt_parse_json_output(response);

            console.log(return_array);
            //remove current style sheet on the header
            //decode the return values
            var css_content = vgt_de_serialize_data(return_array[VGT_CSS_CONTENT]);

            var theme_content = vgt_de_serialize_data(return_array['popup_widget_code']);

            localStorage.setItem(VGT_CSS_CONTENT, return_array[VGT_CSS_CONTENT]);
            localStorage.setItem(VGT_PAGE_OUTER_ID, return_array[VGT_PAGE_OUTER_ID]);
            localStorage.setItem(VGT_CUSTOM_CSS_CODE, return_array[VGT_CUSTOM_CSS_CODE]);
            localStorage.setItem(VGT_CUSTOM_JS_CODE, return_array[VGT_CUSTOM_JS_CODE]);
            localStorage.setItem(VGT_AR_CODE, return_array[VGT_AR_CODE]);


            jQuery("head").children("style.vgt_theme_css").remove();
            //set the class so it will be easier to remove later
            jQuery("<style class='vgt_theme_css'>"+css_content+"</style>").appendTo("head");

            //put content in site_area
            jQuery("#site_area").html(theme_content);

            jQuery("#site_area *").not("a,li,h1,h2,h3,h4,h5,h6,p,input,button").contents().filter(function(){	return (this.nodeType == 3); }).wrap("<span class='editable'></span>");
            jQuery("#site_area h1, #site_area h2, #site_area h3, #site_area h4, #site_area h5, #site_area h6, #site_area p, #site_area ul").addClass("editable");

            //remove blanks spans
            jQuery(".editable").filter(function(){return ((jQuery.trim(jQuery(this).text())).length == false);}).remove();

            //show the publish button
            jQuery("#publishb").fadeIn();
            jQuery("#pwpublishb").fadeIn(); //for popup and widget
            vgt_wpl_enable_tinymce();

        });


    });



    //COMMON FUNCTIONS FOR POPUP AND WIDGET ONLY
    //GET SHORTCODE
    jQuery(document).on("click", "#vgt_get_shortcode", function(){

        if (jQuery("#vgt_popup_manager").length > 0)
        {
            jQuery("#vgt_shortcode").text("[wpl_show_popup option_id="+localStorage.getItem("vgt_current_popup_option_id")+"]");
        } else if (jQuery("#vgt_widget_manager").length > 0)
        {
            jQuery("#vgt_shortcode").text("[wpl_show_widget option_id="+localStorage.getItem("vgt_current_widget_option_id")+"]");
        } else if (jQuery("#vgt_ab_left").length > 0)
        {
            if (jQuery("#select_page_type").val() == "popup")
            {
                jQuery("#vgt_shortcode").text("[wpl_ab_popup_test option_id="+localStorage.getItem("vgt_ab_id")+"]");

            } else if (jQuery("#select_page_type").val() == "widget")
            {
                jQuery("#vgt_shortcode").text("[wpl_ab_widget_test option_id="+localStorage.getItem("vgt_ab_id")+"]");

            } else if (jQuery("#select_page_type").val() == "squeeze")
            {
                jQuery("#vgt_shortcode").text("[wpl_ab_squeeze_test option_id="+localStorage.getItem("vgt_ab_id")+"]");

            }

        }

    });

    //DELETE OPTION
    jQuery(document).on("click", "#vgt_delete_option", function(){

        var item_id = jQuery("#vgt_list_of_options").val();
        var data = {
            action: "vgt_delete_option",
            item_id: item_id

        };

        jQuery.post(ajaxurl, data, function(response){

            jQuery("#vgt_list_of_options option[value="+item_id+"]").remove();
            jQuery("#vgt_popup_manager select, #vgt_popup_manager input, #vgt_widget_manager select, #vgt_widget_manager input").val("");
        });
    });


    //BUTTON COLORS
    jQuery(document).on("click", "#vgt_styles button", function(){
        var style_to_apply = jQuery(this).attr("alt_class");

        jQuery(".vgt_tick").remove();

        var style_to_replace = jQuery("#vgt_colors button:first").attr("alt_class");

        jQuery("#vgt_colors button").removeClass(style_to_replace);
        jQuery("#vgt_colors button").addClass(style_to_apply);
        jQuery("#vgt_colors button").attr("alt_class", style_to_apply);

        jQuery("#vgt_colors").fadeIn();

    });

    //SELECT A BUTTON A MARK IT AS SELECTED
    jQuery(document).on("click", "#vgt_colors button", function(){

        jQuery(".vgt_tick").remove();
        jQuery(this).after('<span class="vgt_tick"></span>');

        var btn_offset = jQuery(this).offset();

        var height = jQuery(this).outerHeight();
        var width = jQuery(this).outerWidth();
        jQuery(".vgt_tick").offset({top: btn_offset.top + height - 16, left: btn_offset.left + width - 16 });

        //save the class
        localStorage.setItem("vgt_selected_button_style", jQuery(this).attr("class"));

    });

    //ADD AND REMOVE ITEMS
    /*
    Add and remove works on clonnable and list items only

     */
    jQuery(document).on("click", ".vgt_clonnable, li", function(){

        if (jQuery(this).attr("id") != undefined)
        {
            localStorage.setItem("vgt_clonnable_id", jQuery(this).attr("id"));
        } else
        {
            var random_id = "rand_" + Math.round(Math.random() *29999232);
            jQuery(this).attr("id", random_id);
            localStorage.setItem("vgt_clonnable_id", random_id);
        }
    });

    jQuery(document).on("click", "#vgt_addb", function(){
        if (localStorage.getItem("vgt_clonnable_id") != null)
        {
            var clonner = jQuery("#" + localStorage.getItem("vgt_clonnable_id")).clone();
            console.log(clonner);
            clonner.children().each(function(){
                if (jQuery(this).attr("id") != undefined)
                {
                    var random_id = "rand_" + Math.round(Math.random() *29999232);
                    jQuery(this).attr("id", random_id);
                }

            });

           //insert the new element after current element
            clonner.insertAfter(jQuery("#" + localStorage.getItem("vgt_clonnable_id")));


        }

    });


    jQuery(document).on("click", "#vgt_removeb", function(){
        if (localStorage.getItem("vgt_clonnable_id") != null)
        {
            jQuery("#" + localStorage.getItem("vgt_clonnable_id")).remove();

        }

    });
});