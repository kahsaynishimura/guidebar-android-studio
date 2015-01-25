/**
 * Created by gatovago on 10/30/14.
 */



//Processing autoresponder code
function vgt_process_autoresponder_code(code, form_div) //form_div: the part in page where form is located
{
    /*
    1. Save the autoresponder code
    2. Process the code and apply to the form
     */

    //1. Save the code
    localStorage.setItem(VGT_AR_CODE, vgt_serialize_data(code));

    //if the code is from mailpoet, do nothing

    //2. Process the code
    var data = {
        action  : "vgt_parse_autoresponder",
        code    : vgt_serialize_data(code)
    }

    jQuery.post(ajaxurl, data, function(response){
        var data = vgt_parse_json_output(response);
        console.log(data);

        if (data.form.action == undefined)
        {
            vgt_general_notification("warning", "Your form doesn't have action URL. Please check your code", 4);
        }

        jQuery(form_div + " form").attr("action", data.form.action);
        jQuery(form_div + " form").attr("method", data.form.method);

        //append input, checkboxes and radios
        var form_html = data.input_text + data.input_checkbox + data.input_radio + data.input_hidden + data.textarea + data.submit;

        //add some notification to the form
        form_html += '<div class="vgt_form_notification">  <span class="vgt_notification_success vgt_form_messages">Thanks for subscribing. Please check your email</span>  <span class="vgt_notification_missing vgt_form_messages">Please fill in all required fields</span></div>';

        jQuery(form_div + " form").html(form_html);

    });
}

//processing CSS code
function vgt_process_css_code(code)
{
    localStorage.setItem(VGT_CUSTOM_CSS_CODE, vgt_serialize_data(code));
    //set style code to head

    if (jQuery(".custom_css_head").length > 0)
    {
        jQuery("head .custom_css_head").html(code);
    } else
    {
        jQuery('<style class="custom_css_head">'+code+'</style>').appendTo("head");
    }

}

//processing JS code
function vgt_process_js_code(code, code_position)
{
    localStorage.setItem(VGT_CUSTOM_JS_CODE, BASE64.encode(code));//custom_js_code_position
    localStorage.setItem(VGT_CUSTOM_JS_CODE_POSITION, (code_position));
}

//processing HTML code
function vgt_process_html_code(code)
{
    localStorage.setItem("vgt_custom_html_code", vgt_serialize_data(code));

}

//processing media code
function vgt_process_media_code(code, media_div)
{
    //Get media type
    var media_type = vgt_get_media_type(code);
    console.log(media_type);

    //parse and put the media code to the media div,
    if (media_type == "image")
    {
        var code = '<img src="'+code+'" />'
        jQuery(media_div).html(code);
        return;
    }

    if (media_type != "not_media")
    {
        var pure_video_url = "";

        if (code.indexOf("http") == 0 || code.indexOf("//www") == 0 )
        {
            pure_video_url = code;
        } else
        {
            var pattern = /src=.*?[ ]/i;
            var x = code.match(pattern);

            pure_video_url = jQuery.trim(x[0].replace(/["']/g, ''));

            pure_video_url = jQuery.trim(pure_video_url.replace('src=', ''));
        }

        if (pure_video_url.indexOf("http") != 0) {
            pure_video_url = "http:" + pure_video_url;
        }
        console.log(pure_video_url);

        //append the video to media div
        jQuery(media_div).html('<iframe src="'+pure_video_url+'" frameborder="0" allowfullscreen></iframe>');
    }
}

function vgt_process_outer_background_code(bg_type, bg_value)
{
    localStorage.setItem(VGT_OUTER_BACKGROUND_TYPE, bg_type);
    localStorage.setItem(VGT_OUTER_BACKGROUND, vgt_serialize_data(bg_value));

    //reset the value of select box to ""
    jQuery("#vgt_outer_background_div select").val("");
    jQuery("#vgt_outer_background_div select").siblings(".bg_value").hide();

    if (bg_type == "image")
    {
        jQuery("#site_area").css("background", "url("+bg_value+") no-repeat");
        jQuery("#site_area").css("background-size", "cover");
        console.log("iamge");
    }

    else if (bg_type == "image_pattern")
    {
        jQuery("#site_area").css("background", "url("+bg_value+")");
    }

    else if (bg_type == "color")
    {
        jQuery("#site_area").css("background", bg_value);
    }

}

function vgt_process_inner_background_code(bg_type, bg_value)
{
    localStorage.setItem(VGT_INNER_BACKGROUND, bg_type);
    localStorage.setItem(VGT_INNER_BACKGROUND_TYPE, vgt_serialize_data(bg_value));

    //reset the value of select box to ""
    jQuery("#vgt_inner_background_div select").val("");
    jQuery("#vgt_inner_background_div select").siblings(".bg_value").hide();

    if (bg_type == "image")
    {
        //if the template has .vgt_inner_box (which is child of vgt_outer_container), apply the background to it instead
        if (jQuery(".vgt_inner_box").length > 0)
        {
            jQuery(".vgt_inner_box").backstretch(bg_value);
        } else
        {
            jQuery("[page_type]").backstretch(bg_value);
        }

    }

    else if (bg_type == "image_pattern")
    {
        if (jQuery(".vgt_inner_box").length > 0)
        {
            jQuery(".vgt_inner_box").backstretch("destroy");
            jQuery(".vgt_inner_box").css("background", "url("+bg_value+")");
        } else
        {
            jQuery("[page_type]").backstretch("destroy");
            jQuery("[page_type]").css("background", "url("+bg_value+")");
        }

        jQuery("[page_type]").css("background", "url("+bg_value+")");
    }

    else if (bg_type == "color")
    {
        if (jQuery(".vgt_inner_box").length > 0)
        {
            jQuery(".vgt_inner_box").backstretch("destroy");
            jQuery(".vgt_inner_box").css("background", bg_value);
        } else
        {
            jQuery("[page_type]").backstretch("destroy");
            jQuery("[page_type]").css("background", bg_value);
        }

    }
}


//MEDIA CODE PROCESSING
function vgt_get_media_type(code)
{
    var video_sites = ["youtube", "wistia", "vimeo", "dailymotion", "youku"];

    if (code.indexOf(".jpg") != -1 | code.indexOf(".png") != -1)
    {
        return "image";
    }

    for (var i = 0; i < video_sites.length; i++)
    {
        if (code.indexOf(video_sites[i]) != -1)
        {
            return video_sites[i];
        }
    }

    return "not_media";
}
