<?php

if (!function_exists('curl_init')) {
	die('Curl module not installed!' . PHP_EOL);
}


$route = '/ping';
//$route = '/test/4';
//$route = '/doesntexist';
//$route = '/skip/auth';

if (isset($argv[1])) {
	$host = 'http://' . $argv[1] . $route;
} else {
	$host = "http://helio.api" . $route.".xml?token=oWMhwXXmSnSR5y3Xj9ljGzLmgu5q8I0JFtQuSrxg";
}

//$headers = ['ACCESS_TOKEN: cbx4S1QCBOmZHRan0z4jHkTWiqLKNeLrMMSFW68M'];


$ch = curl_init();

curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
curl_setopt($ch, CURLOPT_URL, $host);
curl_setopt($ch, CURLOPT_POST, FALSE);
curl_setopt($ch, CURLOPT_HEADER, TRUE);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);

$result = curl_exec($ch);
if ($result === FALSE) {
	echo "Curl Error: " . curl_error($ch);
} else {
	echo PHP_EOL;
	echo "Request: " . PHP_EOL;
	echo curl_getinfo($ch, CURLINFO_HEADER_OUT);	
	echo PHP_EOL;

	echo "Response:" . PHP_EOL;
	echo $result; 
	echo PHP_EOL;
}

curl_close($ch);


function buildMessage($time, $id, array $data) {
	return $time . $id . implode($data);
}

?>
