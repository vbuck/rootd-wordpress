<?php

/**
 * Rootd Framework base class.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

$basePath 	= dirname(__FILE__);
$paths 		= array();
$paths[]	= WP_PLUGIN_DIR; 																// Plugins
$paths[]	= $basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core';		// Framework Core
$paths[]	= $basePath;																	// Framework Lib

set_include_path(implode(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());

Rootd_Loader::addScope('Rootd');

final class Rootd
{

	/* @var $_basePath string */
	private static $_basePath;
	/* @var $_config Rootd_Config */
	private static $_config;
	private static $_modules = array();
	/* @var $_request Rootd_Request */
	private static $_request;

	/**
	 * Load configuration files.
	 * 
	 * @return void
	 */
	protected static function _loadConfig()
	{
		$config = self::$_config;
		if($config instanceof Rootd_Config)
		{
			foreach(array('core', 'plugin') as $base)
			{
				foreach(self::getModuleList($base) as $module)
				{
					$config->loadConfiguration(self::getBasePath($base, "{$module}/config.xml"));
				}
			}
		}
	}

	/**
	 * Translate the slashes in a path.
	 * 
	 * @param  	string $path
 	 * @return  string
	 */
	public function fixPath($path = '')
	{
		$translation = DIRECTORY_SEPARATOR === '/' ?
			array('from' => '\\\\', 'to' => '/') :
			array('from' => '/', 'to' => '\\\\');

		return str_replace($translation['from'], $translation['to'], $path);
	}

	/**
	 * Get a base path by type.
	 * 
	 * @param  	string $type
	 * @param  	string $subPath
	 * @return 	string
	 */
	public static function getBasePath($type = 'base', $subPath = '')
	{
		$basePath 	= '';
		$subPath 	= self::fixPath($subPath);

		switch($type)
		{
			case 'base':
				$basePath = self::$_basePath . DIRECTORY_SEPARATOR;
				break;
			case 'core':
				$basePath = self::$_basePath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR;
				break;
			case 'lib':
				$basePath = self::$_basePath . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
			case 'plugin':
			default:
				$basePath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR;
				break;
		}

		return $basePath . $subPath;
	}

	/**
	 * Get the configuration instance.
	 * 
	 * @return Rootd_Config
	 */
	public static function getConfig()
	{
		if(!self::$_config)
		{
			self::$_config = new Rootd_Config();
			self::_loadConfig();
		}

		return self::$_config;
	}

	/**
	 * Collect a list of modules by type.
	 * 
	 * @param  	string  $type
	 * @param  	boolean $reload
	 * @return 	array
	 */
	public static function getModuleList($type = 'core', $reload = false)
	{
		if(
			!isset(self::$_modules[$type]) ||
			(isset(self::$_modules[$type]) && $reload === true)
		)
		{
			$modules 	= array();
			$path 		= self::getBasePath($type);
			$dh 		= opendir($path);

			while(false !== ($item = readdir($dh)))
			{
				// Accepts any folder as a module, even non-Rootd plugins.
				// Member modules will be detected at use-time.
				if(is_dir($path . $item) && $item !== '.' && $item !== '..')
				{
					$modules[] = $item;
				}
			}

			closedir($dh);

			self::$_modules[$type] = $modules;
		}
		
		return self::$_modules[$type];
	}

	/**
	 * Get the request object.
	 * 
	 * @return Rootd_Request
	 */
	public function getRequest()
	{
		if(!self::$_request)
		{
			self::$_request = new Rootd_Request();
		}

		return self::$_request;
	}

	/**
	 * Load all module configurations.
	 * 
	 * @return void
	 */
	public static function loadModules()
	{
		$config = self::$_config;

		if(!($config instanceof Rootd_Config))
		{
			return false;
		}

		try
		{
			foreach($config->getNode('modules')->children() as $moduleKey => $moduleConfig)
			{
				$moduleName = (string) $moduleConfig->class;
				
				foreach($moduleConfig->features->children() as $featureKey => $featureConfig)
				{
					if((string) $featureConfig->enabled === 'true')
					{
						// @todo Determine initialization method based on $type
						$type 	= (string) $featureConfig->type;
						$class 	= $moduleName . '_' . (string) $featureConfig->class;

						call_user_func(array($class, 'register'));
					}
				}
			}
		}
		catch(Exception $error) { }
	}

	/**
	 * Register a plugin with the framework.
	 *
	 * Adds the module to the autoloader scope.
	 * 
	 * @param  	string $path
	 * @return 	void
	 */
	public static function registerPlugin($path)
	{
		$parts = explode(DIRECTORY_SEPARATOR, dirname($path));
		$scope = array_pop($parts);

		Rootd_Loader::addScope($scope);
	}

	/**
	 * Load and start the framework.
	 * 
	 * @return void
	 */
	public function run()
	{
		self::setBasePath(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'mu-plugins' . DIRECTORY_SEPARATOR . 'Rootd');
		self::getConfig();

		self::loadModules();
	}

	/**
	 * Set the base path for the framework.
	 * 
	 * @param 	string $path
	 * @return  void
	 */
	public static function setBasePath($path = '')
	{
		self::$_basePath = $path;
	}

}