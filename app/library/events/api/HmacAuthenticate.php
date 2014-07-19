<?php

/**
 * Event that Authenticates the client message with HMac
 *
 * @package Events
 * @subpackage Api
 * @author Jete O'Keeffe
 * @version 1.0
 */

namespace Events\Api;

use Interfaces\IEvent as IEvent;

class HmacAuthenticate extends \Phalcon\Events\Manager implements IEvent {


    protected $oauthConfig;

	/**
	 * Constructor
	 *
	 * @param object
	 * @param string
	 */
	public function __construct($oauthConfig){
        $this->oauthConfig = $oauthConfig;


		// Add Event to validate message
		$this->handleEvent();
	}

	/**
	 * Setup an Event
	 *
	 * Phalcon event to make sure client sends a valid message
	 * @return FALSE|void
	 */
	public function handleEvent() {

		$this->attach('micro', function ($event, $app) {
			if ($event->getType() == 'beforeExecuteRoute') {

                if(!preg_match("/access/", $app->request->getURI())){
                    $oauthdb = new \Phalcon\Db\Adapter\Pdo\Mysql($this->oauthConfig->toArray());


                    $session = new \Oauth2\Server\Storage\Pdo\Mysql\Session($oauthdb);
                    $valid = $session->validateAccessToken($app->request->getHeaders()['ACCESS_TOKEN']);
                    if(!$valid){
                        $response = new \Phalcon\Http\Response();
                        $response->setStatusCode(401, "Unauthorized");
                        $response->setContent("Access denied");
                        $response->send();
                        return false;
                    }
                    return true;
                }
			}
		});
	}
}
