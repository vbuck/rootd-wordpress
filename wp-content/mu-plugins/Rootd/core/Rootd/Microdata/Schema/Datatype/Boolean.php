<?php

/**
 * Rootd microdata: datatype boolean schema.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Schema_Datatype_Boolean extends Rootd_Microdata_Schema_Datatype
{

    public function render($input = false)
    {
        if ($input === false || !$input || $input == 'false' || !(boolean) $input) {
            return false;
        }

        return true;
    }

}