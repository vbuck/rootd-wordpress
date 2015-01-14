<?php

/**
 * Rootd module installer class.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Module_Installer
{

    const OPTION_NAME = 'rootd_modules';

    /**
     * Module resource data. Tracks module data (setup) versions only.
     * 
     * @var $_data array
     */
    protected $_data;
    protected $_hasUpdated = false;

    /**
     * Prepare the resource data.
     *
     * @return void
     */
    public function __construct()
    {
        //delete_option(self::OPTION_NAME);

        $data = json_decode(get_option(self::OPTION_NAME), true);

        if (is_array($data)) {
            $this->_data = $data;
        } else {
            $this->_data = array();
        }

        //Rootd::debug($this->_data, Rootd::DEBUG_CONTINUE);

        unset($data);
    }

    /**
     * Locate the setup path for the given module.
     * 
     * @param string $module The module key.
     * 
     * @return string|false
     */
    protected function _getModuleSetupPath($module)
    {
        $types = array('core', 'plugin');
        $class = (string) Rootd::getConfig()->getNode("modules/{$module}/class");

        foreach ($types as $type) {
            $path = Rootd::getBasePath($type, "{$class}/setup");

            if (file_exists($path)) {
                return $path;
            }
        }

        return false;
    }

    /**
     * Get all setup scripts (ordered) for the given module.
     * 
     * @param string $module The module key.
     * 
     * @return array
     */
    protected function _getSetupScripts($module)
    {
        $path       = $this->_getModuleSetupPath($module);
        $scripts    = array();
        $install    = null;

        if ($path) {
            $dir        = opendir($path);

            while ( false !== ( $file = readdir($dir) ) ) {
                if (preg_match('/^(install|upgrade)([\.\-0-9]+)php$/', $file, $matches)) {
                    if (strpos($file, 'install-') !== false) {
                        $install = array(
                            'version'   => $this->_parseVersion(end($matches)), 
                            'path'      => $path . DIRECTORY_SEPARATOR . $file,
                        );
                    } else {
                        $scripts[] = array(
                            'version'   => $this->_parseVersion(end($matches)),
                            'path'      => $path . DIRECTORY_SEPARATOR . $file,
                        );
                    }
                }
            }

            closedir($dir);
        }

        usort($scripts, array($this, 'sortScripts'));

        if ($install) {
            array_unshift($scripts, $install);
        }

        return $scripts;
    }

    /**
     * Trim a raw version string.
     * 
     * @param string $version The version string.
     * 
     * @return string
     */
    protected function _parseVersion($version)
    {
        return rtrim(ltrim($version, '-'), '.');
    }

    /**
     * Run setup scripts for the given module.
     * 
     * @param string $module The module key.
     * 
     * @return Rootd_Module_Installer
     */
    protected function _runUpdates($module)
    {
        global $wpdb;

        $scripts = $this->_getSetupScripts($module);

        foreach ($scripts as $script) {
            if (version_compare($script['version'], $this->getCurrentVersion($module)) > 0) {
                break;
            }

            if ($this->versionCompare($module, $script['version']) >= 0) {
                continue;
            }

            //echo "Would run {$script['path']}<br/>";
            include $script['path'];

            $this->_data[$module]   = $script['version'];
            $this->_hasUpdated      = true;
        }

        return $this;
    }

    /**
     * Get the current module version from config XML.
     * 
     * @param string $module The module key.
     * 
     * @return string
     */
    public function getCurrentVersion($module)
    {
        $config = Rootd::getConfig()->getNode("modules/{$module}");

        if ($config && !empty($config->version)) {
            return (string) $config->version;
        }

        return '0';
    }

    /**
     * Script sort comparator.
     * 
     * @param array $a Script object left.
     * @param array $b Script object right.
     * 
     * @return integer
     */
    public function sortScripts($a, $b)
    {
        $matchA = '';
        $matchB = '';

        preg_match('/[\.\-0-9]+/', basename($a['path']), $matchesA);

        if (!empty($matchesA)) {
            $matchA = $this->_parseVersion($matchesA[0]);

            unset($matchesA);
        }

        preg_match('/[\.\-0-9]+/', basename($b['path']), $matchesB);

        if (!empty($matchesB)) {
            $matchB = $this->_parseVersion($matchesB[0]);
            
            unset($matchesB);
        }

        return version_compare($matchA, $matchB);
    }

    /**
     * Update the given module.
     * 
     * @param string $module The module key.
     * 
     * @return Rootd_Module_Installer
     */
    public function update($module)
    {
        // Only allow updates in admin
        if (!is_admin()) {
            return $this;
        }

        if ($this->versionCompare($module, $this->getCurrentVersion($module)) < 0) {
            $this->_runUpdates($module);

            if ($this->_hasUpdated) {
                update_option(self::OPTION_NAME, json_encode($this->_data));
            }
        }

        return $this;
    }

    /**
     * Compare a given module version to it's data version.
     * 
     * @param string $module  The module key.
     * @param string $version A version to compare.
     * 
     * @return integer
     */
    public function versionCompare($module, $version)
    {
        if (!empty($this->_data[$module])) {
            return version_compare($this->_data[$module], $version);
        }

        return -1;
    }

}