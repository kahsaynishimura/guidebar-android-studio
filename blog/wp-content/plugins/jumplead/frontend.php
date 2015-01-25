<?php
// Hooks
add_action('wp_head', 'jumplead_tracking_code');
add_action('wp_footer', 'jumplead_automation_trigger_code');

// Create shortcode [jumplead_form]
add_shortcode('jumplead_form', 'jumplead_embed_form');


/**
 * Echo out the Jumplead Tracking Code
 * Echos the tracking code or just a comment if tracker id is invalid
 *
 * @return void
 */
function jumplead_tracking_code() {
    $tracker_id = get_option('jumplead_tracker_id');

    echo jumplead_comment('Tracking Code');

    if (jumplead_is_tracker_id_valid()) {
	    echo <<<JUMPLEAD
<script type="text/javascript">
    window.Jumplead||function(b,d){function k(){return["<",l,' onload="var d=',c,";d."+m+"('head')[0].",n,"(d.",p,"('script')).",e,"='",q,"'\"></",l,">"].join("")}var c="document",f=b[c],l="body",p="createElement",m="getElementsByTagName",r=f[m]("head")[0],n="appendChild",a=f[p]("iframe"),e="src",g,q="//cdn.jumplead.com/tracking_code.js";b.jump=b.jump||function(){(b.jump.q=b.jump.q||[]).push(arguments)};d.events=b.jump;a.style.display="none";r[n](a);try{a.contentWindow[c].open()}catch(s){d.domain=f.domain,
    g="javascript:var d="+c+".open();d.domain='"+f.domain+"';",a[e]=g+"void(0);"}try{var h=a.contentWindow[c];h.write(k());h.close();h.params=d}catch(t){a[e]=g+'d.write("'+k().replace(/"/g,'\"')+'");d.close();',a[e].contentDocument.params=d}}
    (window,{account:"{$tracker_id}",version:4});
</script>

JUMPLEAD;
    } else {
        echo jumplead_comment('Tracker ID ' . $tracker_id . ' is invalid');
    }
}

/**
 * Echo JavaScript to trigger an automation
 * Only echos JS if there's data to be sent to Jumplead
 *
 * @return void
 */
function jumplead_automation_trigger_code() {
    // Send data
    if (!empty(Jumplead::$data)) {
        // Get the data
        $data = Jumplead::$data;

        $nameFirst  = isset($data['name'])      ? trim($data['name'])       : '';
        $nameLast   = isset($data['name_last']) ? trim($data['name_last'])  : '';
        $email      = isset($data['email'])     ? json_encode($data['email'])   : 'null';
        $company    = isset($data['company'])   ? json_encode($data['company']) : 'null';

        // Populate automation, if there is one
        $automation = 'null';
        if (isset(Jumplead::$data['automation_id'])) {
            $automation_id = Jumplead::$data['automation_id'];
            if (!empty($automation_id)) {
                $automation_id = json_encode(Jumplead::$data['automation_id']);
                if ($automation_id) {
                    $automation = $automation_id;
                }
            }
        }

        // Concat name
        $name = json_encode(trim($nameFirst . ' ' . $nameLast));

        echo jumplead_comment('Automation Trigger');
        echo <<<HTML
<script type="text/javascript">
    var JumpleadWordPress = {
        createCookie: function (name,value,days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime()+(days*24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
            }
            else var expires = "";
            document.cookie = name+"="+value+expires+"; path=/";
        },
        readCookie: function(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        },
        eraseCookie: function(name) {
            this.createCookie(name,"",-1);
        }
    };


    var contact = {
        name: $name,
        email: $email,
        company: $company
    };
    jump('send', 'automation', 'trigger', $automation, contact);

HTML;


        foreach (JumpleadIntegration::$cookies as $cookie) {
            echo PHP_EOL . '    '; // Nice indenting
            echo 'JumpleadWordPress.eraseCookie("' . JumpleadIntegration::$cookiePrefix . $cookie . '"); ';
        }

        echo <<<HTML

</script>
HTML;
    }
}



/**
 * Short Tags
 */

// Embed form
function jumplead_embed_form($atts) {
    // Ensure ID is set
    if (!isset($atts['id']) || trim($atts['id']) == '') {
	    return 'Jumplead Error: Invalid Form ID';
    }

    $dataStr = 'data-id="' . $atts['id'] . '"';

    // JavaScript callback
    if (isset($atts['callback'])) {
        $dataStr .= ' data-callback="' . $atts['callback'] . '"';
    }

    return '<div class="jlcf" ' . $dataStr . '></div>';
}
