<?php
namespace App;

/**
* Configuration of the fremework
*
* @version v1.0
* @author Mikail Khan <khanmikail3@gmail.com>
*/
class Config
{
	# ######################
	#
	# Database Data
	# 
	# ######################
	
	/**
	 * DBdriver Stores the driver of database
	 * i.e mysql
	 * 
	 * @var const
	 */
	const DBDRIVER = 'mysql';

	/**
	 * DBHOST Stores the host of database 
	 * i.e localhost
	 * 
	 * @var const
	 */
	const DBHOST = 'localhost';

	/**
	 * DBNAME Stores the name of database
	 * 
	 * @var const
	 */
	const DBNAME = 'microframmer';

	/**
	 * DBUSER Stores the user of database 
	 * i.e root
	 * 
	 * @var const
	 */
	const DBUSER = 'root';

	/**
	 * DBPASS Stores the password of database 
	 * i.e secretPassword
	 * 
	 * @var const
	 */
	const DBPASS = '';


	/**
	 * DEVMODE allows user to switch on/off errors.
	 * NOTE: Set it to false in production mode.
	 * 
	 * @var boolean
	 */
	const DEVMODE = true;

}
?>