<?php

/**
 * Geocode service class.
 *
 * PHP Version 5
 *
 * @package   Rootd_Iso
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

final class Rootd_Iso_Geocode_Service
{

    /* @var $_providers array */
    private static $_providers;

    /**
     * Load registered provider descriptor data.
     * 
     * @return void
     */
    private static function _initProviders()
    {
        $node = Rootd::getConfig()->getNode('modules/rootd/iso/geocode/providers');

        if (!$node->hasChildren()) {
            throw new Exception('No geocode service providers declared.');
        }

        self::$_providers   = array();

        foreach ($node->children() as $provider) {
            if ( !(string) $provider->scope ) {
                throw new Exception("Provider {$provider->getName()} must declare a scope.");
            }

            $class = (string) $provider->class;

            if (class_exists($class)) {
                $descriptor = new Rootd_Object();

                $descriptor->setId($provider->getName())
                    ->setPriority((int) $provider->priority)
                    ->setScope(explode(',', (string) $provider->scope))
                    ->setUseCache((bool) $provider->use_cache)
                    ->setInstanceClass($class)
                    ->setInstance(null);

                self::$_providers[$provider->getName()] = $descriptor;
            }
        }

        if (!count(self::$_providers)) {
            throw new Exception('No geocode service providers could be loaded.');
        }
    }

    /**
     * Get a provider connection by its priority.
     * 
     * @param integer $priority The provider priority.
     * 
     * @return Rootd_Iso_Geocode_Provider_Abstract|null
     */
    private static function _getConnectionByPriority($priority = 0)
    {
        $connection = null;

        if (!is_numeric($priority)) {
            $priority = 0;
        }

        foreach (self::$_providers as $descriptor) {
            if ((int) $descriptor->getPriority() <= $priority) {
                $connection = self::_getConnectionInstance($descriptor);
            }
        }

        return $connection;
    }

    /**
     * Get a provider connection by its scope.
     * 
     * @param string|array $priority The provider scope.
     * 
     * @return Rootd_Iso_Geocode_Provider_Abstract|null
     */
    private static function _getConnectionByScope($scope = null)
    {
        $connection = null;

        if (is_null($scope)) {
            return self::_getConnectionByPriority(0);
        }

        if (!is_array($scope)) {
            $scope = array($scope);
        }

        foreach (self::$_providers as $descriptor) {
            if (count( (array_intersect($descriptor->getScope(), $scope)) ) > 0) {
                $connection = self::_getConnectionInstance($descriptor);
                break;
            }
        }

        return $connection;
    }

    /**
     * Get the provider connection instance.
     * 
     * @param Rootd_Object $descriptor The provider descriptor.
     * 
     * @return Rootd_Iso_Geocode_Provider_Abstract
     */
    private static function _getConnectionInstance(Rootd_Object $descriptor)
    {
        if (!$descriptor->getInstance()) {
            $instance = Rootd::getSingleton($descriptor->getInstanceClass());

            if ( !($instance instanceof Rootd_Iso_Geocode_Provider_Abstract) ) {
                throw new Exception('Geocode provider must and instance of Rootd_Iso_Geocode_Provider_Abstract.');
            }

            $descriptor->setInstance($instance);

            $instance->setDescriptor($descriptor);
        }

        return $descriptor->getInstance();
    }

    /**
     * Get a provider connection.
     * 
     * @param string|array $scope The provider scope. If omitted, selection
     *                            will be based on provider priority.
     *                            
     * @return Rootd_Iso_Geocode_Provider_Abstract|null
     */
    public static function getConnection($scope = null)
    {
        if ( empty(self::$_providers) ) {
            self::_initProviders();
        }

        return self::_getConnectionByScope($scope);
    }

    /**
     * API method: Get all matching regions by postal code.
     * 
     * @param string $postcode The requested postal code.
     * @param string $country  The postal code country scope.
     * 
     * @return array
     */
    public static function getRegionsByPostcode($postcode, $country = null)
    {
        $result = self::getConnection($country)->fetch($country, null, null, null, $postcode);

        return $result->getValues('region');
    }

    /**
     * API method: Get the first matching region by postal code.
     * 
     * @param string $postcode The requested postal code.
     * @param string $country  The postal code country scope.
     * 
     * @return string|null
     */
    public static function getRegionByPostcode($postcode, $country = null)
    {
        $result = self::getConnection($country)->fetch($country, null, null, null, $postcode);
        $item   = $result->getFirstItem();

        return $item->getRegion();
    }

    /**
     * API method: Get all matching cities by postal code.
     * 
     * @param string $postcode The requested postal code.
     * @param string $country  The postal code country scope.
     * 
     * @return string|null
     */
    public static function getCitiesByPostcode($postcode, $country = null)
    {
        $result = self::getConnection($country)->fetch($country, null, null, null, $postcode);

        return $result->getValues('city');
    }

    /**
     * API method: Get the first matching city by postal code.
     * 
     * @param string $postcode The requested postal code.
     * @param string $country  The postal code country scope.
     * 
     * @return string|null
     */
    public static function getCityByPostcode($postcode, $country = null)
    {
        $result = self::getConnection($country)->fetch($country, null, null, null, $postcode);
        $item   = $result->getFirstItem();

        if ($item instanceof Rootd_Object) {
            return $item->getCity();
        }

        return null;
    }

}