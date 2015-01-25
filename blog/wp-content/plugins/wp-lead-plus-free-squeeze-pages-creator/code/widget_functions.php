<?php
/**
 * Created by PhpStorm.
 * User: gatovago
 * Date: 10/26/14
 * Time: 10:02 AM
 */



    //function to generate widget code based on position in post of the widget
    function vgt_widget_build_full_post($content, $option_properties, $widget_core)
    {
        //return the widget based on vgt_widget_position_in_post
        $return_code = "";

        if ($option_properties["vgt_widget_position_in_post"] == "top")
        {
            $return_code = $widget_core."<br />".$content;

        } else if ($option_properties["vgt_widget_position_in_post"] == "bottom")
        {
            $return_code = $content."<br />".$widget_core;

        } else if ($option_properties["vgt_widget_position_in_post"] == "top_bottom")
        {
            $return_code = $widget_core."<br />".$content."<br />".$widget_core;

        } else if ($option_properties["vgt_widget_position_in_post"] == "random")
        {
            if (rand() % 2 == 0)
            {
                $return_code = $widget_core."<br />".$content;
            } else
            {
                $return_code = $content."<br />".$widget_core;
            }


        } else
        {
            $return_code = $content;
        }

        return $return_code;
    }

