<?php

if(get_option('rootd_framework_enabled'))
{
	/**
	 * Plugin Name: 	Rootd Framework
	 * Plugin URI: 		http://rickbuczynski.com/wordpress/rootd-framework
	 * Description: 	Required extensions to WordPress for all Rootd plugins.
	 * Version: 		0.0.1
	 * Author: 			Rick Buczynski
	 * Author URI: 		http://rickbuczynski.com
	 * License: 		MIT
	 */

	$rootdLibDir = 
		dirname(__FILE__) . DIRECTORY_SEPARATOR .
		'Rootd' . DIRECTORY_SEPARATOR .
		'lib' . DIRECTORY_SEPARATOR;

	require_once $rootdLibDir . 'Loader.php';
	require_once $rootdLibDir . 'Base.php';

	Rootd_Loader::initialize();
	Rootd::run();
}