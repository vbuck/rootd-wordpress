<?php

/**
 * Rootd Framework configuration class.
 *
 * @package     Rootd
 * @author      Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Config
{

    const TABLE_NAME = 'rootd_config';

    protected $_elementClass = 'Rootd_Config_Element';
    /* @var $_xml Rootd_Config_Element */
    protected $_xml;
    /* @var $_prototype Rootd_Config */
    protected $_prototype;

    /**
     * Prepare configuration structure.
     * 
     * @param boolean $isPrototype
     *
     * @return void
     */
    public function __construct($isPrototype = false)
    {
        if (!$isPrototype) {
            $this->_prototype = new Rootd_Config(true);
        }

        $this->loadFromString('<config />');
    }

    /**
     * Convert a multi-dimensional array to a key/value pair set
     * of configuration path values.
     * 
     * @param array   $element The input element.
     * @param string  $path    The current path in the operation.
     * @param integer $depth   The current depth in the operation.
     * 
     * @return array
     */
    protected function _flattenArray($element = array(), $path = '', $depth = 0)
    {
        $config = array();

        if (is_array($element)) {
            foreach ($element as $childKey => $childValue) {
                $config += $this->_flattenArray($element[$childKey], "{$path}{$childKey}/", ($depth + 1));
            }
        } else {
            $key = $depth > 0 ? preg_replace('#/*$#', '', $path) : "{$path}{$element}";
            $config[$key] = $element;
        }

        return $config;
    }

    /**
     * Flatten a form data set into configuration paths.
     * 
     * @param array $data The input form data.
     * 
     * @return array
     */
    public function convertFormToConfig(array $data, $prefix = '') 
    {
        return $this->_flattenArray($data, $prefix);
    }

    /**
     * Merge in additional configuration.
     * 
     * @param Rootd_Config $config
     * 
     * @return Rootd_Config
     */
    public function extend(Rootd_Config $config)
    {
        if ($this->_xml instanceof SimpleXMLElement) {
            $this->getNode()->extend($config->getNode());
        }

        return $this;
    }

    /**
     * Get a configuration node.
     * @param string $path
     * 
     * @return Rootd_Config_Element
     */
    public function getNode($path = null)
    {
        if (!($this->_xml instanceof SimpleXMLElement)) {
            return false;
        } else if (is_null($path)) {
            return $this->_xml;
        } else {
            return $this->_xml->descend($path);
        }
    }

    /**
     * Get the configuration database table name.
     * 
     * @return string
     */
    public function getTableName()
    {
        global $wpdb;

        return $wpdb->prefix . self::TABLE_NAME;
    }

    /**
     * Load a configuration file by path.
     * 
     * @param string $path
     * 
     * @return Rootd_Config
     */
    public function load($path = null)
    {
        if (!is_readable($path)) {
            return false;
        }

        $data = file_get_contents($path);

        return $this->loadFromString($data);
    }

    /**
     * Load configuration data from the database.
     * 
     * @return Rootd_Config
     */
    public function loadDbConfiguration()
    {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT * FROM `{$this->getTableName()}`", 
            ARRAY_A
        );

        // Simple write of values to existing nodes
        // Future versions may support more intelligent merging
        foreach ($results as $row) {
            $node = $this->getNode($row['path']);

            if ($node && !$node->hasChildren()) {
                $node[0] = $row['value'];
            }
        }

        return $this;
    }

    /**
     * Load a configuration file into memory.
     * 
     * @param string|array $paths
     * 
     * @return Rootd_Config
     */
    public function loadConfiguration($paths = null)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }

        foreach ($paths as $path) {
            $model = clone $this->_prototype;

            if ($model->load($path)) {
                $this->extend($model);
            }
        }

        return $this;
    }

    /**
     * Set the configuration instance from a string.
     * 
     * @param string $string
     * 
     * @return boolean
     */
    public function loadFromString($string = '')
    {
        if (is_string($string)) {
            $xml = simplexml_load_string($string, $this->_elementClass);

            if ($xml instanceof SimpleXMLElement) {
                $this->_xml = $xml;

                return true;
            }
        } else  {
            throw new Exception('Failed to load malformed XML string: ' . $string);
        }

        return false;
    }

    /**
     * Write a configuration value to the database.
     * 
     * @param string $path  The configuration path.
     * @param string $value The configuration value.
     *
     * @return Rootd_Config
     */
    public function setNodeValue($path, $value = '')
    {
        global $wpdb;

        try {
            $node = $this->getNode($path);

            // Only write if node exists in configuration XML
            // May change as future versions support better merging
            if ($node && !$node->hasChildren()) {
                $statement = $wpdb->prepare(
                    "INSERT INTO `{$this->getTableName()}` (`path`, `value`) VALUES (%s, %s) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)",
                    $path,
                    $value
                );

                $wpdb->query($statement);
            
                // Update existing node
                $node[0] = $value;
            }
        } catch (Exception $error) { } // @todo Exception

        return $this;
    }

}