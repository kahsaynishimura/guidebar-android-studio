<?php
	/* Template name: Blank Template
	
	*/

?>

<?php 
	remove_filter( 'the_content', 'wpautop' );
	//get current post id
	$id = get_the_ID();
	
	//get custom js code if any
	$js_code = get_post_meta($id, 'pros_custom_js_code', true);
	$js_position = get_post_meta($id, 'pros_custom_js_position', true);
		
	//define a function to place javascript code
	function sq_bgt_place_custom_js($id, $position, $js_position, $js_code)
	{
		if ($js_position == $position)
		{
			//var_dump($js_code);
			echo base64_decode($js_code);
			
		}
		
		return;
	}
	
	//filter the content 
	add_filter('the_content', 'sq_bgt_replace_https');
	
	function sq_bgt_replace_https($content)
	{
		return sq_bgt_use_https($content);
	}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php sq_bgt_place_custom_js($id, 'after_head', $js_position, $js_code); ?>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- the src in the script below will load when the squeeze page is loaded. it will send 3 GET var (type, id, view(in view/arrive)) -->
<script src="<?php echo sq_bgt_use_https(plugins_url())."/wpleadplus/tracking.php?page_type=squeeze_page&destination=view&page_id=".$id."&ref=".urlencode($_SERVER['HTTP_REFERER']);?>">
	var sq_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
	document.onclick = function(){};
</script>

<script type="text/javascript">
function sq_bgt_set_cookie(cookieName,cookieValue,nDays) {
	  if (typeof (nDays) == undefined)
	  {
		  nDays = 1;
	  }
	  var today = new Date();
	  var expire = new Date();
	  if (nDays==null || nDays==0) nDays=1;
	  expire.setTime(today.getTime() + 3600000*24*nDays);
	  document.cookie = cookieName+"="+escape(cookieValue)
					  + ";expires="+expire.toGMTString();
	 }
</script>
<title><?php
	echo the_title();

	?></title>
	<style>
		#sq_body_container {
			background-image: none !important;
		}
	</style>
		<?php
		if (get_option('sq_user_tracking_code') !== false) 
		{
			
			echo get_option('sq_user_tracking_code');
		}
		$header =  get_post_meta($id, 'pros_post_head', true);
		
		echo sq_bgt_use_https($header);
		
		sq_bgt_place_custom_js($id, 'before_head', $js_position, $js_code);
		echo "</head>";
	?>
<body>

	<!-- Background image and video -->
	<?php 
		$bg_type = get_post_meta($id, 'pros_current_background_type', true);
		$bg_url = get_post_meta($id, 'pros_current_background_url', true);
		if ($bg_type == "video")
		{
			echo '<div style="position: fixed; z-index: -999; width: 100%; height: 100%"> <iframe width="100%" height="120%" style="position: absolute; top: -10%;" src="'. $bg_url. "?autoplay=1&controls=0&showinfo=0&autohide=1&loop=1&rel=0" . '" frameborder="0" allowfullscreen></iframe></div>';
		} else  if ($bg_type = "image")
		{
			echo '<script>jQuery(document).ready(function(){jQuery.backstretch("'.$bg_url.'"); jQuery("#sq_body_container").css("background", "none");   });</script>';
		}
	
	?>
	
	<?php if (get_option('sq_bgt_enable_facebook') == 'enable') {
		
		echo '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=637287852982156";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));</script>';
		
	}?>
	<?php sq_bgt_place_custom_js($id, 'after_body', $js_position, $js_code); ?>	
	<?php while ( have_posts() ) : the_post(); ?>
	<?php sq_bgt_use_https(the_content()); ?>
	<?php endwhile; // end of the loop. ?>
                
<?php if (get_option('sq_social_bar_status') == 'enable')
{
    echo sq_bgt_use_https(get_option('sq_social_scripts'));
    echo sq_bgt_use_https(base64_decode(get_option('sq_social_code')));

} 


