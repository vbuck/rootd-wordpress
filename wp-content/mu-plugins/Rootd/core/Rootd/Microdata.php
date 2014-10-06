<?php

/**
 * Rootd microdata generator.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

final class Rootd_Microdata {

    /**
     * Render the microdata.
     * 
     * @param string $schema The schema type.
     * @param array  $data   Data for populating microdata.
     *                       Can optionally specify a '_template' parameter
     *                       to use a custom template for rendering.
     * 
     * @return string
     */
    public static function render($schema, $data = array())
    {
        $class      = 'Rootd_Microdata_Schema_' . Rootd_Microdata_Schema_Abstract::translateName($schema);
        $instance   = Rootd::getSingleton($class);

        if ($instance) {
            return $instance->render($data);
        }

        return '';
    }

}