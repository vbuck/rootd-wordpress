<?php

/**
 * Region map interface.
 *
 * PHP Version 5
 *
 * @package   Rootd_Iso
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

interface Rootd_Iso_Country_Interface
{

    /**
     * Get a region code by name.
     * 
     * @param  string $name The region name.
     * 
     * @return string
     */
    public static function getRegionCode($name);

    /**
     * Get a region name by code.
     * 
     * @param  string $code The region code.
     * 
     * @return string
     */
    public static function getRegionByCode($code);

    /**
     * Get all regions for this country as an option array.
     * 
     * @return array
     */
    public static function getRegionOptions();

}