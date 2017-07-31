<?php
namespace Core;

/**
* View Class for the MVC Framework
* 
* @version v1.0
* @author Mikail Khan <khanmikail3@gmail.com>
*/
class View 
{
	/**
	 * make() method adds the template to the controller action.
	 * 	
	 * @param  strong $template twig template 
	 * @param  array  $data     data to be passed
	 * @return template
	 */
	public static function make($template, $data = [])
	{
		static $twig = null;
		if($twig === null){
			$loader = new \Twig_Loader_Filesystem(dirname(__DIR__).'/App/Views/');
			$twig = new \Twig_Environment($loader);
		}
		echo $twig->render($template, $data);
	}
}

?>