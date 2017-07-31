<?php 
namespace App\Controllers;
use Core\Controller;
Use App\Models\Post;
use Core\View;

/**
* Home Controller
*/

class Home extends Controller
{
	/**
	 * Displays the index page of mircroframmer
	 * @return view
	 */
	public function index()
	{
		View::make('index.html');
	}

	/**
	 * before() method executes before any other methods
	 * @return void
	 */
	public function before()
	{
		
	}

	/**
	 * after() method executes after any other methods
	 * @return void
	 */
	public function after()
	{
		
	}
}

?>