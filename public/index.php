<?php

/**
 * Driver for PHP HMAC Restful API using PhalconPHP's Micro framework
 * 
 * @package None
 * @author  Jete O'Keeffe 
 * @license none
 */


// Setup configuration files
$dir = dirname(__DIR__);
$appDir = $dir . '/app';
$vendorDir = dirname(dirname(__FILE__))."/vendor";

// Necessary requires to get things going
require $appDir . '/library/utilities/debug/PhpError.php';
require $appDir . '/library/interfaces/IRun.php';
require $appDir . '/library/application/Micro.php';

// Capture runtime errors
register_shutdown_function(['Utilities\Debug\PhpError','runtimeShutdown']);

// Necessary paths to autoload & config settings
$configPath = $appDir . '/config/';
$config = $configPath . 'config.php';
$autoLoad = $configPath . 'autoload.php';
$routes = $configPath . 'routes.php';

/*
     * auto load vendor
     */
require __DIR__ ."/../vendor/autoload.php";

use \Models\Api as Api;

try {
	$app = new Application\Micro();

	// Record any php warnings/errors
	set_error_handler(['Utilities\Debug\PhpError','errorHandler']);

	// Setup App (dependency injector, configuration variables and autoloading resources/classes)
	$app->setAutoload($autoLoad, $appDir, $vendordir);
	$app->setConfig($config);


    $oauthConfig = $app->getOauth2Config($config);

	// Setup HMAC Authentication callback to validate user before routing message
	// Failure to validate will stop the process before going to proper Restful Route
	$app->setEvents(new \Events\Api\HmacAuthenticate($oauthConfig));

	// Setup RESTful Routes
	$app->setRoutes($routes);

    $app->setService('oauth2', function() use ($oauthConfig) {


        $oauthdb = new Phalcon\Db\Adapter\Pdo\Mysql($oauthConfig->toArray());
        $server = new League\OAuth2\Server\Authorization(

            new Oauth2\Server\Storage\Pdo\Mysql\Client($oauthdb),
            new Oauth2\Server\Storage\Pdo\Mysql\Session($oauthdb),
            new Oauth2\Server\Storage\Pdo\Mysql\Scope($oauthdb)
        );
        # Not required as it called directly from original code
        # $request = new \League\OAuth2\Server\Util\Request();

        # add these 2 lines code if you want to use my own Request otherwise comment it
        $request = new \Oauth2\Server\Storage\Pdo\Mysql\Request();
        $server->setRequest($request);

        $server->setAccessTokenTTL(86400);
        $server->addGrantType(new League\OAuth2\Server\Grant\ClientCredentials());
        return $server;
    });

    $app->get('/access', function () use ($app) {

        try {
            $params = $app->oauth2->getParam(array('client_id', 'client_secret'));
            echo json_encode(
                $app->oauth2
                    ->getGrantType('client_credentials')
                    ->completeFlow($params)
            );

        } catch (League\OAuth2\Server\Exception\ClientException $e) {
            //var_export($e);
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    });

    $app->finish(function () use ($app) {


        if(!preg_match("/access/", $app->request->getURI())){
            // check format
            $format = $app->request->getQuery('format', 'string', 'json');

            switch ($format) {
                case 'json':
                    echo (json_encode($app->getReturnedValue()));
                    break;
                case 'xml':
                    print \Utilities\Outputformats\ArrayToXML::toXml($app->getReturnedValue());
                    break;
            }
        }
    });

    // Boom, Run
	$app->run();

} catch(Exception $e) {
	// Do Something I guess, return Server Error message
	$app->response->setStatusCode(500, "Server Error");
	$app->response->setContent($e->getMessage());
	$app->response->send();
}
