<?php

/**
 * Post Notes meta box.
 *
 * A demo meta module for the Rootd Framework which demonstrates
 * the meta box development workflow in a practical example that
 * lets you add custom notes to your post.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

// All meta boxes must extend Rootd_Meta
class Rootd_Postnote_Meta extends Rootd_Meta
{

	/**
	 * The local constructor is where you will configure
	 * the meta box, setting properties like the usable
	 * area(s), title, display priority, and the actual
	 * template to display the meta box content.
	 * 
	 * @return void
	 */
	public function _construct()
	{
		$this
			->setArea('post')						// Can also accept an array of multiple areas
			->setTitle($this->__('Post Notes'))		// Appears at the top of the meta box
			->setPriority()							// Can also accept a priority value, else the default is applied
			->setTemplate('Postnote/Meta.phtml')	// Path to the template is relative to your module (eg: 'Rootd/Postnote/Meta.phtml')
			;

		// Special flag for core module and features, you do not need to add this
		$this->_renderArea = 'core';
	}

	/**
	 * The backend save handler is where you will process
	 * the request data as usual for WordPress. In this
	 * example, we are only dealing with a single value,
	 * which is added, updated, or removed from WordPress.
	 * 
	 * @param  	Rootd_Object $post
	 * @return 	Rootd_Meta
	 */
	protected function _save(Rootd_Object $post)
	{
		$key 	 = 'postnote_content';
		$content = $this->getRequest()->getParam($key);

		if(!is_null($content) && $post->getPostnoteContent() === '')
		{
			add_post_meta($post->getId(), $key, $content, true);
		}
		else if($content && $content !== $post->getPostnoteContent())
		{
			update_post_meta($post->getId(), $key, $content);
		}
		else if($content === '' && $post->getPostnoteContent())
		{
			delete_post_meta($post->getId(), $key, $post->getPostnoteContent());
		}

		return $this;
	}

	/**
	 * There are additional methods which you can extend
	 * here to further customize your meta box, including:
	 *
	 * 	_beforeRender
	 * 	_afterRender
	 * 	_beforeSave
	 * 	_afterSave
	 * 	_render
	 * 	getNonceFieldName
	 * 
	 * @see  Rootd_Meta_Abstract
	 */

}