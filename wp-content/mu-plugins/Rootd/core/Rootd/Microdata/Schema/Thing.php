<?php

/**
 * Rootd microdata: organization schema.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Schema_Thing extends Rootd_Microdata_Schema_Abstract
{

    /**
     * Get schema properties.
     * 
     * @return array
     */
    public function getProperties()
    {
        // Include inherited properties from abstract
        return array_merge(parent::getProperties(), array(
            'additionalType'    => 'DataType/Text/Url',
            'alternateName'     => 'DataType/Text',
            'description'       => 'DataType/Text',
            'image'             => 'DataType/Text/Url',
            'name'              => 'DataType/Text',
            'potentialAction'   => 'Thing/Action',
            'samesAs'           => 'DataType/Text/Url',
            'url'               => 'DataType/Text/Url',
        ));
    }

}