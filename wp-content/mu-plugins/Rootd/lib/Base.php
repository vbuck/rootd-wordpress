<?php

/**
 * Rootd Framework base class.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

defined('APPLICATION_ENV') || 
define(
    'APPLICATION_ENV', 
    (
        getenv('APPLICATION_ENV') ? 
            getenv('APPLICATION_ENV') : 
            'production'
    )
);

$basePath   = dirname(__FILE__);
$paths      = array();
$paths[]    = WP_PLUGIN_DIR;                                                                // Plugins
$paths[]    = $basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core';        // Framework Core
$paths[]    = $basePath;                                                                    // Framework Lib

set_include_path(implode(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());

Rootd_Loader::addScope('Rootd');

final class Rootd
{

    const DEBUG_CONTINUE            = 'continue_execution';
    const DEBUG_PRINT_NORMAL        = 'normal_format';
    const DEFAULT_LOG_GROUP         = 'default';

    /* @var $_basePath string */
    private static $_basePath;
    /* @var $_config Rootd_Config */
    private static $_config;
    private static $_coreLog        = array();
    private static $_classCache     = array();
    private static $_modules        = array();
    private static $_registry       = array();
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

        if ($config instanceof Rootd_Config) {
            foreach (array('core', 'plugin') as $base) {
                foreach (self::getModuleList($base) as $module) {
                    $config->loadConfiguration(self::getBasePath($base, "{$module}/config.xml"));

                    // Auto-load scopes for core modules
                    if ($base == 'core' && $module != 'Rootd') {
                        Rootd_Loader::addScope($module);
                    }
                }
            }
        }

        // Load user-defined configuration
        $config->loadConfiguration(self::getBasePath('base', 'local.xml'));
    }

    /**
     * Wrapper for var_dump.
     * 
     * @return void
     */
    public static function debug()
    {
        $args       = func_get_args();
        $printPre   = false;
        $stopExec   = false;

        end($args);
        if (current($args) !== self::DEBUG_CONTINUE) {
            $stopExec = true;
        } else {
            array_pop($args);
        }

        end($args);
        prev($args);
        if (current($args) !== self::DEBUG_PRINT_NORMAL) {
            $printPre = true;
        } else {
            array_pop($args);
        }

        reset($args);

        if ($printPre) {
            echo '<pre>';
        }

        call_user_func_array('var_dump', $args);

        if ($printPre) {
            echo '</pre>';
        }

        if ($stopExec) {
            exit;
        }
    }

    /**
     * Translate the slashes in a path.
     * 
     * @param   string $path
     * @return  string
     */
    public static function fixPath($path = '')
    {
        $translation = DIRECTORY_SEPARATOR === '/' ?
            array('from' => '\\\\', 'to' => '/') :
            array('from' => '/', 'to' => '\\\\');

        return str_replace($translation['from'], $translation['to'], $path);
    }

    /**
     * Get a base path by type.
     * 
     * @param   string $type
     * @param   string $subPath
     * @return  string
     */
    public static function getBasePath($type = 'base', $subPath = '')
    {
        $basePath   = '';
        $subPath    = self::fixPath($subPath);

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

    public static function getIsModuleEnabled($module)
    {
        $file   = self::locatePluginFile(self::getBasePath('plugin', $module));
        $state  = false;

        if (file_exists($file)) {
            // For compatibility on front-end
            include_once ABSPATH . 'wp-admin/includes/plugin.php';

            $path           = explode(DIRECTORY_SEPARATOR, $file);
            $relativePath   = implode(DIRECTORY_SEPARATOR, (array_slice($path, count($path) - 2)));

            // Requires a path relative to the plugins directory
            $state = is_plugin_active($relativePath);
        }

        return $state;
    }

    /**
     * Get the core log.
     * 
     * @param string $group The log group.
     * 
     * @return array
     */
    public static function getLog($group = self::DEFAULT_LOG_GROUP)
    {
        if (isset(self::$_coreLog[$group])) {
            return self::$_coreLog[$group];
        }

        return array();
    }

    /**
     * Collect a list of modules by type.
     * 
     * @param   string  $type
     * @param   boolean $reload
     * @return  array
     */
    public static function getModuleList($type = 'core', $reload = false)
    {
        if(
            !isset(self::$_modules[$type]) ||
            (isset(self::$_modules[$type]) && $reload === true)
        )
        {
            $modules    = array();
            $path       = self::getBasePath($type);
            $dh         = opendir($path);

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
     * Locate the path to the PHP binary.
     * 
     * @return string
     */
    public static function getPhpBinaryPath()
    {
        if (defined('PHP_BINARY')) {
            return constant('PHP_BINARY');
        }

        if (defined('PHP_BINDIR')) {
            $path = constant('PHP_BINDIR');
        } else {
            return false;
        }

        if (file_exists($path . DIRECTORY_SEPARATOR . 'php5')) {
            return $path . DIRECTORY_SEPARATOR . 'php5';
        }

        // Hope for the best
        return $path . DIRECTORY_SEPARATOR . 'php';
    }

    /**
     * Get the request object.
     * 
     * @return Rootd_Request
     */
    public static function getRequest()
    {
        if(!self::$_request)
        {
            self::$_request = new Rootd_Request();
        }

        return self::$_request;
    }

    /**
     * Get the default session model.
     * 
     * @return Rootd_Session
     */
    public static function getSession()
    {
        return self::getSingleton('Rootd_Session');
    }

    public static function getSingleton($class)
    {
        if (class_exists($class)) {
            if (!isset(self::$_classCache[$class])) {
                $instance = new $class();

                self::$_classCache[$class] = $instance;
            }
            
            return self::$_classCache[$class];
        }

        return null;
    }

    public static function hasModule($name) 
    {
        $node = self::getConfig()->getNode('global/modules');

        foreach ($node->children as $module) {
            if (strcasecmp($module->getName(), $name) === 0) {
                return true;
            }
        }

        if (class_exists("$name_Plugin")) {
            return true;
        }

        return false;
    }

    public static function helper($name)
    {
        $class = (string) self::getConfig()->getNode("helpers/{$name}");

        if ($class) {
            if (!isset(self::$_classCache[$class])) {
                $instance = new $class();

                self::$_classCache[$class] = $instance;
            }
            
            return self::$_classCache[$class];
        }

        return null;
    }

    /**
     * Load all module configurations.
     * 
     * @return void
     */
    public static function loadModules()
    {
        $config = self::$_config;

        if (!($config instanceof Rootd_Config)) {
            return false;
        }

        try {
            foreach($config->getNode('modules')->children() as $moduleKey => $moduleConfig)
            {
                $moduleName = (string) $moduleConfig->class;

                if (!self::getIsModuleEnabled($moduleName)) {
                    continue;
                }
                
                foreach ($moduleConfig->features->children() as $featureKey => $featureConfig) {
                    if ((string) $featureConfig->enabled === 'true') {
                        // @todo Determine initialization method based on $type
                        $type   = (string) $featureConfig->type;
                        $class  = $moduleName . '_' . (string) $featureConfig->class;

                        call_user_func(array($class, 'register'));
                    }
                }
            }
        } catch(Exception $error) { }
    }

    public static function locatePluginFile($path)
    {
        if (!is_dir($path)) {
            return false;
        }

        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $dh         = opendir($path);
        $filePath   = null;

        while (false !== ($file = readdir($dh))) {
            if ($file != '.' && $file != '..') {
                $filePath   = $path . DIRECTORY_SEPARATOR . $file;
                $data       = get_plugin_data($filePath);

                if ($data['Name'] != '') {
                    break;
                }
            }
        }

        closedir($dh);

        return $filePath;
    }

    /**
     * Core logging mechanism.
     * 
     * @param string $message The message to log.
     * @param string $group   The group to which the message belongs.
     * 
     * @return void
     */
    public static function log($message, $group = self::DEFAULT_LOG_GROUP)
    {
        if (!isset(self::$_coreLog[$group])) {
            self::$_coreLog[$group] = array();
        }

        self::$_coreLog[$group][] = $message;
    }

    /**
     * Register data to the internal store.
     * 
     * @param string $key   The registry key.
     * @param mixed  $value The item value.
     * 
     * @return void
     */
    public static function register($key, $value)
    {
        if(isset(self::$_registry[$key]))
        {
            throw new Exception("Registry entry already exists for {$key}.");
        }

        self::$_registry[$key] = $value;
    }

    /**
     * Register a plugin with the framework.
     *
     * Adds the module to the autoloader scope.
     * 
     * @param   string $path
     * @return  void
     */
    public static function registerPlugin($path)
    {
        $parts = explode(DIRECTORY_SEPARATOR, dirname($path));
        $scope = array_pop($parts);

        Rootd_Loader::addScope($scope);
    }

    /**
     * Get data from the internal store.
     * 
     * @param string $key The registry key.
     * 
     * @return mixed
     */
    public static function registry($key)
    {
        if(isset(self::$_registry[$key]))
        {
            return self::$_registry[$key];
        }

        return null;
    }

    /**
     * Load and start the framework.
     * 
     * @return void
     */
    public static function run()
    {
        self::setBasePath(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'mu-plugins' . DIRECTORY_SEPARATOR . 'Rootd');
        self::getConfig();

        self::loadModules();
    }

    /**
     * Set the base path for the framework.
     * 
     * @param   string $path
     * @return  void
     */
    public static function setBasePath($path = '')
    {
        self::$_basePath = $path;
    }

    /**
     * Remove data from the internal store.
     * 
     * @param string $key   The registry key.
     * 
     * @return void
     */
    public static function unregister($key)
    {
        if (isset(self::$_registry[$key])) {
            unset(self::$_registry[$key]);
        }
    }

}