<?php

/**
 * Rootd Framework request class.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Request
{

    protected $_httpReferer = '';
	protected $_params      = array();

	public function __construct()
	{
		$this->_prepareRequest();
	}

	/**
	 * Prepare the request data.
	 * 
	 * @return Rootd_Request
	 */
	protected function _prepareRequest()
	{
		return $this;
	}

    /**
     * Get the referring URL.
     * 
     * @return string
     */
    public function getHttpReferer()
    {
        if ($this->_httpReferer) {
            return $this->_httpReferer;
        }

        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
            return $_SERVER['HTTP_REFERER'];
        }

        // Not found, return current URL
        if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '') {
            return $_SERVER['REQUEST_URI'];
        }

        if (isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL'] != '') {
            return $_SERVER['REDIRECT_URL'];
        }

        return site_url();
    }

	/**
	 * Get a request parameter.
	 * 
	 * @param  	string $key
	 * @param  	mixed $default
	 * @return 	mixed
	 */
	public function getParam($key, $default = null)
    {
        global $wp_query;

        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        } 
        else if (isset($_GET[$key])) {
            return urldecode($_GET[$key]);
        } 
        else if (isset($_POST[$key])) {
            return $_POST[$key];
        } else if (isset($wp_query->query_vars[$key])) {
            return $wp_query->query_vars[$key];
        }

        return $default;
    }

    /**
     * Get all request parameters.
     * 
     * @return array
     */
    public function getParams($includeWpQuery = false) {
        global $wp_query;

        $data = $this->_params;

        if (isset($_GET) && is_array($_GET)) {
            $data += $_GET;
        }

        if (isset($_POST) && is_array($_POST)) {
            $data += $_POST;
        }

        if ($includeWpQuery && isset($wp_query) && is_array($wp_query->query_vars)) {
            $data += $wp_query->query_vars;
        }

        return $data;
    }

    /**
     * Set the HTTP referring URL.
     * 
     * @param string $url The URL.
     */
    public function setHttpReferer($url) {
        $this->_httpReferer = $url;

        return $this;
    }

}