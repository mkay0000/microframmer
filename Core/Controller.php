<?php 
namespace Core;
/**
* Controller of the framework
*
* PHP v5.6
* @version v1.0
* @author Mikail Khan <khanmikail3@gmail.com>
*/
abstract class Controller
{
	/**
	 * __call magic method will callback methods which are not found
	 * @return void
	 */
	public function __call($name, $args){
		$method_name = preg_replace('/action/', '', $name);
		if($this->before() !== false){
			// if method exists
			if(method_exists($this, $method_name)){
				call_user_func_array([$this, $method_name], $args);
			}else {
				throw new \Exception("Method {$method_name} not found in " . get_class($this));
			}

			$this->after();
		}	
	}

	/**
	 * Use to be called before a function executes
	 * for e.g - Authentication CSRF
	 * 
	 * @return void
	 */
	protected function before()
	{

	}

	/**
	 * Use to be called after a function executes
	 * @return void
	 */
	protected function after()
	{

	}
}

?>