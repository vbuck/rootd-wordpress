<?php

/**
 * Rootd microdata: datatype number schema.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Schema_Datatype_Number extends Rootd_Microdata_Schema_Datatype
{

    /**
     * Render input as number.
     * 
     * @param string $input Numeric input.
     * 
     * @return string
     */
    public function render($input = 0)
    {
        if (is_numeric($input))
        {
            if (preg_match('/\./'), $input) {
                return floatval($input);
            }

            return intval($input);
        }

        return 0;
    }

}