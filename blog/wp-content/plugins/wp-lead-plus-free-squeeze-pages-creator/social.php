<?php

function sub_squeezers_social_cb()
{ ?>
    
    <div id="sq_social">
        <h2>Social buttons</h2>
        <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
        <h3>Display the social bar?</h3>
        <div id="sq_onoff">
            <input type="radio" name="enable" value="enable" /> Enable  &nbsp
            <input type="radio" name="enable" value="disable" checked="true"/> Disable    <br /><br />            
        </div>
        
        <h3>Orientation</h3>
        <div id="sq_orientation">
            <input type="radio" name="ori" value="vertical" /> Vertical  &nbsp
            <input type="radio" name="ori" value="horizontal" checked="true"/> Horizontal    <br /><br />
        </div>
        
        <h3>Position</h3>
        <div id="sq_position">
            <input type="radio" name="position" value="tl" checked="true"/> Top left  &nbsp
            <input type="radio" name="position" value="tr"/> Top right  &nbsp
            <input type="radio" name="position" value="tc"/> Top center  &nbsp
            <input type="radio" name="position" value="bl" /> Bottom left  &nbsp
            <input type="radio" name="position" value="br"/> Bottom right  &nbsp
            <input type="radio" name="position" value="bc"/> Bottom center  &nbsp
            <input type="radio" name="position" value="cr" disabled="true"/> Center right  &nbsp
            <input type="radio" name="position" value="cl"  disabled="true"/> Center left  &nbsp
            
            
        </div>
        <h4>Which buttons to use?</h4>
        <div id="sq_buttons">
            <input type="checkbox" name="socialbtn" value="facebook" checked="checked" /> Facebook &nbsp
             <input type="checkbox" name="socialbtn" value="google" checked="checked" /> Google + &nbsp
            <input type="checkbox" name="socialbtn" value="twitter" checked="checked" /> Twitter &nbsp
            <input type="checkbox" name="socialbtn" value="linkedin" checked="checked" /> Linkedin &nbsp
            
        </div>
        
        <h3>Preview</h3>
    <div id="preview_horizontal">
        <div class="fb-button social_btn" style="width: 75px; float: left;">
        <fb:like href="" layout="box_count"></fb:like>
        </div>
        <div class="plusone social_btn" style="width: 75px; float: left;"><g:plusone size="tall" annotation="bubble" href="http://google.com/"></g:plusone></div>
        <div class="social_btn" id="tweet-button" style="width: 90px; float: left;"><a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical">Tweet</a></div>
        <div id="linkedin-wrapper" class="social_btn"><script type="in/share" data-url="http://google.com/" data-counter="top"></script></div>    
    </div>
	
    
    <div id="preview_vertical">
        <div class="fb-button social_btn" style="width: 75px; margin: 5px 0;">
            <fb:like href="" layout="box_count"></fb:like>
        </div>
        <div class="plusone social_btn" style="width: 75px; margin: 5px 0;"><g:plusone size="tall" annotation="bubble" href=""></g:plusone></div>
        <div class="social_btn" id="tweet-button" style="width: 90px; margin: 5px 0;"><a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical">Tweet</a></div>
        <div id="linkedin-wrapper" class="social_btn" style="margin: 5px 0;"><script type="in/share" data-url="http://google.com/" data-counter="top"></script></div>    
    </div>
        
        <p><input type="button" id="sq_btn_save" class="button button-primary" value="Save options"/></p>
        <p id="sq_notify" style="display: none; background: #BADA55; height: 40px; width: 200px; border-radius: 4px; line-height: 40px; text-align: center; font-size: 1.1em;">Settings Saved!</p>
   
    </div>
    
    <script>
        jQuery(document).ready(function(){
            
            //social button selection
            jQuery("#sq_buttons input[type=checkbox]").click(function(){
                //get the position of the current checkbox
                if (jQuery(this).is(":checked"))
                {
                    jQuery("#preview_vertical .social_btn").eq(jQuery("input[type=checkbox]").index(jQuery(this))).fadeIn();
                    jQuery("#preview_horizontal .social_btn").eq(jQuery("input[type=checkbox]").index(jQuery(this))).fadeIn();
    
                } else
                {
                    jQuery("#preview_vertical .social_btn").eq(jQuery("input[type=checkbox]").index(jQuery(this))).fadeOut();
                    jQuery("#preview_horizontal .social_btn").eq(jQuery("input[type=checkbox]").index(jQuery(this))).fadeOut();
                }
                
            });
            
            //orientation selection
            
            jQuery("#sq_orientation input[type=radio]").click(function(){
                var ori = jQuery(this).val();
                
                if (ori == "horizontal")
                {
                    jQuery("#preview_vertical").fadeOut();
                    jQuery("#preview_horizontal").fadeIn();
                    //hide the center right and center left option of positioning coz it's a stupid idea
                    jQuery("#sq_position input[value=cl], #sq_position input[value=cr]").attr("disabled", true);
                    jQuery("#sq_position input[value=tc], #sq_position input[value=bc]").removeAttr("disabled");
                    
                } else
                {
                    jQuery("#preview_horizontal").fadeOut();
                    jQuery("#preview_vertical").fadeIn();
                    //hide the center right and center left option of positioning coz it's a stupid idea
                    jQuery("#sq_position input[value=tc], #sq_position input[value=bc]").attr("disabled", true);
                    jQuery("#sq_position input[value=cl], #sq_position input[value=cr]").removeAttr("disabled");
                    
                }
            });
                //save button
                jQuery("#sq_btn_save").click(function(){
                    
                    var position =  jQuery("#sq_position input[type=radio]:checked").val(); 
                    var orientation = jQuery("#sq_orientation input[type=radio]:checked").val();
                    var enable = jQuery("#sq_onoff input[type=radio]:checked").val(); 
                    //get the checked value
                    var social_btns = [];
                    jQuery("#sq_buttons input[type=checkbox]:checked").each(function(){
                        social_btns.push('"'+jQuery(this).val() + '":"'+ jQuery(this).val() + '"' );
                        
                    });
                    
                    social_btns = '{' + social_btns.join() + '}';
                    var data = {
                        action: 'sq_save_social',
                        position: position,
                        enable: enable,
                        orientation: orientation,
                        social_btns: (social_btns)
                    }
                    //send the data
                    jQuery.post(ajaxurl, data, function(){
                        jQuery("#sq_notify").fadeIn();
                        
                        //hide the notification
                        setTimeout(function(){jQuery("#sq_notify").fadeOut();}, 2000);
                        });
                });            
            
        });

        
    </script>
<?php }


