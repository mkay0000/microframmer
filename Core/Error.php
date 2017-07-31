<?php
namespace Core;

/**
* Error Class 
* Handles all sorts of errors/exceptions and display default message.
*
* @version v1.0
* @author Mikail Khan <khanmikail3@gmail.com>
*/
class Error
{
	/**
	 * Converts all php errors into exceptions by throwing new ErrorException
	 *
	 * @param string $message The Exception message to throw.
	 * @param int $line The line number where the exception is thrown.
	 * @param int $level The severity level of the exception.
	 * @param string $file The filename where the exception is thrown. 
	 */
	static public function errorHandler($level, $message, $file, $line)
	{
		if(error_reporting() !== 0){ // to keep the operator working
			throw new \ErrorException($message, 0, $level, $file, $line);
		}
	} 

	/**
	 * Exception Handler.
	 *
	 * @param Exception $exception The Exception
	 * @return void
	 */
	static public function exceptionHandler($exception)
	{
		$code = $exception->getCode();
		if($code != 404){
			// if http code isn't 404 not found set it to 500 internal server error
			$code = 500;
		}
		http_response_code($code);

		$err = [
			"class" => get_class($exception),
			"msg" => $exception->getMessage(),
			"code" => $exception->getCode(),
			"file" => $exception->getFile(),
			"line" => $exception->getLine(),
			"trace" => $exception->getTraceAsString()
		];

		if(\App\Config::DEVMODE){
			if($code == 500){
				View::make("Errors/500.html", $err);
			}else{
				View::make("Errors/404.html", $err);
			}
		}else {
			// Log Errors
			$log = dirname(__DIR__) . "/logs/" . date("h-i-s d-m-Y") . ".txt";
			ini_set("error_log", $log);
			$error = "Message: " . $err['msg'] . "\r";
			$error .= "Code: " . $err['code'] . "\r";
			$error .= "File: " . $err['file'] . "\r";
			$error .= "Line: " . $err['line'] . "\r";
			$error .= "Trace: " . $err['trace'] . "\r\n";
			error_log($error);
			
			if($code == 500){
				View::make("Errors/serverError500.html", $err);
			}else{
				View::make("Errors/404.html", $err);
			}
		}
	}
}

?>