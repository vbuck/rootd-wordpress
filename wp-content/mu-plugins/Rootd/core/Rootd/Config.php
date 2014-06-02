<?php

/**
 * Rootd Framework configuration class.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Config
{

	protected $_elementClass = 'Rootd_Config_Element';
	/* @var $_xml Rootd_Config_Element */
	protected $_xml;
	/* @var $_prototype Rootd_Config */
	protected $_prototype;

	public function __construct($isPrototype = false)
	{
		if(!$isPrototype)
		{
			$this->_prototype = new Rootd_Config(true);
		}

		$this->loadFromString('<config />');
	}

	/**
	 * Merge in additional configuration.
	 * 
	 * @param  Rootd_Config $config
	 * @return Rootd_Config
	 */
	public function extend(Rootd_Config $config)
	{
		if($this->_xml instanceof SimpleXMLElement)
		{
			$this->getNode()->extend($config->getNode());
		}

		return $this;
	}

	/**
	 * Get a configuration node.
	 * @param  string $path
	 * @return Rootd_Config_Element
	 */
	public function getNode($path = null)
	{
		if(!($this->_xml instanceof SimpleXMLElement))
		{
			return false;
		}
		else if(is_null($path))
		{
			return $this->_xml;
		}
		else
		{
			return $this->_xml->descend($path);
		}
	}

	/**
	 * Load a configuration file by path.
	 * 
	 * @param  	string $path
	 * @return 	Rootd_Config
	 */
	public function load($path = null)
	{
		if(!is_readable($path))
		{
			return false;
		}

		$data = file_get_contents($path);

		return $this->loadFromString($data);
	}

	/**
	 * Load a configuration file into memory.
	 * 
	 * @param  	string|array $paths
	 * @return 	Rootd_Config
	 */
	public function loadConfiguration($paths = null)
	{
		if(!is_array($paths))
		{
			$paths = array($paths);
		}

		foreach($paths as $path)
		{
			$model = clone $this->_prototype;
			if($model->load($path))
			{
				$this->extend($model);
			}
		}

		return $this;
	}

	/**
	 * Set the configuration instance from a string.
	 * 
	 * @param  	string $string
	 * @return 	boolean
	 */
	public function loadFromString($string = '')
	{
		if(is_string($string))
		{
			$xml = simplexml_load_string($string, $this->_elementClass);

			if($xml instanceof SimpleXMLElement)
			{
				$this->_xml = $xml;

				return true;
			}
		}
		else 
		{
			throw new Exception('Failed to load malformed XML string: ' . $string);
		}

		return false;
	}

}