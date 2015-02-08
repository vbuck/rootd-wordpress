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
     * Retrieve the latest core version details from service.
     * 
     * @return Rootd_Object
     */
    public static function getCoreVersionInfo()
    {
        $data = @file_get_contents(Rootd_Installer::CORE_VERSION_URL);

        if (!$data) {
            // If failed to retrieve, at least match the current version
            $data = array('version' => Rootd::getVersion());
        } else {
            $data = json_decode($data, true);
        }

        return new Rootd_Object($data);
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
     * Check for core update candidate.
     * 
     * @return boolean
     */
    public static function needsCoreUpdate()
    {
        // Always return true if update is in progress
        $stepData = Rootd_Installer::getStepData();
        if (substr($stepData['current_step'], 0, 6) == 'update') {
            return true;
        }
        
        if (class_exists('Rootd', false)) {
            $localVersion   = Rootd::getVersion();
            $currentVersion = self::getCoreVersionInfo()->getVersion();

            return version_compare($localVersion, $currentVersion) === -1;
        }

        return false;
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
     * Update the framework core.
     * 
     * @return void
     */
    public static function updateCore()
    {
        if (!current_user_can('activate_plugins')) {
            return false;
        }

        Rootd_Installer::update();
    }

    /**
     * Verify that the Rootd Framework is installed.
     * 
     * @return void
     */
    public static function verify()
    {
        if (self::needsCoreUpdate()) { // Now check for core updates
            add_action('admin_init', array(__CLASS__, 'updateCore'));

            return false;
        } else if (
            class_exists('Rootd', false) &&
            !self::hasStepData()
        ) {
            return true;
        } else if (is_admin()) {
            add_action('admin_init', array(__CLASS__, 'install'));

            return false;
        }

        return true;
    }

}