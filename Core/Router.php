<?php
namespace Core;

/**
 *  Router of the framework
 *  Deals with the URLs
 *
 * PHP > v5.6
 * @version v1.0
 * @author Mikail Khan <khanmikail3@gmail.com>
 */
class Router
{
	/**
	 * Stores the Routes 
	 * @var array routes 
	 */
	protected $_routes = [];

	/**
	 * Stores the arguments 
	 * @var array args
	 */
	protected $_args = [];

	/**
	 * matches is used by Dispatch method which holds routes linked with
	 * controllers and actons.
	 * @var array stores dispatches 
	 */
	protected $_dispatches = [];

	/**
	 * $_fetchies stores query vars from url
	 * @var array
	 */
	protected $_fetchies = [];

	# #######################
	#
	# 
	#  Public Methods 
	# 
	# #######################

	/**
	 * Adds URL to routes 
	 * @param array $routes routes to the url e.g /admin/dashboard
	 * @param array $args arguments for the route
	 *
	 * @return void
	 */
	public function add($routes, $args = []){
		// Check if the route don't exists before
		if(!empty($this->_routes)){
			foreach ($this->_routes as $route) {
				if($route == $routes){
					throw new \Exception("{$route} Route already exists");
				}
			}
		}
		$this->_routes[] = $this->removeForwardSlash($routes);
		$this->_args[] = $args;
	}


	/**
	 * Dispatch method dispatches routes to the Controllers action
	 * for example -
	 * $router->add("/admin/user", ['controller' => "Admin", "action" => "add_user"]);
	 *
	 * This will dispatch (/admin/user) to Contoller Admin@add_user
	 *
	 * @param string $query the url
	 * @return void
	 */
	
	public function dispatch($query)
	{
		if($this->match($query)){
			foreach ($this->_dispatches as $route) {
				$controller = $route['controller'];
				$action = $route['action'];
			}
			$controller = "\App\Controllers\\" . $controller;
			$action = 'action' . $action;
			if(class_exists($controller)){
				$controller = new $controller;
				if(is_callable([$controller, $action])){
					$controller->$action();
				}else {
					throw new \Exception("Method {$action} (in controller $controller) not found");
				}
			}else {
				throw new \Exception("Contoller $controller Not found");	
			}
		} else {
			throw new \Exception("404 Route not found", 404);
		}
	}
	
	

	# #####################
	# 
	# Core Protected Functions
	#
	#
	# ######################

	/**
	 * Matches the URL with the Routes and makes Controllers and Actions arguments to be dispatched
	 *
	 * @param string $query url the user is on - e.g http://localhost/show/product
	 * @return boolean True if the route matched else false
	 */
	protected function match($query)
	{
		// Formats the query 
		$query = $this->removeForwardSlash($query);
		$query = $this->removeQueryString($query);
		// loop through the routes to find the match for the query
		foreach ($this->_routes as $route_key => $route) {
			if($route == $query || $this->queryVarFetcher($query)){
				// Dispatches
				foreach ($this->_args as $arg_key => $arg) {
					if($arg_key == $route_key){
						$this->_dispatches[$route] = $arg;
					}
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * queryVarFetcher fetches the vars from URL and formats it for example -
	 * http://localhost/product/{variable}/show
	 * the method will fetch all variables in curly braces and store it into array.
	 *
	 * @param string $query query from which the vars will be fetched
	 * @return array of fetched vars
	 */
	public function queryVarFetcher($query)
	{
		
		
	}


	/**
	 * removeForwardSlash removes both start and end slash from the query url/route
	 * for e.g -
	 * URL - http://localhost/admin/
	 * Removed the last slash - http://localhost/admin [formatted] 
	 *
	 * Regex - 
	 * \/ matches the character / literally (case sensitive)
	 * $ asserts position at the end of the string, or before the line
	 * terminator right at the end of the string (if any)
	 *
	 * @param string $query The query to be formatted
	 * 
	 * @return string query Withoud end slash
	 */
	protected function removeForwardSlash($query){
		// Removes slash from the start
		$query = preg_replace("/^\//", "", $query);
		// Removes slash from the end
		$query = preg_replace("/\/$/", "", $query);

		return $query;
	}

	/**
	 * Removes the query string from the url for e.g http://microframmer/news?ad=mkay
	 * it will removes ?ad=mkay as it is a query string
	 * 
	 * @param  string $string string to be formatted
	 * @return string         formatted string
	 */
	protected function removeQueryString($string) 
	{
		return preg_replace("/\&.+/", '', $string);
	}

	# ######################
	#
	# 	Get Functions for Core Devs
	# 
	# ######################
	

	/**
	 * Gets the array of URLs 
	 * @return array 
	 */
	public function getRoutes()
	{
		return $this->_routes;
	} 

	/**
	 * Gets the array of URLs 
	 * @return array 
	 */
	public function getArgs()
	{
		return $this->_args;
	} 

	 /**
	 * Gets the array of Dispatches 
	 * @return array 
	 */
	public function getDispatches()
	{
		return $this->_dispatches;
	} 
}

?>