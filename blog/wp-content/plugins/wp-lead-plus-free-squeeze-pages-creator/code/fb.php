<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 
<head>
        <meta http-equiv="X-UA-Compatible" content="IE=7" />
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>FB</title>
<script src="../js/jq.js"></script>
</head>

<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=117879698376435";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<div class="fb-like" id="wplike" data-send="false" data-width="450" data-show-faces="true"></div>
<div id="checklog"></div>
<div id="checklog2">ok</div>
<script>
	$(document).ready(function(){
		setInterval(function(){
			var text = $("#wplike").html();

			if (text.indexOf("24px") == -1)
			{
				jQuery("#checklog").text("here");
				return;
			} else if (text.indexOf("24px") !== -1)
			{
				jQuery("#checklog").text("away");
				return;
			} else
			{
				jQuery("#checklog").text("not sure");
			}
			
			}, 1000);
		
		
	});
	

</script>
</body>
</html>