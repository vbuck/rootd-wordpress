<?php

/**
 * Post helper class.
 *
 * PHP Version 5
 * 
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Post_Helper extends Rootd_Object
{

    /**
     * Get a prepared post object.
     * 
     * @param integer|null $id          The post ID.
     * @param boolean      $includeMeta Set whether to include the post meta data.
     * 
     * @return Rootd_Object
     */
    public function getPost($id = null, $includeMeta = true)
    {
        $data = get_post($id, 'ARRAY_A', 'raw');

        if (!is_array($data)) {
            $data = array();
        }

        $metaData = array();

        if ($includeMeta && isset($data['ID'])) {
            $meta = get_post_meta($data['ID']);

            if (is_array($meta)) {
                foreach ($meta as $key => $values) {
                    $metaData[$key] = implode(',', $values);
                }
            }
        }

        $data = array_merge($data, $metaData);

        return new Rootd_Object($data);
    }

}