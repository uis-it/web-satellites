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

/*
class MyClass {
	public function hola ($inputParam) {	
		return "hola";
    }
}
*/

$server = new Zend_Soap_Server(null,array('uri' => "http://webmeeting.fronttest04.uis.no/soap.php"));
$server	
	->setClass("CMS_Meeting")
;
$server->setObject(new CMS_Meeting());
$server->setReturnResponse(true);
$response = $server->handle();