?>
<script>
		//function to do open the url
		function sq_bgt_open_me(url, self, pop, event)
	   {
		   event.preventDefault();
		   if (pop == true)
		   {
			   //in case the button clicked is from the popup, we have two cases, one case is the popup submit button is a link
			   //and the second one is the submit button is a submit button of a form.
			   //Case one, submit button is a link
			   if (url != "") {
				   //send the disable message
				   jQuery.post(sq_ajax_url, {action: "pop_disable_pop", disable: "true"}, function(){
					   window.open(url, self);	
				   });	
			   } else //case 2: submit button is a submit button, send the disable message then submit
			   {
				   jQuery.post(sq_ajax_url, {action: "pop_disable_pop", disable: "true"}, function(){
					   jQuery('#pop_bgt_container form').submit();
				   });	
			   }
			   
			   
		   } else // if the button is not from a popup, open the link
		   {
			   window.open(url, self);
		   }
	   }
	function sq_bgt_move_body()
	{
		//get the coordinate of the form
		var coor = jQuery('#sq_box_container').offset();
		var top = coor.top;
		if (coor.left < 0)
		{
			jQuery('#sq_box_container').offset({top: top, left: 0});
		}
		
		if (jQuery(window).width() < (coor.left + jQuery('#sq_box_container').width()))
		{
			var distance = (jQuery(window).width() - jQuery('#sq_box_container').width() );
			console.log(distance);
			jQuery('#sq_box_container').offset({top: top, left: distance});
		}
	}
	jQuery(document).ready(function(){
	//get the coordinate of the form
	sq_bgt_move_body();
	
	function checkBothSide()
	{
		if (jQuery('#sq_left_img img').length != 0 && jQuery('#sq_right_img img').length != 0)
		{
			return "both";
		} else if (jQuery('#sq_left_img img').length != 0)
		{
			return 'left';
		} else if (jQuery('#sq_right_img img').length != 0)
		{
			return 'right';
		} else {
			return 'none';
		}
	}
	
	function setMaxWidth(max_allowed, side)
	{
		if (side == 'both')
		{
			jQuery( "#sq_left_img, #sq_right_img" ).css( "maxWidth", (max_allowed -5) + "px" );
			jQuery( "#sq_left_img img, #sq_right_img img" ).css( "maxWidth", (max_allowed -5) + "px" );
		} else if (side == 'left')
		{
			jQuery( "#sq_left_img" ).css( "maxWidth", (max_allowed -5) + "px" );
			jQuery( "#sq_left_img img" ).css( "maxWidth", (max_allowed -5) + "px" );
		} else if (side == 'right')
		{
			jQuery( "#sq_right_img" ).css( "maxWidth", (max_allowed -5) + "px" );
			jQuery( "#sq_right_img img" ).css( "maxWidth", (max_allowed -5) + "px" );
		} else
		{
			return;
		}
		
	}
	
	jQuery(window).resize(function(){
		sq_bgt_move_body();
		
		var body_width = jQuery("body").width();
		var box_width = jQuery('#sq_box_container').width();
		
		side = checkBothSide();
		//console.log(side);
		
		if (side == 'both') {
			var max_allowed = Math.round((body_width - box_width)/2);
		} else
		{
			var max_allowed = Math.round((body_width - box_width));
		}
		
		
		//console.log(body_width + " " + box_width + " " + max_allowed );
		setMaxWidth(max_allowed, side);
		//console.log(jQuery('#sq_left_img').css("max-width"));
		//console.log(jQuery('#sq_right_img').css("max-width"));
	});
	
	
		var body_width = jQuery("body").width();
		var box_width = jQuery('#sq_box_container').width();
		
		side = checkBothSide();
		//console.log(side);
		
		if (side == 'both') {
			var max_allowed = Math.round((body_width - box_width)/2);
		} else
		{
			var max_allowed = Math.round((body_width - box_width));
		}
		
		
		//console.log(body_width + " " + box_width + " " + max_allowed );
		setMaxWidth(max_allowed, side);
		//console.log(jQuery('#sq_left_img').css("max-width"));
		//console.log(jQuery('#sq_right_img').css("max-width"));
	});
</script>
<?php sq_bgt_place_custom_js($id, 'before_body', $js_position, $js_code); ?>
<!-- hidden iframe for click tracking  -->
<iframe src="" class="sq_bgt_tracking_iframe" style="display: none;"></iframe>
<script>
/* when the submit button clicked, save the type of page was submitted (sq, pop, wid). In this case, it's the squeeze page  */ 
jQuery("document").ready(function(){
	/* function to track the click */
	function sq_temp_bgt_set_cookie(cookieName,cookieValue) {
		  document.cookie = cookieName+"="+escape(cookieValue);
	}

	//remove the wmod of tracking ifradAme
	jQuery('.sq_bgt_tracking_iframe').attr("src", "");
	//record the click
	jQuery('#sq_body_container input[type="submit"], #sq_body_container input[type="button"], #sq_body_container input[type="image"]').click( function() {
		//load the conversion iframe
		setTimeout(function(){
		
			jQuery('.sq_bgt_tracking_iframe:first').attr("src", "<?php echo plugins_url()."/wpleadplus/tracking.php?destination=conversion&page_type=squeeze_page&page_id=".$id."&ref=".urlencode($_SERVER['HTTP_REFERER']); ?>");

		}, 100);
	} );
});

</script>

</body>
</html>