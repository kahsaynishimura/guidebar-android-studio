<?php
	include_once 'const.php';

    $http_array = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'cookies' => array()
    );

    function vgt_serialize_data($data)
    {
        return urlencode(base64_encode($data));
    }

    function vgt_de_serialize_data($data)
    {
        return base64_decode(urldecode($data));
    }
	
	//function to load index.html
	function vgt_load_theme_file($theme_path, $css_path)
	{

        if (function_exists("curl_init"))
        {
            $index_file = vgt_curl_load_file($theme_path);
            $css_file = vgt_curl_load_file($css_path);

            if (stripos($index_file, "<body>") === false)
            {
                $index_file = file_get_contents($theme_path);
                $css_file = file_get_contents($css_path);
            }
        } else
        {
            $index_file = file_get_contents($theme_path);
            $css_file = file_get_contents($css_path);
        }

        return array($index_file, $css_file);
	}


    //load themes files, css files with CURL
    function vgt_curl_load_file($path)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $index_file = curl_exec($ch);
        curl_close($ch);
        return $index_file;
    }


    function vgt_func_get_form($code)
    {
        $code = strtolower($code);
        $open_tag = stripos($code, "<form");

        $end_tag = stripos($code, "</form>");

        return substr($code, $open_tag, $end_tag + 7 - $open_tag);
    }

    function vgt_func_get_list_data()
    {
        $xe = get_option(base64_decode("dmd0X3dwbF91c2VyX2VtYWls"));
        $yr = get_option(base64_decode("dmd0X3dwbF91c2VyX3JlY2VpcHQ="));
        global $http_array;
        $http_array["body"] = array(base64_decode("ZW1haWw=") => $xe, base64_decode("cmVjZWlwdA==") => $yr);

        $fn = base64_decode("d3BfcmVtb3RlX3Bvc3Q=");
        $rt = $fn(get_option(base64_decode("dmd0X3dwbF9zZXJ2ZXJfdXJs")), $http_array);

        if (stripos($rt["body"], base64_decode("bGljZW5zZV9hY3RpdmF0ZWQ=")) === FALSE)
        {
            update_option(base64_decode("dmd0X3dwbF9wbHVnaW5fYWN0aXZhdGVk"), "full");
        }
    }