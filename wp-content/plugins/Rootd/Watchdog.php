<?php

/**
 * Rootd Framework watchdog.
 *
 * Verifies that the framework is installed, or else
 * prompts for installation.
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

final class Rootd_Watchdog
{

    /**
     * Activate the framework.
     * 
     * @return void
     */
    public static function activate()
    {
        self::checkRequest();

        add_option('rootd_framework_enabled', '1', '', 'yes');
    }

    /**
     * Check if activation permissions are available.
     * 
     * @return void
     */
    public static function checkRequest()
    {
        if (!current_user_can('activate_plugins')) {
            throw new Exception('You do not have permission to use this plugin.');
        }
    }

    /**
     * Deactivate the framework.
     * 
     * @return void
     */
    public static function deactivate()
    {
        self::checkRequest();

        if (class_exists('Rootd', false)) {
            delete_option('rootd_framework_enabled');
        }
    }

    /**
     * Get the expected path for the framework.
     * 
     * @return string
     */
    public static function getRootdBasePath()
    {
        return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 
            'mu-plugins' . DIRECTORY_SEPARATOR .
            'Rootd' . DIRECTORY_SEPARATOR .
            'lib' . DIRECTORY_SEPARATOR .
            'Base.php'
            ;
    }

    /**
     * Determine if step data exists.
     * 
     * @return boolean
     */
    public static function hasStepData()
    {
        return isset($_SESSION[Rootd_Installer::SESSION_DATA_KEY]);
    }

    /**
     * Initialize the watchdog.
     * 
     * @param   string $pluginPath
     * @return  void
     */
    public static function initialize($pluginPath)
    {
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Installer.php';

        register_activation_hook($pluginPath, array('Rootd_Watchdog', 'activate'));
        register_deactivation_hook($pluginPath, array('Rootd_Watchdog', 'deactivate'));
        register_uninstall_hook($pluginPath, array('Rootd_Watchdog', 'uninstall'));

        self::verify();
    }

    /**
     * Trigger framework installation.
     * 
     * @return void
     */
    public static function install()
    {
        if (!current_user_can('activate_plugins')) {
            return false;
        }

        Rootd_Installer::start();
    }

    /**
     * Trigger framework un-install.
     * 
     * @return void
     */
    public static function uninstall()
    {
        self::checkRequest();

        self::deactivate();
        
        Rootd_Installer::uninstall();
    }

    /**
     * Verify that the Rootd Framework is installed.
     * 
     * @return void
     */
    public static function verify()
    {
        if (
            (
                class_exists('Rootd_Base', false) ||
                file_exists(self::getRootdBasePath())
            ) &&
            !self::hasStepData()
        )
        {
            return true;
        } else if(is_admin()) {
            add_action('admin_init', array(__CLASS__, 'install'));
            return false;
        }

        return true;
    }

}