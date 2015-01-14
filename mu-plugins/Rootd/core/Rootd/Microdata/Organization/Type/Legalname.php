<?php

/**
 * Rootd microdata: Organization schema, telephone renderer.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Microdata_Organization_Type_Legalname extends Rootd_Microdata_Type_Abstract
{

    const SCHEMA = 'http://schema.org/Organization';

    /**
     * Load defaults implementation.
     * 
     * @param array $data Data for populating microdata.
     * 
     * @return array
     */
    public function loadDefaults($data = array())
    {
        return array_merge(array(
            'legal_name'    => '',
            'container'     => 'span',
        ), $data);
    }

    /**
     * Strip common non-numeric characters from number.
     * 
     * @param string $number The phone number.
     * 
     * @return string
     */
    public function parseNumber($number = '')
    {
        return preg_replace('/[\-\s\t\(\)]*/', '', $number);
    }

    /**
     * Renderer implementation.
     * 
     * @param  array  $data Data for populating microdata.
     * 
     * @return string
     */
    public function render($data = array())
    {
        $data = $this->loadDefaults($data);

        return '
            <' . $data['container'] . ' itemscope itemtype="' . self::SCHEMA . '">
                ' . ( $data['name'] ? '
                <span itemprop="name">' . $data['name'] . '</span>' : '' ) . '
                ' . ( $data['telephone'] ? '
                <span itemprop="telephone">
                    <a href="tel:' . $this->parseNumber($data['telephone']) . '">' . $data['telephone'] . '</a>
                </span>' : '' ) . '
            </' . $data['container'] . '>
        ';
    }

}