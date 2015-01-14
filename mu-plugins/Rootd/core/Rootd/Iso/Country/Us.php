<?php

/**
 * Region map class for United States.
 *
 * PHP Version 5
 *
 * @package   Rootd_Iso
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

final class Rootd_Iso_Country_Us
    implements Rootd_Iso_Country_Interface
{

    /* @var $_regions array */
    private static $_regions;

    /**
     * Get a region code by name.
     * 
     * @param  string $name The region name.
     * 
     * @return string
     */
    public static function getRegionCode($name)
    {
        foreach (self::getRegionOptions() as $option) {
            if (strcasecmp($option['label'], $name) == 0) {
                return $option['value'];
            }
        }

        return $name;
    }

    /**
     * Get a region name by code.
     * 
     * @param  string $code The region code.
     * 
     * @return string
     */
    public static function getRegionByCode($code)
    {
        foreach (self::getRegionOptions() as $option) {
            if (strcasecmp($option['value'], $code) == 0) {
                return $option['label'];
            }
        }

        return $code;
    }

    /**
     * Get all regions for this country as an option array.
     * 
     * @return array
     */
    public static function getRegionOptions()
    {
        // @todo support merging with config XML
        if (!self::$_regions) {
            self::$_regions = array(
                array(
                    'value' => 'AL',
                    'label' => 'Alabama',
                ),
                array(
                    'value' => 'AK',
                    'label' => 'Alaska',
                ),
                array(
                    'value' => 'AZ',
                    'label' => 'Arizona',
                ),
                array(
                    'value' => 'AR',
                    'label' => 'Arkansas',
                ),
                array(
                    'value' => 'CA',
                    'label' => 'California',
                ),
                array(
                    'value' => 'CO',
                    'label' => 'Colorado',
                ),
                array(
                    'value' => 'CT',
                    'label' => 'Connecticut',
                ),
                array(
                    'value' => 'DE',
                    'label' => 'Delaware',
                ),
                array(
                    'value' => 'DC',
                    'label' => 'District Of Columbia',
                ),
                array(
                    'value' => 'FL',
                    'label' => 'Florida',
                ),
                array(
                    'value' => 'GA',
                    'label' => 'Georgia',
                ),
                array(
                    'value' => 'HI',
                    'label' => 'Hawaii',
                ),
                array(
                    'value' => 'ID',
                    'label' => 'Idaho',
                ),
                array(
                    'value' => 'IL',
                    'label' => 'Illinois',
                ),
                array(
                    'value' => 'IN',
                    'label' => 'Indiana',
                ),
                array(
                    'value' => 'IA',
                    'label' => 'Iowa',
                ),
                array(
                    'value' => 'KS',
                    'label' => 'Kansas',
                ),
                array(
                    'value' => 'KY',
                    'label' => 'Kentucky',
                ),
                array(
                    'value' => 'LA',
                    'label' => 'Louisiana',
                ),
                array(
                    'value' => 'ME',
                    'label' => 'Maine',
                ),
                array(
                    'value' => 'MD',
                    'label' => 'Maryland',
                ),
                array(
                    'value' => 'MA',
                    'label' => 'Massachusetts',
                ),
                array(
                    'value' => 'MI',
                    'label' => 'Michigan',
                ),
                array(
                    'value' => 'MN',
                    'label' => 'Minnesota',
                ),
                array(
                    'value' => 'MS',
                    'label' => 'Mississippi',
                ),
                array(
                    'value' => 'MO',
                    'label' => 'Missouri',
                ),
                array(
                    'value' => 'MT',
                    'label' => 'Montana',
                ),
                array(
                    'value' => 'NE',
                    'label' => 'Nebraska',
                ),
                array(
                    'value' => 'NV',
                    'label' => 'Nevada',
                ),
                array(
                    'value' => 'NH',
                    'label' => 'New Hampshire',
                ),
                array(
                    'value' => 'NJ',
                    'label' => 'New Jersey',
                ),
                array(
                    'value' => 'NM',
                    'label' => 'New Mexico',
                ),
                array(
                    'value' => 'NY',
                    'label' => 'New York',
                ),
                array(
                    'value' => 'NC',
                    'label' => 'North Carolina',
                ),
                array(
                    'value' => 'ND',
                    'label' => 'North Dakota',
                ),
                array(
                    'value' => 'OH',
                    'label' => 'Ohio',
                ),
                array(
                    'value' => 'OK',
                    'label' => 'Oklahoma',
                ),
                array(
                    'value' => 'OR',
                    'label' => 'Oregon',
                ),
                array(
                    'value' => 'PA',
                    'label' => 'Pennsylvania',
                ),
                array(
                    'value' => 'RI',
                    'label' => 'Rhode Island',
                ),
                array(
                    'value' => 'SC',
                    'label' => 'South Carolina',
                ),
                array(
                    'value' => 'SD',
                    'label' => 'South Dakota',
                ),
                array(
                    'value' => 'TN',
                    'label' => 'Tennessee',
                ),
                array(
                    'value' => 'TX',
                    'label' => 'Texas',
                ),
                array(
                    'value' => 'UT',
                    'label' => 'Utah',
                ),
                array(
                    'value' => 'VT',
                    'label' => 'Vermont',
                ),
                array(
                    'value' => 'VA',
                    'label' => 'Virginia',
                ),
                array(
                    'value' => 'WA',
                    'label' => 'Washington',
                ),
                array(
                    'value' => 'WV',
                    'label' => 'West Virginia',
                ),
                array(
                    'value' => 'WI',
                    'label' => 'Wisconsin',
                ),
                array(
                    'value' => 'WY',
                    'label' => 'Wyoming',
                ),
            );
        }

        return self::$_regions;
    }

}