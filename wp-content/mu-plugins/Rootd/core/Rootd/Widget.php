<?php

class Rootd_Widget extends WP_Widget
{

	const CACHE_TAG = 'WIDGET';

	protected $_cacheData = array(
		'cache_key'	=> 'WIDGET',
		'lifetime' 	=> 3600,
		'path'		=> '/tmp'
	);
	protected $_templates = array(
		'form' 		=> null,
		'widget' 	=> null
	);

	public function __construct()
	{
		$this->_construct();
	}

	public function _construct()
	{
		return $this;
	}

	protected function _afterRender($output = '')
	{
		return $output;
	}

	protected function _beforeRender()
	{
		return $this;
	}

	protected function _cacheOutput($output = '')
	{
		try {
			$path 	= $this->_getCachePath();
			$fp 	= fopen($path, 'w');

			flock($fp, LOCK_EX);
			fputs($fp, $output);
			flock($fp, LOCK_UN);

			fclose($fp);
		}
		catch(Exception $error) { }

		return $this;
	}

	protected function _fetchView($area = 'widget')
	{
		$view = '';

		try {
			ob_start();

			include_once $this->_getTemplatePath($area);
			
			$view = ob_get_contents();

			ob_end_clean();
		}
		catch(Exception $error)
		{
			return $this->__('Failed to fetch view in ' . get_class($this));
		}

		return $view;
	}

	protected function _getCachePath()
	{
		return
			$this->_cacheData['path'] . DIRECTORY_SEPARATOR . PATH_SEPARATOR .
			$this->_cacheData['cache_key'] . '_' .
			self::CACHE_TAG . '_' .
			session_id();
	}

	protected function _getTemplatePath($area)
	{
		$path = null;

		if(isset($this->_templates[$area]))
		{
			$moduleName = array_shift((explode('_', get_class($this))));
			$path 		= Rootd::getBaseDir() . $moduleName . DIRECTORY_SEPARATOR . $this->_templates[$area];
		}

		return $path;
	}

	protected function _init(
		$idBase = false, 
		$name, 
		array $widgetOptions = array(), 
		array $controlOptions = array()
	)
	{
		parent::__construct($idBase, $name, $widgetOptions, $controlOptions);

		return $this;
	}

	protected function _render()
	{
		return $this->_fetchView('widget');
	}

	protected function _renderFromCache()
	{
		$output = '';

		try {
			ob_start();

			readfile($this->_getCachePath());

			$output = ob_get_contents();

			ob_end_clean();
		}
		catch(Exception $error)
		{
			$output = $this->_render();
		}

		return $contents;
	}

	protected function _validateCache()
	{
		$path  		= $this->_getCachePath();
		$timestamp 	= 0;
		$now 		= time();

		if($this->_cacheData['lifetime'] < 1)
		{
			return false;
		}

		if(file_exists($path))
		{
			$timestamp 	= filemtime($path);
		}

		if($now >= ($timestamp + $cacheLifetime) || !$timestamp)
		{
			if($exists)
			{
				unlink($path);
			}

			return false;
		}

		return true;
	}

// @todo implement
	public function __($text = '', $domain = '')
	{
		return $text;
	}
// @todo implement
	public function _e($text = '', $domain = '')
	{
		echo $text;
	}
// @todo implement
	public function _n($singular = '', $plural ='', $number = null, $domain ='')
	{
		return $singular;
	}

	public function form()
	{
		return $this->getFormHtml();
	}

	public function getCacheData()
	{
		return $this->_cacheData;
	}

	public function getFormHtml()
	{
		return $this->_fetchView('form');
	}

	public function getWidgetHtml()
	{
		return $this->render();
	}

	public function render()
	{
		if($this->_validateCache())
		{
			return $this->_renderFromCache();
		}
		
		$this->_beforeRender();
		$output = $this->_render();
		$output = $this->_afterRender($output);

		$this->_cacheOutput($output);

		return $output;
	}

	public function setCacheData(array $data = array())
	{
		$this->_cacheData = array_merge($this->_cacheData, $data);

		return $this;
	}

	public function setTemplate($area = 'widget', $template)
	{
		$this->_templates[$area] = $template;

		return $this;
	}

	public function widget()
	{
		return $this->getWidgetHtml();
	}

}