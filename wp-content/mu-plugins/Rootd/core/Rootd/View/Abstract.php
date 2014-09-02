<?php

/**
 * Base view abstract class.
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_View_Abstract 
    extends Rootd_Object
{

    protected $_children = array();

    /**
     * Call internal constructor.
     * 
     * @param array $attributes Object attributes.
     *
     * @return void
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        $this->_construct();
    }

    /**
     * Internal constructor.
     * 
     * @return Rootd_View_Abstract
     */
    public function _construct() {
        return $this;
    }

    /**
     * Add a child view.
     * 
     * @param Rootd_View_Abstract $view A view object.
     */
    protected function _addChild(Rootd_View_Abstract $view)
    {
        $this->_children[$view->getViewId()] = $view;

        return $this;
    }

    /**
     * Post-render method.
     * 
     * @param string $content The rendered output.
     * 
     * @return string
     */
    protected function _afterRender($content = '') 
    {
        return $content;
    }

    /**
     * Pre-render method.
     * 
     * @return string
     */
    protected function _beforeRender() 
    {
        return '';
    }

    /**
     * Get a child view.
     * 
     * @param string $id The child view ID.
     * 
     * @return Rootd_View_Abstract
     */
    protected function _getChild($id = null)
    {
        if (isset($this->_children[$id])) {
            return $this->_children[$id];
        }

        return false;
    }

    /**
     * Render the view.
     * 
     * @param string $content The view output.
     * 
     * @return string
     */
    protected function _render($content = '')
    {
        return $content;
    }

    // @todo implement
    public function __($input = '')
    {
        return $input;
    }

    /**
     * Add a child view.
     * 
     * @param Rootd_View_Abstract $view A view object.
     *
     * @return Rootd_View_Abstract
     */
    public function addChild(Rootd_View_Abstract $view)
    {
        if (!$this->getChild($view->getViewId())) {
            $this->_addChild($view);
            $view->setParentView($this);
        } else {
            throw new Exception("View {$view->getViewId()} already exists in {$this->getViewId()}");
        }

        return $this;
    }

    /**
     * Get a child view.
     * 
     * @param string $id The child view ID.
     * 
     * @return Rootd_View_Abstract
     */
    public function getChild($id = null)
    {
        return $this->_getChild($id);
    }

    /**
     * HTML-escape input.
     * 
     * @param string $input The input string.
     * 
     * @return string
     */
    public function htmlEscape($input = '')
    {
        return htmlentities($input, ENT_COMPAT);
    }

    /**
     * Render the view content.
     * 
     * @return string
     */
    public function render()
    {
        $content = $this->_beforeRender();
        $content = $this->_render($content);
        $this->_afterRender($content);

        return $content;
    }

    /**
     * Remove a child view.
     * 
     * @param string $id The child view ID.
     * 
     * @return Rootd_View_Abstract
     */
    public function removeChild($id)
    {
        if ( isset($this->_children[$id]) ) {
            unset($this->_children[$id]);
        }

        return $this;
    }

    /**
     * Render a child view.
     * 
     * @param string $id The child view ID.
     * 
     * @return string
     */
    public function renderChild($id = null)
    {
        $view = $this->getChild($id);

        if ($view) {
            return $view->render();
        }

        return '';
    }

    /**
     * Set a child view, replacing an existing.
     * 
     * @param Rootd_View_Abstract $view The child view object.
     *
     * @return Rootd_View_Abstract
     */
    public function setChild(Rootd_View_Abstract $view)
    {
        $this->removeChild($view->getViewId());

        $this->addChild($view);

        return $this;
    }

}