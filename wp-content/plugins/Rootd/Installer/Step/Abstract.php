<?php

/**
 * Rootd Framework installer step abstract class.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

abstract class Rootd_Installer_Step_Abstract
{

	protected $_errors = array();
	protected $_stepId;
	protected $_template;

	/**
	 * Trigger the extending class constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->_construct();
	}

	/**
	 * Extending constructor.
	 * 
	 * @return Rootd_Installer_Step_Abstract
	 */
	public function _construct()
	{
		return $this;
	}

	/**
	 * Get the installer code base path.
	 * 
	 * @return string
	 */
	protected function _getInstallerPath()
	{
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
	}

	/**
	 * Get the step template path.
	 * 
	 * @return mixed
	 */
	protected function _getTemplatePath()
	{
		$path = dirname(__FILE__) . DIRECTORY_SEPARATOR .
			'..' . DIRECTORY_SEPARATOR .
			$this->getTemplate();

		if(is_file($path))
		{
			return $path;
		}

		return null;
	}

	/**
	 * Add an error to the step.
	 * 
	 * @param 	string $message
	 * @return  Rootd_Installer_Step_Abstract
	 */
	public function addError($message = 'Unspecified error')
	{
		$this->_errors[] = __($message);

		return $this;
	}

	/**
	 * Get the form action URL for the step.
	 * 
	 * @return string
	 */
	public function getAction()
	{
		return admin_url('plugins.php') . "?rootd_install={$this->getId()}&rootd_action=complete";
	}

	/**
	 * Get the errors collection.
	 * 
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * Get the step ID.
	 * 
	 * @return string
	 */
	public function getId()
	{
		return $this->_stepId;
	}

	/**
	 * Get the installer instance.
	 * 
	 * @return Rootd_Installer
	 */
	public function getInstaller()
	{
		return Rootd_Installer::getInstance();
	}

	/**
	 * Get the installation progress as a ratio.
	 * 
	 * @return string
	 */
	public function getProgress()
	{
		$instance = Rootd_Installer::getInstance();

		if($instance)
		{
			return $instance->getCurrentStepIndex() . ' / ' . $instance->getTotalSteps();
		}

		return '';
	}

	/**
	 * Get the installation script content.
	 * 
	 * @return string
	 */
	public function getScripts()
	{
		$path 	= $this->_getInstallerPath() . 'scripts.js';
		$js 	= '';

		if(file_exists($path))
		{
			$js = file_get_contents($path);
		}

		return
			'<script type="text/javascript">' . "\n" .
				$js . "\n" .
			'</script>';
	}

	/**
	 * Get the installation stylesheet content.
	 * 
	 * @return string
	 */
	public function getStylesheet()
	{
		$path 	= $this->_getInstallerPath() . 'styles.css';
		$css 	= '';

		if(file_exists($path))
		{
			$css = file_get_contents($path);
		}

		return 
			'<style type="text/css">' . "\n" .
				$css . "\n" .
			'</style>';
	}

	/**
	 * Get the step template.
	 * 
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->_template;
	}

	/**
	 * Determine if there are errors at this step.
	 * 
	 * @return boolean
	 */
	public function hasErrors()
	{
		return (count($this->_errors) > 0);
	}

	/**
	 * Step processing implementation.
	 * 
	 * @return boolean
	 */
	public function install()
	{
		return true;
	}

	/**
	 * Render the step content.
	 * 
	 * @return string
	 */
	public function render()
	{
		try {
			$path = $this->_getTemplatePath();

			ob_start();

			include $path;

			$content = 
				$this->getStylesheet() . "\n" .
				'<div id="' . $this->_stepId . '" class="rootd-installer step">' . "\n" .
					ob_get_contents() . "\n" .
				'</div>' . "\n" .
				$this->getScripts();

			ob_end_clean();
		}
		catch(Exception $error)
		{
			throw new Exception('Failed to render installer step.');
		}

		return $content;
	}

	/**
	 * Set the step ID.
	 * 
	 * @param 	string $id
	 * @return  Rootd_Installer_Step_Abstract
	 */
	public function setId($id = '')
	{
		$this->_stepId = $id;

		return $this;
	}

	/**
	 * Set the step template.
	 * 
	 * @param 	string $template
	 * @return  Rootd_Installer_Step_Abstract
	 */
	public function setTemplate($template = '')
	{
		$this->_template = $template;

		return $this;
	}

}