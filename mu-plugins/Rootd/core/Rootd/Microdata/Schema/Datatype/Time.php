<?php

/**
 * Rootd microdata: datatype time schema.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Schema_Datatype_Time extends Rootd_Microdata_Schema_Datatype
{

    /**
     * Render input string as time.
     * 
     * @param string $input Time string.
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
        return date('H:i:sZ', $timestamp);
    }

}