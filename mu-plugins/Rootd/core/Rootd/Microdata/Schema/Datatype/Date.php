<?php

/**
 * Rootd microdata: datatype date schema.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Schema_Datatype_Date extends Rootd_Microdata_Schema_Datatype
{

    /**
     * Render date value in ISO 8601 format.
     * 
     * @param string $input Date string.
     * 
     * @return string
     */
    public function render($input = '')
    {
        try {
            $timestamp = strtotime($input);
        } catch(Exception $error) {
            $timestamp = time();
        }

        // @see http://www.php.net//manual/en/function.date.php
        return date('o-m-d', $timestamp);
    }

}