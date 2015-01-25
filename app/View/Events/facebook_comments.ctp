
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id))
                return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&appId=402213736551577&version=v2.0";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    <div class="fb-comments" data-mobile="true" data-href="http://guidebar.com.br/events/view/<?php echo $event['Event']['id']; ?>"  data-numposts="10" data-colorscheme="light"></div>
