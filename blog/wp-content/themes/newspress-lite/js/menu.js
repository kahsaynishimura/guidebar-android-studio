jQuery(document).ready(function(){ jQuery("#newspress-main-menu ul ul").css({display: "none"}); jQuery('#newspress-main-menu ul li').hover( function() { jQuery(this).find('ul:first').slideDown(200).css('visibility', 'visible'); jQuery(this).addClass('selected'); }, function() { jQuery(this).find('ul:first').slideUp(200); jQuery(this).removeClass('selected'); }); });


		jQuery(document).ready(function() { 
			// Show or hide the sticky footer button
			jQuery(window).scroll(function() {
				if (jQuery(this).scrollTop() > 200) {
					jQuery('.go-top').fadeIn(200);
				} else {
					jQuery('.go-top').fadeOut(200);
				}
			});
			
			// Animate the scroll to top
			jQuery('.go-top').click(function(event) {
				event.preventDefault();
				
				jQuery('html, body').animate({scrollTop: 0}, 500);
			})
		});