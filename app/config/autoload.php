<?php

/**
 * Auto Load Class files by namespace
 *
 * @eg 
 	'namespace' => '/path/to/dir'
 */
$vendorDir = dirname(dirname(dirname(__FILE__)))."/vendor";

$autoload = [
	'Events\Api' => $dir . '/library/events/api/',
	'Utilities\Debug' => $dir . '/library/utilities/debug/',
    'Utilities\Outputformats' => $dir . '/library/utilities/outputformats',
	'Application' => $dir . '/library/application/',
	'Interfaces' => $dir . '/library/interfaces/',
	'Controllers' => $dir . '/controllers/',
	'Models' => $dir . '/models/',
];

return $autoload;