//process the save button, save the social buttons into options
add_action('wp_ajax_sq_save_social', 'sq_save_social_cb');

function sq_save_social_cb()
{
    //configure the position of the social bar, stick
    $position = "";
    switch ($_POST['position'])
    {
        case 'tl':
            $position = "top: 5px; left: 5px;";
            break;
        
        case 'tr':
            $position = "top: 5px; right: 5px;";
            break;
        
        case 'bl':
            $position = "bottom: 5px; left: 5px;";
            break;
        
        case 'br':
            $position = "bottom: 5px; right: 5px;";
            break;
        
        case 'tc':
            $position = "top: 5px; left: 50%; margin-left: -155px;";
            break;
        
        case 'bc':
            $position = "bottom: 5px; left: 50%; margin-left: -155px;";
            break;
        
        case 'cr':
            $position = "top: 50%; right: 5px; margin-top: -155px";
            break;
        
        case 'cl':
            $position = "top: 50%; left: 5px; margin-top: -155px";
            break;
        
        default:
            $position = "top: 50%; left: 5px; margin-top: -155px";
            break;
    }
    

    //orientation, this is the style apply to particular button
    $ori_single = '';
    $ori_div  = '';
    
    //in case horizontal
    if ($_POST['orientation'] == 'horizontal')
    {
        $ori_div = '<div style="width: 310px; height: 70px; position: fixed;'.$position.'">';
        $ori_single = 'float: left; width: 75px;';
    } else
    {
        $ori_div = '<div style="width: 70px; height: 310px; position: fixed; '.$position.'">';
        $ori_single = 'margin: 5px 0;';
    }
    
    //configure the javascript too
    /*
    <script type='text/javascript' src='https://apis.google.com/js/plusone.js'></script>
<script type='text/javascript' src='http://platform.linkedin.com/in.js'></script>
<script type='text/javascript' src='http://platform.twitter.com/widgets.js'></script>
    */
    
    $script_code = '';
    
    //get the social buttons
    $button_code = '';
    $buttons = json_decode(str_replace("\\", "",$_POST['social_btns']), true);
    
    if (in_array('facebook', $buttons))
    {
        $button_code .= '<div class="fb-button social_btn" style="width: 75px; '.$ori_single.'">
            <fb:like href="" layout="box_count"></fb:like>
        </div>';
        
        $script_code .= '<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>';
    }
    
    if (in_array('google', $buttons))
    {
        $button_code .= '<div class="plusone social_btn" style="width: 75px;'.$ori_single.'"><g:plusone size="tall" annotation="bubble" href=""></g:plusone></div>';
        $script_code .= '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
    }
    
    if (in_array('twitter', $buttons))
    {
        $button_code .= '<div class="social_btn" id="tweet-button" style="width: 90px;'.$ori_single.'"><a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical">Tweet</a></div>';
        $script_code .= '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
    }
    
    if (in_array('linkedin', $buttons))
    {
        $button_code .= '<div id="linkedin-wrapper" class="social_btn" style="width: 75px;'.$ori_single.';"><script type="in/share" data-url="" data-counter="top"></script></div>';
        $script_code .= '<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script>';
    }
    
    //return the code, save to db
    
    if ($_POST['orientation'] == 'horizontal')
    {
        $code = $ori_div.$button_code.'</div>';
    } else
    {
        $code = $ori_div.$button_code.'<div style="clear:both"></div></div>';
    }
    
    //save the code to db
    update_option('sq_social_code', base64_encode($code));
    update_option('sq_social_bar_status', $_POST['enable']);
    update_option('sq_social_scripts', $script_code);    

    die();
}