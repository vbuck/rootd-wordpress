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

    /* @var $_post Rootd_Object */
    protected $_post;

    /**
     * Get a prepared post object.
     * 
     * @param integer|null $id          The post ID.
     * @param boolean      $includeMeta Set whether to include the post meta data.
     * 
     * @return Rootd_Object
     */
    public function getPost($id = null, $includeMeta = true, $forceReload = false)
    {
        if ($this->_post && !$forceReload) {
            return $this->_post;
        }

        $data = get_post(get_queried_object_id(), 'ARRAY_A', 'display');

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

        $this->_post = new Rootd_Object(array_merge($data, $metaData));

        return $this->_post;
    }

}