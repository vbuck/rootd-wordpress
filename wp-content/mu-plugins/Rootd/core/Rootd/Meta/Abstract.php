<?php

/**
 * Meta box abstract class.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

abstract class Rootd_Meta_Abstract
{

	const CONTEXT_ADVANCED 	= 'advanced';
	const CONTEXT_NORMAL 	= 'normal';
	const CONTEXT_SIDE 		= 'side';
	const PRIORITY_CORE 	= 'core';
	const PRIORITY_DEFAULT 	= 'default';
	const PRIORITY_HIGH		= 'high';
	const PRIORITY_LOW 		= 'low';

	protected $_areas 		= array();
	/* @var $_context string */
	protected $_context;
	/* @var $_post Rootd_Object */
	protected $_post;
	/* @var $_priority string */
	protected $_priority;
	protected $_renderArea 	= 'plugin';
	/* @var $_template string */
	protected $_template;
	/* @var $_title string */
	protected $_title;

	/**
	 * Calls extending initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->_context 	= self::CONTEXT_NORMAL;
		$this->_priority 	= self::PRIORITY_DEFAULT;

		$this->_construct();
	}

	/**
	 * Initializer.
	 * 
	 * @return Rootd_Meta_Abstract
	 */
	public function _construct()
	{
		return $this;
	}

	/**
	 * Post-render actions.
	 * 
	 * @param  string $output The rendered content.
	 * @return string
	 */
	protected function _afterRender($output = '')
	{
		return $output;
	}

	/**
	 * Post-save actions.
	 * 
	 * @param  WP_Post $post
	 * @return Rootd_Meta_Abstract
	 */
	protected function _afterSave(Rootd_Object $post)
	{
		return $this;
	}

	/**
	 * Pre-render actions.
	 * 
	 * @return Rootd_Meta_Abstract
	 */
	protected function _beforeRender()
	{
		return $this;
	}

	/**
	 * Pre-save actions.
	 * 
	 * @param  WP_Post $post
	 * @return Rootd_Meta_Abstract
	 */
	protected function _beforeSave(Rootd_Object $post)
	{
		// Verify nonce field
		if(!$this->_verifyNonce())
		{
			return false;
		}

		// User must have edit permissions
		if(
			!current_user_can(
				get_post_type_object($post->getPostType())->cap->edit_post, 
				$post->getId()
			)
		)
		{
			return false;
		}

		return $this;
	}

	/**
	 * Fetch the view from the template.
	 * 
	 * @return string
	 */
	protected function _fetchView()
	{
		$view = '';

		try {
			ob_start();

			include $this->_getTemplatePath();
			
			$view = ob_get_contents();

			ob_end_clean();
		}
		catch(Exception $error)
		{
			return $this->__('Failed to fetch view in ' . get_class($this));
		}

		return $view;
	}

	/**
	 * Get the path to the template.
	 *
	 * Returned path is relative to the module.
	 * 
	 * @return string
	 */
	protected function _getTemplatePath()
	{
		$moduleName = array_shift((explode('_', get_class($this))));
		$path 		= Rootd::getBasePath($this->_renderArea) . $moduleName . DIRECTORY_SEPARATOR . $this->_template;

		return $path;
	}

	/**
	 * Prepares the post post object for use.
	 *
	 * Auto-loads all meta data.
	 * 
	 * @param  WP_Post $object
	 * @return Rootd_Meta_Abstract
	 */
	protected function _preparePost(WP_Post $object)
	{
		$data 			= (array) $object;
		$metaData 		= array();

		foreach(get_post_meta($data['ID']) as $key => $values)
		{
			$metaData[$key] = implode(',', $values);
		}

		$data 			= array_merge($data, $metaData);
		$this->_post 	= new Rootd_Object($data);

		return $this;
	}

	/**
	 * Backend renderer.
	 * 
	 * @return string
	 */
	protected function _render()
	{
		return $this->_fetchView();
	}

	/**
	 * Backend save handler.
	 * 
	 * @param  WP_Post $post
	 * @return Rootd_Meta_Abstract
	 */
	protected function _save(Rootd_Object $post)
	{
		return $this;
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

	/**
	 * Verify the nonce field.
	 * 
	 * @return boolean
	 */
	public function _verifyNonce()
	{
		if(
			!isset($_POST[$this->getNonceFieldName()]) ||
			wp_verify_nonce($_POST[$this->getNonceFieldName()])
		)
		{
			return false;
		}

		return true;
	}

	/**
	 * Add the meta box to the given areas.
	 *
	 * @return Rootd_Meta_Abstract
	 */
	public function addMeta()
	{
		foreach($this->_areas as $type)
		{
			add_meta_box(
				get_class($this),
				$this->getTitle(),
				array($this, 'render'),
				$type,
				$this->getContext(),
				$this->getPriority()
			);
		}

		return $this;
	}

	/**
	 * Escape an HTML attribute.
	 * 
	 * @param  	string $value
	 * @return 	string
	 */
	public function escapeAttribute($value = '')
	{
		return str_replace('"', '\\"', $value);
	}

	/**
	 * Get the meta box areas.
	 * 
	 * @return array
	 */
	public function getArea()
	{
		return $this->_areas;
	}

	/**
	 * Get the meta box context.
	 * 
	 * @return string
	 */
	public function getContext()
	{
		return $this->_context;
	}

	/**
	 * Generate a HTML attributes string
	 * from an associative array.
	 * 
	 * @param  	array  $attributes
	 * @return 	string
	 */
	public function getHtmlAttributes($attributes = array())
	{
		$groups = array();

		foreach($attributes as $key => $value)
		{
			$groups[] = $key . '="' . $this->escapeAttribute($value) . '"';
		}

		return implode(' ', $groups);
	}

	/**
	 * Get the nonce field name.
	 * 
	 * @return string
	 */
	public function getNonceFieldName()
	{
		return strtolower(get_class($this));
	}

	/**
	 * Get the nonce field HTML.
	 * 
	 * @return string
	 */
	public function getNonceHtml()
	{
		return wp_nonce_field(basename(__FILE__), $this->getNonceFieldName());
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
	 * Get the meta box priority.
	 * 
	 * @return string
	 */
	public function getPriority()
	{
		return $this->_priority;
	}

	/**
	 * Get the request object.
	 * 
	 * @return Rootd_Request
	 */
	public function getRequest()
	{
		return Rootd::getRequest();
	}

	/**
	 * Get the template.
	 * 
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->_template;
	}

	/**
	 * Get the meta box title.
	 * 
	 * @return string
	 */
	public function getTitle()
	{
		return $this->_title;
	}

	/**
	 * Initialize the meta box in WordPress.
	 * 
	 * @return Rootd_Meta_Abstract
	 */
	public function initialize()
	{
		add_action('load-post.php', array($this, 'setupMeta'));
		add_action('load-post-new.php', array($this, 'setupMeta'));

		return $this;
	}

	/**
	 * Register the meta box for use.
	 * 
	 * @return Rootd_Meta_Abstract
	 */
	public static function register()
	{
		$class 		= get_called_class();
		$instance 	= new $class();

		add_action('admin_init', array($instance, 'initialize'));

		return $instance;
	}

	/**
	 * Frontend meta box renderer.
	 * 
	 * @param  	WP_Post $object
	 * @param  	array $instance
	 * @return 	void
	 */
	public function render(WP_Post $object, array $instance)
	{
		$this->_preparePost($object);

		$this->_beforeRender();
		$output = $this->_render();
		$output = $this->_afterRender($output);

		echo $output;
	}

	/**
	 * Frontend meta box save handler.
	 * 
	 * @param  	integer $id
	 * @param  	WP_Post $post
	 * @return 	Rootd_Meta_Abstract
	 */
	public function save($id, WP_Post $post)
	{
		$this->_preparePost($post);

		if(!$this->_beforeSave($this->_post))
		{
			return false;
		}

		$this->_save($this->_post);
		$this->_afterSave($this->_post);

		return $this;
	}

	/**
	 * Set the meta-box location(s).
	 *
	 * @param 	mixed $area
	 * @return  Rootd_Meta_Abstract
	 */
	public function setArea($area = array())
	{
		if(!is_array($area))
		{
			$area = array($area);
		}

		$this->_areas = $area;

		return $this;
	}

	/**
	 * Set the meta box context.
	 * 
	 * @param 	string $context
	 * @return  Rootd_Meta_Abstract
	 */
	public function setContext($context = self::CONTEXT_NORMAL)
	{
		$this->_context = $context;

		return $this;
	}

	/**
	 * Set the meta box priority.
	 * 
	 * @param 	string $priority
	 * @return  Rootd_Meta_Abstract
	 */
	public function setPriority($priority = self::PRIORITY_DEFAULT)
	{
		$this->_priority = $priority;

		return $this;
	}

	/**
	 * Set the meta box template.
	 * 
	 * @param 	string $template
	 * @return  Rootd_Meta_Abstract
	 */
	public function setTemplate($template = '')
	{
		$this->_template = $template;

		return $this;
	}

	/**
	 * Set the meta box title.
	 * 
	 * @param 	string $title
	 * @return  Rootd_Meta_Abstract
	 */
	public function setTitle($title = '')
	{
		$this->_title = $title;

		return $this;
	}

	/**
	 * Register the meta box setup processes.
	 * 
	 * @return  Rootd_Meta_Abstract
	 */
	public function setupMeta()
	{
		add_action('add_meta_boxes', array($this, 'addMeta'));
		add_action('save_post', array($this, 'save'), null, 2);

		return $this;
	}

}