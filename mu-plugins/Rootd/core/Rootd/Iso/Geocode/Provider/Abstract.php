<?php

/**
 * Geocoding service provider abstract class.
 *
 * PHP Version 5
 *
 * @package   Rootd_Iso
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

abstract class Rootd_Iso_Geocode_Provider_Abstract
{

    /* @var $_connection mixed */
    protected $_connection;
    /* @var $_descriptor Rootd_Object */
    protected $_descriptor;
    /* @var $_request Rootd_Object */
    protected $_request;
    protected $_rules = array();

    /**
     * Prepare connection.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_construct();
        $this->_prepareConnection();
    }

    /**
     * Internal constructor.
     * 
     * @return mixed
     */
    public function _construct()
    {
        return $this;
    }

    /**
     * Provider fetch implementation.
     * 
     * @param string $country  The input country.
     * @param string $address  The input address.
     * @param string $region   The input region.
     * @param string $city     The input city.
     * @param string $postcode The input postal code.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response
     */
    abstract protected function _fetch(
        $country = null, 
        $address = null, 
        $region = null, 
        $city = null, 
        $postcode = null
    );

    /**
     * Fetch a result from cache.
     * 
     * @param array  $data The request data.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response|null
     */
    protected function _fetchCachedResult(array $data)
    {
        return Rootd_Iso_Geocode_Cache::get($this->_getCacheKey($data));
    }

    /**
     * Generate a hash of the request data as the cache key.
     * 
     * @param array $data The request data.
     * 
     * @return string|null
     */
    protected function _getCacheKey($data)
    {
        $keyParts = array();

        foreach ($data as $key => $value) {
            if (!is_null($value)) {
                $keyParts[] = $value;
            }
        }
        
        if(!empty($keyParts)) {
            return md5(implode('|', $keyParts));
        }

        return null;
    }

    /**
     * Provider response parse implementation.
     * 
     * @param mixed $response The provider-specific response object.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response
     */
    abstract protected function _parseResponse($response);

    /**
     * Provider connection initialization implementation.
     * 
     * @return void
     */
    abstract protected function _prepareConnection();

    /**
     * Write the results to cache.
     * 
     * @param array                               $request The request data.
     * @param Rootd_Iso_Geocode_Provider_Response $data    The response data.
     * 
     * @return void
     */
    protected function _writeCachedResult(array $request, Rootd_Iso_Geocode_Provider_Response $data)
    {
        Rootd_Iso_Geocode_Cache::set($this->_getCacheKey($request), $data);
    }

    /**
     * Get geocode data from the provider service.
     * 
     * @param string $country  The input country.
     * @param string $address  The input address.
     * @param string $region   The input region.
     * @param string $city     The input city.
     * @param string $postcode The input postal code.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response
     */
    public function fetch(
        $country = null, 
        $address = null, 
        $region = null, 
        $city = null, 
        $postcode = null
    )
    {
        $request = func_get_args();

        $this->setRequest($request);

        if ($this->getDescriptor()->getUseCache()) {
            if ( ($results = $this->_fetchCachedResult($this->getRequest()->getData())) ) {
                return $results;
            }
        }

        $results = $this->_fetch($country, $address, $region, $city, $postcode);

        if ($this->getDescriptor()->getUseCache()) {
            $this->_writeCachedResult($this->getRequest()->getData(), $results);
        }

        return $results;
    }

    /**
     * Response component format helper.
     * 
     * @param string $type  The component type (country|address|region|city|postcode).
     * @param string $value The component value.
     * 
     * @return string
     */
    public function format($type, $value = '')
    {
        // Fetch configured rules from config
        if (!isset($this->_rules[$type])) {
            $this->_rules[$type] = explode(';', (string) Rootd::getConfig()->getNode("modules/rootd/iso/geocode/format_rules/{$type}"));
        }

        // Apply each rule to component value
        foreach ($this->_rules[$type] as $callable) {
            // Rule must be a callable internal method or global function
            if (method_exists($this, $callable)) {
                $callable = array($this, $callable);
            }

            if (is_callable($callable)) {
                $value = call_user_func($callable, $value);
            }
        }

        return $value;
    }

    /**
     * Get the underlying connection object.
     * 
     * @return mixed
     */
    final public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Get the provider descriptor.
     * 
     * @return Rootd_Object
     */
    final public function getDescriptor()
    {
        return $this->_descriptor;
    }

    /**
     * Internal formatter, get country code.
     * 
     * @param string $name The full country name.
     * 
     * @return string
     */
    public function getCountryCode($name = '')
    {
        if ( ($country = $this->getRequest()->getCountry()) ) {
            $name = Rootd_Iso_Country::getCountryCode($name);
        }

        return $name;
    }

    /**
     * Internal formatter, get country by code.
     * 
     * @param string $name The 2-letter country code.
     * 
     * @return string
     */
    public function getCountryByCode($code = '')
    {
        if ( ($country = $this->getRequest()->getCountry()) ) {
            $code = Rootd_Iso_Country::getCountryByCode($code);
        }

        return $code;
    }

    /**
     * Internal formatter, get region code.
     * 
     * @param string $name The full region name.
     * 
     * @return string
     */
    public function getRegionCode($name = '')
    {
        if ( ($country = $this->getRequest()->getCountry()) ) {
            $class = 'Rootd_Iso_Country_' . ucfirst( (strtolower(Rootd_Iso_Country::getCountryCode($country))) );

            Rootd_Loader::setIgnoreWarnings(true);

            if (class_exists($class)) {
                $name = $class::getRegionByCode($name);
            }

            Rootd_Loader::setIgnoreWarnings(false);
        }

        return $name;
    }

    /**
     * Internal formatter, get region by code.
     * 
     * @param string $name The region code.
     * 
     * @return string
     */
    public function getRegionByCode($code = '')
    {
        if ( ($country = $this->getRequest()->getCountry()) ) {
            $class = 'Rootd_Iso_Country_' . ucfirst( (strtolower(Rootd_Iso_Country::getCountryCode($country))) );

            Rootd_Loader::setIgnoreWarnings(true);

            if (class_exists($class)) {
                $code = $class::getRegionByCode($code);
            }

            Rootd_Loader::setIgnoreWarnings(false);
        }

        return $code;
    }

    /**
     * Get the request object.
     * 
     * @return Rootd_Object
     */
    final public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get a new response object.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response
     */
    final public function getResponseObject()
    {
        return new Rootd_Iso_Geocode_Provider_Response();
    }

    /**
     * Set the descriptor.
     * 
     * @param Rootd_Object $descriptor The descriptor object.
     */
    final public function setDescriptor(Rootd_Object $descriptor)
    {
        $this->_descriptor = $descriptor;

        return $this;
    }

    /**
     * Set the current request object.
     * 
     * @param array $data The request data.
     */
    final public function setRequest(array $data)
    {
        // Element order follows Rootd_Iso_Geocode_Provider_Abstract::fetch argument order
        $this->_request = new Rootd_Object(array(
            'country'   => $data[0],
            'address'   => $data[1],
            'region'    => $data[2],
            'city'      => $data[3],
            'postcode'  => $data[4],
        ));

        unset($data);

        return $this;
    }

}