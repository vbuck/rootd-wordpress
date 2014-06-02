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

	protected $_params = array();

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
	 * Get a request parameter.
	 * 
	 * @param  	string $key
	 * @param  	mixed $default
	 * @return 	mixed
	 */
	public function getParam($key, $default = null)
    {
        if(isset($this->_params[$key])) 
        {
            return $this->_params[$key];
        } 
        else if(isset($_GET[$key])) 
        {
            return $_GET[$key];
        } 
        else if(isset($_POST[$key])) 
        {
            return $_POST[$key];
        }

        return $default;
    }

    /**
     * Get all request parameters.
     * 
     * @return array
     */
    public function getParams()
    {
        $data = $this->_params;

        if(isset($_GET) && is_array($_GET)) 
        {
            $data += $_GET;
        }

        if(isset($_POST) && is_array($_POST)) 
        {
            $data += $_POST;
        }

        return $data;
    }

}