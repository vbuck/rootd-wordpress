<?php

/**
 * Rootd microdata schema type renderer abstract.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

abstract class Rootd_Microdata_Type_Abstract
{

    const SCHEMA = '';

    /**
     * Load default microdata properties.
     *
     * @param array $data Data for populating microdata.
     * 
     * @return array
     */
    public function loadDefaults($data = array()) 
    {
        return array_merge(array(), $data);
    }

    /**
     * Render the microdata.
     * 
     * @param  array  $data Data for populating microdata.
     * 
     * @return string
     */
    abstract public function render($data = array());

    /**
     * Translate input for class mapping.
     * 
     * @param string $input The input string.
     * 
     * @return string
     */
    public static function translateVar($input = '')
    {
        return ucwords((str_replace('_', '', $input));
    }

}