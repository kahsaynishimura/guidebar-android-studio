/**
 * Created by gatovago on 10/28/14.
 */


function vgt_tracking_send_data(ajaxurl, event_type, item_id, item_type, option_id, clicked_element_tag, clicked_element_id, clicked_element_text, ab_test_id)
{
    var data = {
        action: "vgt_record_tracking_data",
        event_type: event_type,
        item_id: item_id,
        item_type: item_type,
        option_id: option_id,
        clicked_element_tag: clicked_element_tag,
        clicked_element_id: clicked_element_id,
        clicked_element_text: clicked_element_text,
        ab_test_id: ab_test_id

    };
    console.log(ab_test_id);
    jQuery.post(ajaxurl, data, function(response){
        console.log(response);
    });


}

function vgt_tracking_get_element_tag(element)
{
    if (element.is("a"))
    {
        return "a";
    }

    if (element.is("input"))
    {
        return "input";
    }

    if (element.is("button"))
    {
        return "button";
    }

    return "";
}

function vgt_tracking_get_element_text(element)
{
    if (element.is("a") || element.is("button"))
    {
        return element.text();
    }

    if (element.is("input"))
    {
        return element.attr("value");
    }

    return "";


}


jQuery(document).ready(function(){

    var my_ajax_url = jQuery(".vgt_ajax_url").text();


    //record the view event
    jQuery("[page_type=vgt_page]").each(function(){
        if (jQuery(this).is(":visible"))
        {
            var id = jQuery(this).attr("id");
            var item_type = jQuery("[for_item="+id+"]").attr("item_type");
            var item_id = jQuery("[for_item="+id+"]").attr("item_id");
            var option_id = jQuery("[for_item="+id+"]").attr("option_id");
            var ab_test_id = jQuery("[for_item="+id+"]").attr("ab_id") == undefined ? "0" : jQuery("[for_item="+id+"]").attr("ab_id");

            vgt_tracking_send_data(my_ajax_url, "view", item_id, item_type, option_id, "", "", "", ab_test_id);
            //.vgt_item_id (the class to store ID of the item)
            //vgt_tracking_send_data(ajaxurl, event_type, item_id, item_type, option_id, clicked_element_tag, clicked_element_id, clicked_element_text, ab_test_id)
        }

    });

    //record the click event
    jQuery(document).on("click", "[page_type=vgt_page] button, [page_type=vgt_page] a, [page_type=vgt_page] input[type=submit], [page_type=vgt_page] input[type=button], [page_type=vgt_page] input[type=image]", function(){

        localStorage.setItem("vgt_popup_showed", "1");
        var my_ajax_url = jQuery(".vgt_ajax_url").text();

        var parent = jQuery(this).closest("[page_type=vgt_page]");
        var id = parent.attr("id");
        var item_type = jQuery("[for_item="+id+"]").attr("item_type");
        var item_id = jQuery("[for_item="+id+"]").attr("item_id");
        var option_id = jQuery("[for_item="+id+"]").attr("option_id");
        var clicked_element_tag = vgt_tracking_get_element_tag(jQuery(this));
        var clicked_element_text = vgt_tracking_get_element_text(jQuery(this));
        var clicked_element_id = jQuery(this).attr("id") == undefined ? "" : jQuery(this).attr("id");
        var ab_test_id = jQuery("[for_item="+id+"]").attr("ab_id") == undefined ? "0" : jQuery("[for_item="+id+"]").attr("ab_id");

        vgt_tracking_send_data(my_ajax_url, "click", item_id, item_type, option_id, clicked_element_tag, clicked_element_id, clicked_element_text, ab_test_id);

    });

});

