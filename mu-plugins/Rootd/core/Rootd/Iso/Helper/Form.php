<?php

/**
 * Region form JavaScript helper class.
 *
 * @package   Rootd_Iso
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Iso_Helper_Form
{

    /**
     * Get the absolute URL to the module.
     * 
     * @return string
     */
    public function getModuleUrl()
    {
        return plugins_url(
            null,
            (dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR)
        ) . '/core/Rootd/Iso';
    }

    /**
     * Get all countries as JSON.
     * 
     * @return string
     */
    public function getCountriesJson()
    {
        $data = array();

        foreach (Rootd_Iso_Country::getCountryOptions() as $option) {
            $data[$option['value']] = $option['label'];
        }

        return json_encode($data);
    }

    /**
     * Generate the form library JavaScript.
     * 
     * @param string $countrySelector  The country selector.
     * @param string $addressSelector  The address selector.
     * @param string $regionSelector   The region selector.
     * @param string $citySelector     The city selector.
     * @param string $postcodeSelector The postal code selector.
     * @param string $defaultCountry   The selected country.
     * @param string $defaultRegion    The selected region.
     * 
     * @return string.
     */
    public function getHelperJs(
        $countrySelector = '', 
        $addressSelector = '', 
        $regionSelector = '', 
        $citySelector = '',
        $postcodeSelector = '',
        $defaultCountry = '',
        $defaultRegion = ''
    )
    {
        return '
            <script type="text/javascript" src="' . ( $this->getModuleUrl() . '/Helper/js/form.js' ) . '"></script>
            <script type="text/javascript">
                new rootdIsoRegionSelector(
                    "' . $countrySelector . '",
                    "' . $addressSelector . '",
                    "' . $regionSelector . '",
                    "' . $citySelector . '",
                    "' . $postcodeSelector . '",
                    "' . $defaultCountry . '",
                    "' . $defaultRegion . '",
                    ' . $this->getCountriesJson() . ',
                    ' . $this->getRegionsJson() . '
                );
            </script>
        ';
    }

    /**
     * Get all regions as JSON.
     * 
     * @return string
     */
    public function getRegionsJson()
    {
        $data = array();

        foreach (Rootd_Iso_Country::getCountryRegionOptions() as $country => $regions) {
            if (!isset($data[$country])) {
                $data[$country] = array();
            }

            foreach ($regions as $option) {
                $data[$country][$option['value']] = $option['label'];
            }
        }

        return json_encode($data);
    }

}