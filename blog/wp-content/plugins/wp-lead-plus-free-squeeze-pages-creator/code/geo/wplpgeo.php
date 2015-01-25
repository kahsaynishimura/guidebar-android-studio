<?php

	require_once("mm/src/geoip.inc");
	require_once("mm/src/geoipcity.inc");
	require_once("mm/src/geoipregionvars.php");
	
	function bgt_get_plugins_location_2()
	{
		$full_path = getcwd();
		
		if (substr($full_path, -1) !== "/" || substr($full_path, -1) !== "\\")
		{
			$full_path .= "/";
		}
		//perform regular xpression replace
		$pattern = '/wpleadplus[\/\\\\]/';
		
		$new_path = preg_replace($pattern, '*****', $full_path);
		
		
		$ar = explode("*****", $new_path);
		return $ar[0];
	}	
	
	function sq_bgt_get_country($ip)
	{
		$data = geoip_open(bgt_get_plugins_location_2()."wpleadplus/code/geo/GeoIP.dat", GEOIP_STANDARD);
		return  geoip_country_name_by_addr($data, $ip);
	}
	


