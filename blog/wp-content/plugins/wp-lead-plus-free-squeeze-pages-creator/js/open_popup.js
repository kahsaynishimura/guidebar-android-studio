//define a constant to match the VGT_UNIQUE_WRAPER
var VGT_UNIQUE_WRAPER = "vgt_unique_338742";


//function to process return JSON DATA
function vgt_parse_json_output(response)
{
    var return_data = response.split(VGT_UNIQUE_WRAPER);

    return jQuery.parseJSON(return_data[1]);
}


function vgt_show_close_button(outer_id)
{

    jQuery(outer_id).after('<span class="vgt_close_popup"></span>');
    var box_offset = jQuery(outer_id).offset();
    var height = jQuery(outer_id).outerHeight();
    var width = jQuery(outer_id).outerWidth();
    jQuery(".vgt_close_popup").offset({top: box_offset.top + 5, left: box_offset.left + width - 25 });
    jQuery(".vgt_close_popup").css("z-index", "99999999999999999");
}

function vgt_position_popup(outer_id)
{
    setTimeout(function(){
        //styling for center classes
        var top_width = jQuery(outer_id + ".vgt_top_center").outerWidth();
        var bottom_width = jQuery(outer_id + ".vgt_bottom_center").outerWidth();

        var vgt_screen_height = jQuery(outer_id + ".vgt_screen_center").outerHeight();
        var vgt_screen_width = jQuery(outer_id + ".vgt_screen_center").outerWidth();


        jQuery(outer_id + ".vgt_top_center").css("margin-left", -top_width/2);
        jQuery(outer_id + ".vgt_bottom_center").css("margin-left", -bottom_width/2);

        jQuery(outer_id + ".vgt_screen_center").css("margin-top",  -vgt_screen_height/2);
        jQuery(outer_id + ".vgt_screen_center").css("margin-left", -vgt_screen_width/2);

        jQuery(outer_id).css("z-index", "9999999999999");

    }, 50);


}

function vgt_js_popup_trigger(frequency, vgt_unique_outer_id, effect) //the frequency variable tells the frequency (once/all time). The function will check with a
//localStorage varaible to decide whether to show the popup or not
{
    var popup_showed = localStorage.getItem("vgt_popup_showed");

    //if the close button got clicked in that session (before the page reload again, don't show the popup)
    if (localStorage.getItem("vgt_close_button_clicked") == "1")
    {
        console.log("close button was clicked");
        return false;
    }

    if (jQuery("#"+ vgt_unique_outer_id).parent().is(":visible"))
    {
        console.log("popup is visible");
        return;
    }
    console.log(frequency);
    if (frequency == "once" && popup_showed == "1")
    {
        return false;
    }
    vgt_position_popup("#"+ vgt_unique_outer_id);

    jQuery("#"+ vgt_unique_outer_id).hide();
    jQuery("#"+ vgt_unique_outer_id).parent().fadeIn();


    jQuery("#"+ vgt_unique_outer_id).effect("slide", 500, function(){

        vgt_show_close_button("#"+ vgt_unique_outer_id);
    });


    /*
    if (effect == "slide_down")
    {

        jQuery("#"+ vgt_unique_outer_id).slideDown(500, function(){

            vgt_show_close_button("#"+ vgt_unique_outer_id);

        });

    } else if (effect == "slide_up")
    {

        jQuery("#"+ vgt_unique_outer_id).slideUp(500, function(){
            vgt_show_close_button("#"+ vgt_unique_outer_id);


        });

    } else if (effect == "bounce")
    {
        jQuery("#"+ vgt_unique_outer_id).effect("bounce", {}, 500, function(){
            vgt_show_close_button("#"+ vgt_unique_outer_id);

        });
    } else if (effect == "shake")
    {
        jQuery("#"+ vgt_unique_outer_id).effect("shake", {}, 500, function(){
            vgt_show_close_button("#"+ vgt_unique_outer_id);

        });
    } else if (effect == "pulsate")
    {
        jQuery("#"+ vgt_unique_outer_id).effect("pulsate", {}, 500, function(){
            vgt_show_close_button("#"+ vgt_unique_outer_id);

        });
    } else if (effect == "puff")
    {
        jQuery("#"+ vgt_unique_outer_id).effect("puff", {}, 500, function(){
            vgt_show_close_button("#"+ vgt_unique_outer_id);

        });
    } else if (effect == "highlight")
    {
        jQuery("#"+ vgt_unique_outer_id).effect("highlight", {}, 500, function(){
            vgt_show_close_button("#"+ vgt_unique_outer_id);

        });
    }

    */
    //TRACKING
    var my_ajax_url = jQuery(".vgt_ajax_url").text();
    var item_type = jQuery("[for_item="+vgt_unique_outer_id+"]").attr("item_type");
    var item_id = jQuery("[for_item="+vgt_unique_outer_id+"]").attr("item_id");
    var option_id = jQuery("[for_item="+vgt_unique_outer_id+"]").attr("option_id");
    var ab_test_id = jQuery("[for_item="+vgt_unique_outer_id+"]").attr("ab_id") == undefined ? "0" : jQuery("[for_item="+vgt_unique_outer_id+"]").attr("ab_id");

    //vgt_tracking_send_data(my_ajax_url, "click", item_id, item_type, option_id, clicked_element_tag, clicked_element_id, clicked_element_text, ab_test_id);
    vgt_tracking_send_data(my_ajax_url, "view", item_id, item_type, option_id, "", "", "", ab_test_id);

}


