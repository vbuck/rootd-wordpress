<?php

/**
 * Country map interface.
 *
 * PHP Version 5
 *
 * @package   Rootd_Iso
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

interface Rootd_Iso_Interface
{

    /**
     * Get a country code by name.
     * 
     * @param  string $name The country name.
     * 
     * @return string
     */
    public static function getCountryCode($name);

    /**
     * Get a country name by code.
     * 
     * @param  string $code The country code.
     * 
     * @return string
     */
    public static function getCountryByCode($code);

    /**
     * Get all countries as an option array.
     * 
     * @return array
     */
    public static function getCountryOptions();

}