<?php

/**
 * Rootd Autoloader
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Loader
{

	/* @var $_instance Rootd_Autoloader */
	private static $_instance 	= null;
	private static $_isReady 	= false;
	private static $_scopes 	= array();

	/**
	 * Add a module to the autoload scope.
	 * 
	 * @param 	string $scope
	 * @return  void
	 */
	public static function addScope($scope)
	{
		self::$_scopes[] = $scope;
	}

	/**
	 * Auto-load a class.
	 * 
	 * @param  	string $class
	 * @return 	boolean
	 */
	public static function autoload($class = '')
	{
		if(!self::validateClass($class))
		{
			return false;
		}

		$file = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class))) . '.php';

		try
		{
			include $file;
		}
		catch(Exception $error) 
		{
			return false;
		}

		return true;
	}

	/**
	 * Register the autoloader.
	 * 
	 * @return void
	 */
	public static function initialize()
	{
		if(!self::$_isReady)
		{
			spl_autoload_register(array(self::instance(), 'autoload'));
			self::$_isReady = true;
		}
	}

	/**
	 * Generate the autoloader instance.
	 * 
	 * @return Rootd_Autoloaer
	 */
	public function instance()
	{
		if(!self::$_instance)
		{
			self::$_instance = new Rootd_Loader();
		}

		return self::$_instance;
	}

	/**
	 * Validate a class for module scope.
	 * 
	 * @param  	string $class
	 * @return 	boolean
	 */
	public function validateClass($class = '')
	{
		$path 	= explode('_', $class);
		$vendor = array_shift($path);

		return in_array($vendor, self::$_scopes);
	}

}