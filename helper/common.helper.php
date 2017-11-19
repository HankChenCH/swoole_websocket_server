<?php

if (!function_exists('convertObliqueLine')) {
	function convertObliqueLine( $str , $ucfirst = true)
	{
	    while(($pos = strpos($str , '/'))!==false)
	        $str = substr($str , 0 , $pos).ucfirst(substr($str , $pos+1));
	 
	    return $ucfirst ? ucfirst($str) : $str;
	}
}

if (!function_exists('humpToLine')) {
	function humpToLine($str){
	    $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
	        return '/'.strtolower($matches[0]);
	    },$str);
	    return $str;
	}
}

if (!function_exists('config')) {
	function config($key)
	{
		$keyArr = explode('.', $key);

		if (is_array($keyArr) && is_file(__DIR__ . '/../config/' . $keyArr[0] . '.json')) {

			$jsonStr = file_get_contents(__DIR__ . '/../config/' . $keyArr[0] . '.json');
			$config = json_decode($jsonStr, true);

			if (count($keyArr) === 1) {
				return $config;
			} elseif (count($keyArr) === 2 && isset($config[$keyArr[1]])) {
				return $config[$keyArr[1]];
			}
		}

		return null;
	}
}

if (!function_exists('deep_array_search')) {
	function deep_array_search($search, $array)
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$deep_key = deep_array_search($search, $value);
				if (!is_null($deep_key)) {
					return [
						$key,
						$deep_key
					];
				}
			}

			if ($value == $search) {
				return $key;
			}
		}

		return null;
	}
}

if (!function_exists('getClientIp')) {
	function getClientIP($request)  
	{  
		$_SERVER = $request->server;
		
	    if (@$_SERVER["HTTP_X_FORWARDED_FOR"])  
	    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];  
	    else if (@$_SERVER["HTTP_CLIENT_IP"])  
	    $ip = $_SERVER["HTTP_CLIENT_IP"];  
	    else if (@$_SERVER["REMOTE_ADDR"])  
	    $ip = $_SERVER["REMOTE_ADDR"];  
	    else if (@getenv("HTTP_X_FORWARDED_FOR"))  
	    $ip = getenv("HTTP_X_FORWARDED_FOR");  
	    else if (@getenv("HTTP_CLIENT_IP"))  
	    $ip = getenv("HTTP_CLIENT_IP");  
	    else if (@getenv("REMOTE_ADDR"))  
	    $ip = getenv("REMOTE_ADDR");  
	    else  
	    $ip = "Unknown";  
	    return $ip;  
	}  
}