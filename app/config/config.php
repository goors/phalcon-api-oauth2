<?php

/**
 * Settings to be stored in dependency injector
 */

$settings = array(
	'database' => array(
		'adapter' => 'Mysql',	/* Possible Values: Mysql, Postgres, Sqlite */
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'name' => 'helioapi',
		'port' => 3306
	),

    'oauth2' => array(
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => 'sanja11',
        'dbname'   => 'oauth2',
    )
);


return $settings;
