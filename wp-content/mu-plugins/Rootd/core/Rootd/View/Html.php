<?php

/**
 * HTML view class.
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_View_Html
    extends Rootd_View_Abstract
{

    protected $_content = '';

    /**
     * Render the content string.
     * 
     * @param string $content The rendered output.
     * 
     * @return string
     */
    protected function _render($content = '')
    {
        return $content . $this->_content;
    }

    /**
     * Get the current content.
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Set the view content.
     * 
     * @param string $content The content to render.
     *
     * @return Rootd_View_Html
     */
    public function setContent($content = '')
    {
        $this->_content = $content;

        return $this;
    }

}