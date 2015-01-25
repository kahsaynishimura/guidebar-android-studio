/*
function bgt_bg_init(element_id, img_src)
	{
		//get the width and height of element_id
		var height = jQuery(element_id).height();
		var width = jQuery(element_id).width();
		
		var img_elem = '<img class="bgt_img_bg" style="z-index: -99; max-width:100%; position: absolute; top: 0; left: 0; max-height: 100%; min-width: 100%; min-height: 100%; width: '+width+'; height: '+ height +';" src='+ img_src +' />';
		
		jQuery(element_id).append(img_elem);
	}
	
	function bgt_resizer()
	{
		var height = jQuery(this).height();
		var width = jQuery(this).width();
	}

*/
	   //function to set and get cookies
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
		 
		 function sq_bgt_get_cookie(cookieName) {
		  var theCookie=" "+document.cookie;
		  var ind=theCookie.indexOf(" "+cookieName+"=");
		  if (ind==-1) ind=theCookie.indexOf(";"+cookieName+"=");
		  if (ind==-1 || cookieName=="") return "";
		  var ind1=theCookie.indexOf(";",ind+1);
		  if (ind1==-1) ind1=theCookie.length; 
		  return unescape(theCookie.substring(ind+cookieName.length+2,ind1));
		 }

		jQuery.fn.bgt_bg_cover = function(img_src)
	{
		this.each(function(){
			var height = jQuery(this).height();
			var width = jQuery(this).width();
			
			var img_elem = '<img class="bgt_img_bg" src='+ img_src +' />';
			//var img_elem = '<img class="bgt_img_bg" style="z-index: -99; max-width:100%; position: absolute; top: 0; left: 0; max-height: 100%; min-width: 100%; min-height: 100%; width: '+width+'; height: '+ height +';" src='+ img_src +' />';
			
			jQuery(this).append(img_elem);
			
			//get the width and height of the image
			
			var img_height = jQuery(this).children(".bgt_img_bg").height();
			var img_width = jQuery(this).children(".bgt_img_bg").width();
			
			if (width/height < img_width/img_height)
			{
				jQuery(this).children(".bgt_img_bg").attr("style", 'z-index: -99; height: 100%; position: absolute; top: 0; left: 0; max-width: none !important; max-height: none !important;');
			} else
			{
				jQuery(this).children(".bgt_img_bg").attr("style", 'z-index: -99; width: 100%; position: absolute; top: 0; left: 0; max-width: none !important; max-height: none !important;');
			}
		});
		

		//jQuery(this).trigger("resize");
	};
	
	function bgt_bg_resize(elem_id)
	{
			var height = jQuery(elem_id).height();
			var width = jQuery(elem_id).width();
			var img_height = jQuery(elem_id).children(".bgt_img_bg").height();
			var img_width = jQuery(elem_id).children(".bgt_img_bg").width();
			
			console.log(height);
			console.log(img_height);
			
			if (width/height < img_width/img_height)
			{
				jQuery(elem_id).children(".bgt_img_bg").attr("style", 'z-index: -99; height: 100%; position: absolute; top: 0; left: 0; max-width: none !important; max-height: none !important;');
			} else
			{
				jQuery(elem_id).children(".bgt_img_bg").attr("style", 'z-index: -99; width: 100%; position: absolute; top: 0; left: 0; max-width: none !important; max-height: none !important;');
			}			

	}
	
	function bgt_pop_smart_positioning(position, id, width, height)
	{
		/* position the popup based on the position set by user
		 * 1. Get the current position of the popup by using offset
		 * 2. set the top and left accordingly to the position
		 * 3. Trigger this function on load and on resize
		 */
		var popup_width = 0;
		var popup_height = 0;
		
		if (!jQuery('#'+id).is(':visible'))
		{
			popup_width = width;
			popup_height = height;
		} else
		{
			popup_width = jQuery('#'+id).width();
			popup_height = jQuery('#'+id).height()
		}	
			
		var current_offset = jQuery('#'+id).offset();
		var window_width = jQuery(window).width();
		var window_height = jQuery(window).height();
		
		
		console.log("WH: "+window_height);
		console.log("WW: "+window_width);
		console.log("PH: "+popup_height);
		console.log("PW: "+popup_width);
		console.log("ROUND: "+Math.round( (window_height - popup_height)/2  ));
		//console.log(jQuery('#'+id));
		console.log(position);
		
		var off_top;
		var off_left;
		if (position == "pop_top_left")
		{
			off_top = 20 + jQuery(window).scrollTop();
			off_left = 20 + jQuery(window).scrollLeft();
		} else if (position == "pop_top_right")
		{
			off_top = 20 + jQuery(window).scrollTop();
			off_left = window_width - popup_width - 20;
			
		} else if (position == "pop_bottom_left")
		{
			off_top = window_height - popup_height - 20 + jQuery(window).scrollTop();
			off_left = 15 + jQuery(window).scrollLeft();
			
		} else if (position == "pop_bottom_right")
		{
			off_top = window_height - popup_height - 20 + jQuery(window).scrollTop();
			off_left = window_width - popup_width - 20 + jQuery(window).scrollLeft();
			
		} else if (position == "pop_center")
		{
			off_top = (window_height - popup_height)/2 + jQuery(window).scrollTop();
			off_left = current_offset.left;
		}
		
		jQuery('#'+id).offset({top: off_top, left: off_left});
		console.log("OFF_TOP: " +off_top);
		console.log("OFF_LEFT: " +off_left);
	}
	
	function bgt_pop_smart_positioning_action(position, id, width, height)
	{
		jQuery(document).ready(function(){
			bgt_pop_smart_positioning(position, id, width, height);
		});

		jQuery(window).resize(function(){
			bgt_pop_smart_positioning(position, id, width, height);
			
		});
	}
	
	
	