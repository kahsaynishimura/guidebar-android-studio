<?php

    define("PLUGIN_PATH", plugin_dir_path(__FILE__));
    include_once 'updater/file.php';
    include_once 'code/const.php';
    //function to get the current themes on local folders
 
    function sub_squeezers_themes_gallery_cb()
    { ?>
        <h3>View available themes</h3>
        <?php echo sq_bgt_activation_notice();?>
        <select name="sq_bgt_theme_select">
            <option value="" selected="selected"></option>
            <option value="popup">Popup</option>
            <option value="traditional">Traditional</option>
            <option value="video">Video</option>
            <option value="widget">Widget</option>
        </select>
        
        <div id="sq_themes_gallery">
            
        </div>
        <img id="sq_gallery_loading" src="<?php echo plugins_url('/updater/img/load.gif', __FILE__); ?>" />
        <div style="display: none;" id="small_loading_gif"><?php echo plugins_url("/updater/img/small_load.gif", __FILE__); ?></div>
    <?php }
    
 
    
    add_action("wp_ajax_sq_bgt_check_themes", "sq_bgt_check_themes_cb");
    
    function sq_bgt_check_themes_cb(){
        $folders = array();
        
        if ($_POST['dir'] == 'video')
        {
            $dir = scandir(plugin_dir_path(__FILE__).'themes/video');
            
        } else if($_POST['dir'] == 'traditional')
        {
            $dir = scandir(plugin_dir_path(__FILE__).'themes/traditional');
            
        } else if($_POST['dir'] == 'popup')
        {
            $dir = scandir(plugin_dir_path(__FILE__).'themes/popups/themes');
            
        } else if($_POST['dir'] == 'widget')
        {
            $dir = scandir(plugin_dir_path(__FILE__).'themes/widgets/themes');
        }
        
        foreach ($dir as $d)
        {
            if (is_numeric($d))
            {
                $folders[] = $d;
            }
        }
        
        echo json_encode($folders);
        die();
    }
    
    //function to get available themes from server
    add_action('wp_ajax_sq_bgt_get_server_themes', 'sq_bgt_get_server_themes_cb');
    
    function sq_bgt_get_server_themes_cb()
    {
        if (function_exists('curl_init'))
        {
        	$ch = curl_init();
        	
        	curl_setopt($ch, CURLOPT_URL, BGT_SERVER_THEME_URL."get_themes.php?theme_type=".$_POST['theme_type']);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        	
        	$server_themes = curl_exec($ch);
        	
        	curl_close($ch);
        } else 
        {
        	$server_themes = file_get_contents(BGT_SERVER_THEME_URL."get_themes.php?theme_type=".$_POST['theme_type']);
        }
    	
        
        echo $server_themes;
        die();
    }
    
    
    add_action("wp_ajax_check_and_download", 'check_and_download_cb');
    
    //first, create a
    function check_and_download_cb() {
        //get the email of current user. record when activated
        $email = get_option('sq_activation_email');
        $theme_type = $_POST['theme_type'];
        $theme_id = $_POST['theme_id'];
        
        echo $email;
        
        $url = BGT_SERVER_THEME_URL.'update.php?user_email='.$email.'&theme_type='.$theme_type.'&theme_id='.$theme_id;
        
        if (ini_get('allow_url_fopen') === 1)
        {
        	$return = json_decode(file_get_contents($url));
        } else if (function_exists('curl_init'))
        {
        	$ch = curl_init();
        	curl_setopt($ch, CURLOPT_URL, $url);
        	
        	//return the transfer as a string
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	
        	$return = curl_exec($ch);
        	
        	curl_close($ch);
			$return = json_decode($return);    
			var_dump($return);    	 
        } else 
        {
        	$return = json_decode(file_get_contents($url));
        }
        

        
        if (stripos($return[0], ".zip") == FALSE)
        {
            echo "failed to retrieve .zip file";
            die();        
        }
        
        //download the file
        sq_bgt_downloader($return[0], PLUGIN_PATH.'/tmp/'.$theme_id.".zip");
        sq_bgt_downloader($return[1], PLUGIN_PATH.'/tmp/'.$theme_id.".jpg");
        
        //save the files to appropriate locations
        switch ($theme_type)
        {
            case "video":
                $theme_path = PLUGIN_PATH.'themes/video/';
                $thumbnail_path = PLUGIN_PATH.'themes/video/thumbnail/';
                break;
            case "traditional":
                $theme_path = PLUGIN_PATH.'themes/traditional/';
                $thumbnail_path = PLUGIN_PATH.'themes/traditional/thumbnail/';
                break;
            case "popup":
                $theme_path = PLUGIN_PATH.'themes/popups/themes/';
                $thumbnail_path = PLUGIN_PATH.'themes/popups/thumbnail/';
                break;
            case "widget":
                $theme_path = PLUGIN_PATH.'themes/widgets/themes/';
                $thumbnail_path = PLUGIN_PATH.'themes/widgets/thumbnail/';
                break;
            
        }
        //extract the file to its location
        sq_bgt_extractor(PLUGIN_PATH.'tmp/'.$theme_id.".zip", $theme_path);
        
        unlink(PLUGIN_PATH.'tmp/'.$theme_id.".zip");
        
        //move the thumbnail
        rename(PLUGIN_PATH.'tmp/'.$theme_id.".jpg", $thumbnail_path.$theme_id.".jpg");
        
        die();

    }
    