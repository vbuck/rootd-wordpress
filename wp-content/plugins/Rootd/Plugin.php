<?php

/**
 * Plugin Name:     Rootd Framework
 * Plugin URI:      http://blog.rickbuczynski.com/wordpress/rootd-framework
 * Description:     Provides enhancements to WordPress plugin development.
 * Version:         0.0.1
 * Author:          Rick Buczynski
 * Author URI:      http://rickbuczynski.com
 * License:         MIT
 * Text-Domain:     rootd-watchdog
 */

session_start();

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Watchdog.php';

if (!class_exists('Rootd_Watchdog', false)) {
    throw new Exception('Failed to initialize Rootd Framework because the plugin observer was not found.');
}

Rootd_Watchdog::initialize(__FILE__);