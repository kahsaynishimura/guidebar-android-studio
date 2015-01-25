jQuery(document).ready(function() {
	//Set up the Slider 
	
	jQuery("time.entry-date").timeago();	
	
	jQuery(".featured-thumb").hoverIntent(function() {
		jQuery('.img-meta',this).slideDown(600,'easeOutBounce'); 
		jQuery('.img-meta-link',this).css('margin-right','50px');
		jQuery('.img-meta-link',this).animate({'margin-right':'0px'},500);
	},
		function() {
		jQuery('.img-meta-link',this).animate({'margin-right':'50px'},500);
		//jQuery('.img-meta-link').css('margin-right','50px');
		jQuery('.img-meta',this).fadeOut('fast');
		//jQuery('.img-meta-link').stop(true,false);	
	});	
	
   	jQuery('a.meta-link-img').nivoLightbox();

	jQuery('.main-navigation .menu ul').superfish({
			delay:       1000,                            // 1 second avoids dropdown from suddenly disappearing
			animation:   {opacity:'show'},  			  // fade-in and slide-down animation
			speed:       'fast',                          // faster animation speed
			autoArrows:  false                            // disable generation of arrow mark-up
		});
	
	jQuery('.main-navigation ul.menu').mobileMenu({
		switchWidth: 768
	});
	
	jQuery('.menu-toggle').toggle(function() {
		jQuery('.td_mobile_menu_wrap').fadeIn();
	},
	function() {
		jQuery('.td_mobile_menu_wrap').hide();
	});
		
	jQuery(window).bind('scroll', function(e) {
		hefct();
	});		
	
	jQuery(function() {
 
	    // grab the initial top offset of the navigation 
	    var sticky_navigation_offset_top = jQuery('#top-bar').offset().top;
	     
	    // our function that decides weather the navigation bar should have "fixed" css position or not.
	    var sticky_navigation = function(){
	        var scroll_top = jQuery(window).scrollTop(); // our current vertical position from the top
	         
	        // if we've scrolled more than the navigation, change its position to fixed to stick to top,
	        if (scroll_top > sticky_navigation_offset_top) { 
	            jQuery('#top-bar').css({ 'position': 'fixed', 'top':0, 'width':'100%' });
	            jQuery("body.admin-bar #top-bar").css({ 'position': 'fixed', 'top':28, 'width':'100%' });
	        } else {
	            jQuery('#top-bar').css({ 'position': 'relative' }); 
	            jQuery('body.admin-bar #top-bar').css({ 'position': 'relative','top':0 }); 
	        }
	        if (jQuery(window).width() < 960 ) {
		         jQuery('#top-bar').css({ 'position': 'relative' }); 
	        }     
    };
     
    // run our function on load
    sticky_navigation();
     

// and run it again every time you scroll
    jQuery(window).scroll(function() {
         sticky_navigation();
    });
 

    });
});
  	
    	
function hefct() {
	var scrollPosition = jQuery(window).scrollTop();
	jQuery('#parallax-bg').css('top', (0 - (scrollPosition * .2)) + 'px');
}	