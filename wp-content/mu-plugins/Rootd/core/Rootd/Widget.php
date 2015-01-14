<?php

/**
 * Widget abstract class.
 *
 * PHP Version 5
 * 
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Widget extends WP_Widget
{

	/* @var $_post Rootd_Object */
	protected $_post;
	protected $_widgetData 	= array();
	protected $_renderArea 	= 'plugin';
	protected $_templates 	= array(
		'form' 		=> null,
		'widget' 	=> null
	);

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->_widgetData = new Rootd_Object();
		
		$this->_construct();
	}

	/**
	 * Internal constructor.
	 * 
	 * @return Rootd_Widget
	 */
	public function _construct()
	{
		return $this;
	}

	/**
	 * Post-render output handler.
	 * 
	 * @param string output The rendered output.
	 * 
	 * @return string
	 */
	protected function _afterRender($output = '')
	{
		return $output;
	}

	/**
	 * Post-render form output handler.
	 * 
	 * @param string output The rendered output.
	 * 
	 * @return string
	 */
	protected function _afterRenderForm($output = '')
	{
		return $output;
	}

	/**
	 * Post-save actions.
	 * 
	 * @return Rootd_Widget
	 */
	protected function _afterSave()
	{
		return $this;
	}

	/**
	 * Pre-render output handler.
	 * 
	 * @return Rootd_Widget
	 */
	protected function _beforeRender()
	{
		return $this;
	}

	/**
	 * Pre-render form output handler.
	 * 
	 * @return Rootd_Widget
	 */
	protected function _beforeRenderForm()
	{
		return $this;
	}

	/**
	 * Pre-save actions.
	 * 
	 * @return Rootd_Widget
	 */
	protected function _beforeSave()
	{
		return $this;
	}

	/**
	 * Load the specified template.
	 * 
	 * @param string $area The widget area to render (form|widget).
	 * 
	 * @return string
	 */
	protected function _fetchView($area = 'widget')
	{
		$view = '';

		try {
			ob_start();

			include $this->_getTemplatePath($area);
			
			$view = ob_get_contents();

			ob_end_clean();
		} catch(Exception $error) {
			return $this->__('Failed to fetch view in ' . get_class($this));
		}

		return $view;
	}

	/**
	 * Get the path to the template by area.
	 * 
	 * @param string $area The widget area (form|widget)
	 * 
	 * @return string|null
	 */
	protected function _getTemplatePath($area)
	{
		$path = null;

		if (isset($this->_templates[$area])) {
			$moduleName = array_shift((explode('_', get_class($this))));
			$path 		= Rootd::getBasePath($this->_renderArea) . $moduleName . DIRECTORY_SEPARATOR . $this->_templates[$area];
		}

		return $path;
	}

	/**
	 * Initialize the widget instance, to be called by
	 * the implementing internal constructor.
	 * 
	 * @param boolean $idBase         The ID base for the widget instance.
	 * @param string  $name           A display name for the widget in the backend.
	 * @param array   $widgetOptions  The widget instance configuration.
	 * @param array   $controlOptions The widget instance control options.
	 * 
	 * @return Rootd_Widget
	 */
	protected function _init(
		$idBase = false, 
		$name, 
		array $widgetOptions = array(), 
		array $controlOptions = array()
	) {
		parent::__construct($idBase, $name, $widgetOptions, $controlOptions);

		return $this;
	}

	/**
     * Prepares the post post object for use.
     *
     * Auto-loads all meta data.
     * 
     * @return Rootd_Widget
     */
    protected function _preparePost()
    {
        $this->_post = Rootd::helper('post')->getPost();

        return $this;
    }

    /**
     * Render the widget view.
     * 
     * @return string
     */
	protected function _render()
	{
		return $this->_fetchView('widget');
	}

	/**
     * Render the form view.
     * 
     * @return string
     */
	protected function _renderForm()
	{
		return $this->_fetchView('form');
	}

	/**
	 * Backend save handler.
	 * 
	 * @return Rootd_Widget
	 */
	protected function _save()
	{
		return $this;
	}

	/**
	 * Translator.
	 * 
	 * @param string $text   Input.
	 * @param string $domain Text-domain.
	 * 
	 * @return string
	 */
	public function __($text = '', $domain = '')
	{
		return __($text, $domain);
	}

	/**
	 * Translator with output.
	 * 
	 * @param string $text   Input.
	 * @param string $domain Text-domain.
	 * 
	 * @return void
	 */
	public function _e($text = '', $domain = '')
	{
		_e($text, $domain);
	}

	/**
	 * Translator for singular/plural.
	 * 
	 * @param string $text   Input.
	 * @param string $domain Text-domain.
	 * 
	 * @return string
	 */
	public function _n($singular = '', $plural ='', $number = null, $domain ='')
	{
		return _n($singular, $plural, $number, $domain);
	}

	/**
	 * Admin form implementation from WP_Widget.
	 * 
	 * @return string
	 */
	public function form($instance = array(), $echo = true)
	{
		$this->_widgetData = new Rootd_Object($instance);

		$content = $this->getFormHtml();

		if ($echo) {
			echo $content;
		} else {
			return $content;
		}
	}

	/**
	 * Get the backend form view.
	 * 
	 * @return string
	 */
	public function getFormHtml()
	{
		$this->_beforeRenderForm();

		$output = $this->_renderForm();
		$output = $this->_afterRenderForm($output);

		return $output;
	}

	/**
	 * Get the post object.
	 * 
	 * @return Rootd_Object
	 */
	public function getPost()
	{
		return $this->_post;
	}

	/**
	 * Get the combined widget instance data.
	 * 
	 * @return Rootd_Object
	 */
	public function getWidgetData()
	{
		return $this->_widgetData;
	}

	/**
	 * Get the frontend widget view.
	 * 
	 * @return string
	 */
	public function getWidgetHtml()
	{
		return $this->render();
	}

	/**
	 * Register the widget instance.
	 * 
	 * @return void
	 */
	public static function initialize()
	{
		register_widget(get_called_class());
	}

	/**
	 * Prepare the widget for registration with WordPress.
	 * 
	 * @return void
	 */
	public static function register()
	{
		$class = get_called_class();

		add_action('widgets_init', array($class, 'initialize'));
	}

	/**
	 * Render the widget instance.
	 * 
	 * @return void
	 */
	public function render()
	{
		$this->_preparePost();
		$this->_beforeRender();

		$output = $this->_render();
		$output = $this->_afterRender($output);

		echo $output;
	}

	/**
	 * Set the widget template for the given area.
	 * 
	 * @param string $area     The widget area (form|widget).
	 * @param string $template The template path relative to the module.
	 *
	 * @return Rootd_Widget
	 */
	public function setTemplate($area = 'widget', $template)
	{
		$this->_templates[$area] = $template;

		return $this;
	}

	/**
	 * Public renderer implementation from WP_Widget.
	 * 
	 * @return string
	 */
	public function widget($args, $instance)
	{
		$this->_widgetData = new Rootd_Object(array_merge($args, $instance));

		return $this->getWidgetHtml();
	}

	public function update($newInstance, $oldInstance)
	{
		$this->_widgetData = new Rootd_Object($newInstance);

		if (!$this->_beforeSave()) {
			return false;
		}

		$this->_save();
		$this->_afterSave();
		
		return $this->_widgetData->getData();
	}

}