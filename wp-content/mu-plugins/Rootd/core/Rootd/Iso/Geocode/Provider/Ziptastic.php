<?php

/**
 * Ziptastic geocode service provider class.
 *
 * PHP Version 5
 *
 * @see       http://ziptasticapi.com/
 *
 * @package   Rootd_Iso
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Iso_Geocode_Provider_Ziptastic
    extends Rootd_Iso_Geocode_Provider_Abstract
{

    protected $_api = 'http://ziptasticapi.com';
    /* @var $_connection resource */
    protected $_connection;
    protected $_validateRegexp = 
        '/
            (?(DEFINE)
                (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )    
                (?<boolean>   true | false | null )
                (?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
                (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
                (?<pair>      \s* (?&string) \s* : (?&json)  )
                (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
                (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
            )
            \A (?&json) \Z
        /six';

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
    protected function _fetch(
        $country = null, 
        $address = null, 
        $region = null, 
        $city = null, 
        $postcode = null
    )
    {
        $connection = $this->getConnection();

        curl_setopt($connection, CURLOPT_URL, $this->getUrl($postcode));

        $result = $this->_parseResponse(curl_exec($connection));

        curl_close($connection);

        return $result;
    }

    /**
     * Provider response parse implementation.
     * 
     * @param string $response The cURL response.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response
     */
    protected function _parseResponse($response)
    {
        $result = $this->getResponseObject();

        if (!preg_match($this->_validateRegexp, $response)) {
            return $response;
        }

        $data = json_decode($response, true);
        $item = new Rootd_Object(
            array(
                'country'   => $this->format('country', $data['country']),
                'address'   => $this->format('address', $this->getRequest()->getAddress()),
                'region'    => $this->format('region', $data['state']),
                'city'      => $this->format('city', $data['city']),
                'postcode'  => $this->format('postcode', $this->getRequest()->getPostcode()),
            )
        );

        $result->addItem($item);

        return $result;
    }

    /**
     * Provider connection initialization implementation.
     * 
     * @return void
     */
    protected function _prepareConnection()
    {
        $this->_connection = curl_init();

        curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * Build the API request URL.
     * 
     * @param string $postcode The postal code.
     * 
     * @return string
     */
    public function getUrl($postcode = '')
    {
        return "{$this->_api}/{$postcode}";
    }

}