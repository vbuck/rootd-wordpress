<?php

class Rootd_Object
{
    
    private $_data 					= array();
    public static $_itemIdPrefix 	= 'item_';
    
    /**
     * Prepare defaults and constructor values.
     * 
     * @return void
     */
    public function __construct() {
        $args = func_get_args();
        
        if(empty($args[0]))
        {
        	$args[0] = array();
        }
        
        $newId = self::$_itemIdPrefix . substr(md5(time()), -6);
        
        $this->_data = array('id' => $newId);

        $this->addData($args[0]);
    }
    
    public function __call($method, $args) 
    {
        switch(substr($method, 0, 3)) 
        {
            case 'get':
            $key 	= $this->_underscore(substr($method, 3));
            $data 	= $this->getData($key, isset($args[0]) ? $args[0] : null);

            return $data;
            
            case 'set':
            $key 	= $this->_underscore(substr($method, 3));
            $result = $this->setData($key, isset($args[0]) ? $args[0] : null);

            return $result;
        }
    }
    
    public function __get($name) 
    {
        $key = $this->_underscore($name);

        return $this->getData($key);
    }
    
    public function __set($name, $value) 
    {
        $key = $this->_underscore($name);

        $this->setData($key, $value);
        
        return $this;
    }
    
    public function _underscore($name) 
    {
        return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
    }

    public function addData(array $data)
    {
    	foreach($data as $key => $value)
    	{
    		$this->setData($key, $value);
    	}

    	return $this;
    }
    
    public function getData($key = null) 
    {
        if(is_null($key))
        {
        	return $this->_data;
        }
        
        if(isset($this->_data[$key]))
        {
        	return $this->_data[$key];
        }
        
        return null;
    }
    
    public function setData($key, $value) 
    {
        $this->_data[$key] = $value;
        
        return $this;
    }
    
}