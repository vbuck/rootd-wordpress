<?php

/**
 * Rootd microdata: datatype text URL schema.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Schema_Datatype_Text_Url extends Rootd_Microdata_Schema_Datatype
{

    public function render($input = '')
    {
        if (!preg_match('/[a-zA-Z]+:\/\//', $input)) {
            $input = "//{$input}";
        }

        return $input;
    }

}