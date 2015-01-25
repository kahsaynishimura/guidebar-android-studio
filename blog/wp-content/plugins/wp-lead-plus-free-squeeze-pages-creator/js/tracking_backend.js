/**
 * Created by gatovago on 11/5/14.
 */

function vgt_populate_list_of_items(div_id, data)
{
    jQuery(div_id).parent().fadeIn();
    var option = "";

    for (var i = 0; i < data.length; i++)
    {

        option += '<li><input type="checkbox" class="form-control" name="report_items" alt_id = "'+data[i].id+'" value='+data[i].id+' /> <span class="vgt_item_title">'+vgt_de_serialize_data(data[i].title) + '</span> <span class="click_details" href="#"><small>Details</small></span></li>';
    }

    jQuery("#item_list").html("<ul>" + option + "</ul>");
}

jQuery(document).ready(function(){

    jQuery(document).on("change","#report_type, #item_type", function(){

        var report_type = jQuery("#report_type").val();
        var item_type = jQuery("#item_type").val();

        if (report_type == "individual")
        {
            jQuery("#ab_list").parent().fadeOut();
        }

        if (report_type == "" || item_type == "")
        {
            return;
        }

        //get the list of items then append to the item list
        var data = {
            action: "tracking_get_item_list",
            report_type: report_type,
            item_type: item_type
        }

        jQuery.post(ajaxurl, data, function(response){
            var data = vgt_parse_json_output(response);

            console.log(data);

            if (data.length == 0)
            {
                jQuery("#item_list").html("");
                return;
            }

            if (report_type == "individual")
            {
                vgt_populate_list_of_items("#item_list", data)

            } else if (report_type == "ab")
            {

                var option = '<option value=""></option>';

                for (var i = 0; i < data.length; i++)
                {

                    option += '<option alt_id = "'+data[i].id+'" value='+data[i].id+'>'+vgt_de_serialize_data(data[i].title) + '</option>';
                }

                jQuery("#ab_list").html(option);
                jQuery("#ab_list").parent().fadeIn();

            }

        });




    });

    //get the list of options/squeeze page of an a/b test
    jQuery(document).on("change", "#ab_list", function(){

        var data = {
            ab_test_id : jQuery(this).val(),
            item_type : jQuery("#item_type").val(),
            action: "tracking_get_options_of_ab" //get all the options under an A/B test
        }

        jQuery.post(ajaxurl, data, function(response){

            var data = vgt_parse_json_output(response);

            console.log(data);

            if (data.length == 0)
            {
                jQuery("#item_list").html("");
                return;
            }
            vgt_populate_list_of_items("#ab_list", data)

        });


    });

    //Get report of the selected item
    jQuery(document).on("change", "#item_list input[type=checkbox]", function(){

        var checked = vgt_get_checked_categories("#item_list");


        jQuery("#vgt_chart").html("");
        var data = {
            item_type: jQuery("#item_type").val(),
            selected_items : vgt_serialize_data(JSON.stringify(checked))

        };

        if (jQuery("#report_type").val() == "ab")
        {
            data.ab_test_id = jQuery("#ab_id").val();
        } else
        {
            data.ab_test_id = 0;
        }

        data.action = "tracking_individual_report";

        jQuery.post(ajaxurl, data, function(response){

            var return_data = vgt_parse_json_output(response);
            console.log(return_data);

            var labels = [];
            var view_count = [];
            var click_count = [];

            for (var i = 0; i < return_data.length; i ++)
            {

                var title = jQuery("#item_list input[alt_id="+return_data[i]["id"]+"]").siblings(".vgt_item_title").text();

                if (title.length > 30)
                {
                    title = title.substr(0,27) + "...";
                }

                labels.push(title);
                view_count.push(return_data[i]["view"]);
                click_count.push(return_data[i]["click"]);

            }

            var data = {
                labels: labels,
                datasets:[
                    {
                        label: "View",
                        fillColor: "rgba(220,220,220,0.5)",
                        strokeColor: "rgba(220,220,220,0.8)",
                        highlightFill: "rgba(220,220,220,0.75)",
                        highlightStroke: "rgba(220,220,220,1)",
                        title: "View",
                        data: view_count
                    },
                    {
                        label: "Click",
                        fillColor: "rgba(151,187,205,0.5)",
                        strokeColor: "rgba(151,187,205,0.8)",
                        highlightFill: "rgba(151,187,205,0.75)",
                        highlightStroke: "rgba(151,187,205,1)",
                        title: "Click",
                        data: click_count
                    }
                ]


            };


            jQuery("#general_chart").html("");
            jQuery("#general_chart").html('<canvas id="vgt_chart" style="width: 100%;" width="800px" height="300px"></canvas>');
            var ctx = document.getElementById("vgt_chart").getContext("2d");
            var myBarChart = vgt_plot_bar_chart(data, options, ctx);


        });

    });

    //get details report on one item (squeeze page, popup, widget)

    jQuery(document).on("click", ".click_details", function(){


        jQuery(".click_details").css("background", "none");
        jQuery(this).css("background", "#ff8c46");
        var data = {
            action: "vgt_get_single_item_click_details",
            item_id: jQuery(this).siblings("input").attr("alt_id"),
            item_type: jQuery("#item_type").val(),
            report_type: jQuery("#report_type").val(),
            ad_test_id: jQuery("#report_type").val() == "individual" ? 0 : jQuery("#ab_list").val()
        };


        jQuery.post(ajaxurl, data, function(response){

            var data = vgt_parse_json_output(response);



            var labels = ["Total View", "Total Click"];
            var clicks = [parseInt(data.total_view), parseInt(data.total_click)];

            var click_details = (data.click_details);



            for (var i = 0; i < click_details.length; i++)
            {
                labels.push(vgt_de_serialize_data(click_details[i].clicked_element_text));
                clicks.push(parseInt(click_details[i].click_count));

                /*

                var color_index = i + 2;
                var single_item = {
                    label: vgt_de_serialize_data(click_details[i].clicked_element_text),
                    fillColor: vgtFillColor[color_index],
                    strokeColor: vgtStrokeColor[color_index],
                    highlightFill: vgtHighlightFill[color_index],
                    highlightStroke: vgtHighlightStroke[color_index],
                    title: click_details[i].clicked_element_text,
                    data: vgt_de_serialize_data(click_details[i].click_count)

                }

                datasets.push(single_item);
                */


            }
            var data = {
                labels : labels,
                datasets: [{
                    fillColor: "rgba(225,140,70,0.5)",
                    strokeColor: "rgba(225,140,70,0.8)",
                    highlightFill: "rgba(225,140,70,0.75)",
                    highlightStroke: "rgba(225,140,70,1)",
                    label: "Click",
                    data: clicks

                }]
            }

            console.log(clicks);


            jQuery("#detailed_chart").html("");
            jQuery("#detailed_chart").html('<canvas id="vgt_detailed_chart" style="width: 100%;" width="800px" height="300px"></canvas>');
            var ctxa = document.getElementById("vgt_detailed_chart").getContext("2d");
            var detailedChart = vgt_plot_bar_chart(data, options, ctxa);

        });

    });
});