<?php

/**
 * Geocoding service cache class.
 *
 * PHP Version 5
 *
 * @package   Rootd_Iso
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

final class Rootd_Iso_Geocode_Cache
{

    private static $_cache      = array();
    private static $_cacheDir   = '';

    /**
     * Get a new response object.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response
     */
    private static function _getResponseObject()
    {
        return new Rootd_Iso_Geocode_Provider_Response();
    }

    /**
     * Load geocoding results from disk cache.
     * 
     * @param string $cacheKey The cached request key.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response|null
     */
    private static function _loadFromCache($cacheKey)
    {
        $path = self::getCacheDir() . DIRECTORY_SEPARATOR . $cacheKey;

        if (file_exists($path)) {
            $data = json_decode(@file_get_contents($path), true);

            self::$_cache[$cacheKey] = self::expand($data);

            return self::$_cache[$cacheKey];
        }

        return null;
    }

    /**
     * Write the geocoding results to disk.
     * 
     * @param string $cacheKey The cached request key.
     * @param array  $data     The results.
     * 
     * @return void
     */
    protected function _writeToCache($cacheKey, array $data)
    {
        $path   = self::getCacheDir() . DIRECTORY_SEPARATOR . $cacheKey;
        $fp     = fopen($path, 'w');

        if (is_resource($fp)) {
            flock($fp, LOCK_EX);
            fputs($fp, json_encode($data));
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    /**
     * Convert a geocode response to a cacheable object.
     * 
     * @param Rootd_Iso_Geocode_Provider_Response $object The geocode response object.
     * 
     * @return array
     */
    public static function compress(Rootd_Iso_Geocode_Provider_Response $object)
    {
        $data = array();

        foreach ($object->getItems() as $item) {
            $data[] = $item->getData();
        }

        return $data;
    }

    /**
     * Convert a cached object to a geocode response object.
     * 
     * @param array $data The cached response object.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response
     */
    public static function expand(array $data)
    {
        $object = self::_getResponseObject();

        reset($data);

        if (!is_numeric(key($data))) {
            $data = array($data);
        }

        foreach ($data as $item) {
            $object->addItem(new Rootd_Object($item));
        }

        unset($data);

        return $object;
    }

    /**
     * Get geocode results from cache.
     * 
     * @param string $cacheKey The cached request key.
     * 
     * @return Rootd_Iso_Geocode_Provider_Response|null
     */
    public static function get($cacheKey)
    {
        // Check cache in memory
        if (isset(self::$_cache[$cacheKey])) {
            return self::$_cache[$cacheKey];
        }

        // Else try disk cache
        return self::_loadFromCache($cacheKey);
    }

    /**
     * Get the geocode cache path.
     * 
     * @return string
     */
    public static function getCacheDir()
    {
        if (!self::$_cacheDir) {
            if ( ($userPath = (string) Rootd::getConfig()->getNode('modules/rootd/iso/geocode/cache_dir')) ) {
                self::$_cacheDir = $userPath;
            } else {
                self::$_cacheDir = Rootd::getBasePath('core', 'Rootd/Iso/cache');
            }
        }

        if (!file_exists(self::$_cacheDir) || !is_writable(self::$_cacheDir)) {
            throw new Exception('Geocode cache directory does not exist or is not writeable: ' . self::$_cacheDir);
        }

        return self::$_cacheDir;
    }

    /**
     * Write geocode results to cache.
     * 
     * @param string                              $cacheKey The cached request key.
     * @param Rootd_Iso_Geocode_Provider_Response $object   Rootd_Iso_Geocode_Provider_Response
     *
     * @return void
     */
    public static function set($cacheKey, Rootd_Iso_Geocode_Provider_Response $object)
    {
        $data = self::compress($object);

        self::$_cache[$cacheKey] = $data;

        self::_writeToCache($cacheKey, $data);
    }

}