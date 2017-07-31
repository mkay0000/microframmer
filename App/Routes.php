<?php
/**
 * Routes 
 * 
 * @version v1.0
 * @author Mikail Khan <khanmikail3@gmail.com>
 */

// Router instance
$router = new Core\Router;

// Here goes your routes
$router->add("", ['controller' => "Home", 'action' => 'index']);


// DO NOT TOUCH THIS
$router->dispatch($_SERVER['QUERY_STRING']);
?> 