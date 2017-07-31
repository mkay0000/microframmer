<?php
/**
 *  Index of the framework
 */


// Classes Loader for the objects 
spl_autoload_register(function($class){
	$class = str_replace('\\', '/', $class);
	$class = dirname(__DIR__) . "/$class.php";
	if(is_readable($class)){
		require_once $class;
	}
});

use \Core\Router;

/**
 * Set Errors
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Twig Files loader
 * @version v1.0
 */
require_once dirname(__DIR__) . "/vendor/autoload.php";
Twig_Autoloader::register();

/**
 * Load Routes
 */
require_once dirname(__DIR__)."/App/Routes.php"; 
?>