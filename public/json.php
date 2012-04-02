<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 

 'production'));

					  


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';
require_once ( 'Utils.php');

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();


// Instantiate server, etc.
$server = new Zend_Json_Server();
$server
//	->setClass('CMS_Api')
	->setClass("CMS_Meeting")
;

if ('GET' == $_SERVER['REQUEST_METHOD']) {
	// Indicate the URL endpoint, and the JSON-RPC version used:
	$server->setTarget('/json.php')
		->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
	
	// Grab the SMD
	$smd = $server->getServiceMap();
	
	// Return the SMD to the client
	header('Content-Type: application/json');
	echo $smd;
	return;
}
$server->handle();
