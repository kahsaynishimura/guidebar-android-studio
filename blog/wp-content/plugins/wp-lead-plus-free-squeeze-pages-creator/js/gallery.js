jQuery('document').ready(function(){
    
    function get_my_themes(dir) {
        /* get the current available themes on the site, based on dir (popup, traditional, )
         */
        
    }
    
    //response to theme selection
    jQuery('select[name="sq_bgt_theme_select"]').change(function(){
        /* when the user select any type of theme,
         * 1. get all the thumbnail from server
         * 2. get all the current theme that user has currently
         * 3. Mark the themes that are not available on user's site with New
         */
        
        if (jQuery(this).val() == "") {
            console.log("none");
            return;
        }
        jQuery('#sq_themes_gallery').html("");
        jQuery('#sq_gallery_loading').fadeIn();
        //get the current themes
        if (jQuery(this).val() != "") {
            
            //get the type of the theme (video, traditional, popup, widget)
            var theme_type = jQuery(this).val();
            
            jQuery.post(ajaxurl, {action: 'sq_bgt_check_themes', dir: jQuery(this).val()}, function(response){
                var local_themes = jQuery.parseJSON(response);
                //now get the available themes from the server
                jQuery.post(ajaxurl, {action: 'sq_bgt_get_server_themes', theme_type: theme_type}, function(new_response){
                    var server_themes = jQuery.parseJSON(new_response);
                    
                    console.log(server_themes);
                    var display = "";
                    
                    for (var i = 0; i < server_themes.length; i++)
                    {
                        if (local_themes.indexOf(server_themes[i]) == -1) {
                            var status = 'install';
                        } else
                        {
                            var status = 'reinstall';
                        }

                        //display += '<div class="gallery_item"><a href="http://wpleadplus.com/updater/'+theme_type+'/'+server_themes[i] +'.jpg" rel="lightcase"><img src="http://wpleadplus.com/updater/'+theme_type+'/'+server_themes[i] +'.jpg" /></a> <div class="install_status '+status+'">'+status+'</div></div>';
                        display += '<div class="gallery_item"><img src="http://wpleadplus.com/updater/'+theme_type+'/'+server_themes[i] +'.jpg" /> <div theme_id="'+(server_themes[i])+'" class="install_status '+status+'">'+status+'</div></div>';
                    }
                    
                    jQuery('#sq_gallery_loading').hide();
                    jQuery('#sq_themes_gallery').html(display);
                    jQuery('a[data-rel^=lightcase]').lightcase('init');
                    
                    
                });                
                
            });

        }
        
        
    });
    
    //download the theme
    jQuery(document).on('click','.install_status',function(){
        //show the loading gif
        jQuery(this).html('<img id="live_loading_gif" src='+jQuery('#small_loading_gif').text()+" />");
        
        //check license
        //xd
        var theme_id = jQuery(this).attr("theme_id");
        var theme_type = jQuery('select[name="sq_bgt_theme_select"]').val();
        
        //send this info to server
        //server will check
        // * license
        // * download if license ok
        
        jQuery.post(ajaxurl, {action: 'check_and_download', theme_id: theme_id, theme_type: theme_type}, function(response){
            jQuery('#live_loading_gif').parent().html("Done!");
            
            
        });
        
    });
    
});