function vgt_js_show_popup_on_exit(event, frequency, vgt_unique_outer_id)
{
    if (event.pageY < 40)
    {
        var current_y = event.pageY;

        if (!jQuery("#"+ vgt_unique_outer_id).parent().is(":visible") && current_y < localStorage.getItem("vgt_current_cursor_y"))
        {
            vgt_js_popup_trigger(frequency, vgt_unique_outer_id, 0);
        }

    }
}


//get the popup and
jQuery(document).ready(function(){

        localStorage.setItem("vgtinputText", "");
        jQuery("input[type=text], input[type=email]").each(function(){
            localStorage.setItem("vgtinputText", jQuery(this).val());
        });

        //On click, hide text
        jQuery("input[type=text], input[type=email]").click(function(){
            localStorage.setItem("vgtoldText", jQuery(this).val());

            if (localStorage.getItem("vgtinputText").indexOf(jQuery(this).val() != -1))
            {
                jQuery(this).val("");
            }

        });

        //On blur, restore text if there is no change
        jQuery("input").blur(function(){

            if ( (jQuery(this).val() == ""))
            {
                jQuery(this).val(localStorage.getItem("vgtoldText"));
            }

        });



    var my_ajaxurl = jQuery(".vgt_ajax_url").text();

    //reset the vgt_close_button_clicked variable
    localStorage.setItem("vgt_close_button_clicked", "");

    //load the popup based on popup-option-id
    jQuery("[vgt-popup-trigger-id]").each(function(){

        //load the popups and put into the div in the footer
        var data = {action: "vgt_get_popup_code", option_id: jQuery(this).attr("vgt-action-value")};
        jQuery.post(my_ajaxurl, data, function(response){
            var data = response.split(VGT_UNIQUE_WRAPER);
            jQuery("body").append(data[1]);
        });

    });

    jQuery(document).on("click", ".vgt_close_popup", function(){

        localStorage.setItem("vgt_popup_showed", "1");
        jQuery(this).parent().hide();

        //set a local storage variable so the popup will not be displayed again, this variable will be reset when the user
        //reload the page
        localStorage.setItem("vgt_close_button_clicked", "1");

        //send tracking data
        var my_ajax_url = jQuery(".vgt_ajax_url").text();
        var item_type = jQuery(this).siblings("[for_item]").attr("item_type");
        var item_id = jQuery(this).siblings("[for_item]").attr("item_id");
        var option_id = jQuery(this).siblings("[for_item]").attr("option_id");
        var ab_test_id = jQuery(this).siblings("[for_item]").attr("ab_id") == undefined ? "0" : jQuery(this).siblings("[for_item]").attr("ab_id");
        vgt_tracking_send_data(my_ajax_url, "close", item_id, item_type, option_id, "", "","",ab_test_id);
        //vgt_tracking_send_data(my_ajax_url, "click", item_id, item_type, option_id, clicked_element_tag, clicked_element_id, clicked_element_text, ab_test_id);
    });

    //buttons that trigger the popup

    jQuery(document).on("click", "[vgt-popup-trigger-id]", function(){
        var popup_class = jQuery(this).attr("vgt-popup-trigger-id");
        jQuery("."+popup_class).fadeIn(function(){
            vgt_show_close_button("."+popup_class + " > [page_type]");

        });

        var top_width = jQuery("."+ popup_class).children(".vgt_top_center").outerWidth();
        var bottom_width = jQuery("."+ popup_class).children(".vgt_bottom_center").outerWidth();

        var vgt_screen_height = jQuery("."+ popup_class).children(".vgt_screen_center").outerHeight();
        var vgt_screen_width = jQuery("."+ popup_class).children(".vgt_screen_center").outerWidth();

        jQuery("."+ popup_class).children(".vgt_top_center").css("margin-left", -top_width/2);
        jQuery("."+ popup_class).children(".vgt_bottom_center").css("margin-left", -bottom_width/2);

        jQuery("."+ popup_class).children(".vgt_screen_center").css("margin-top", -vgt_screen_height/2);
        jQuery("."+ popup_class).children(".vgt_screen_center").css("margin-left", -vgt_screen_width/2);


    });

    //trigger the popup on button/link click
    jQuery(document).on("click", "[vgt-action=open-link]", function(){
        var link = jQuery(this).attr("vgt-action-value");
        if (jQuery(this).attr("vgt-new-window") == "true")
        {
            window.open(link, "_blank");
        } else
        {
            window.open(link);
        }
        return false;


    });

});