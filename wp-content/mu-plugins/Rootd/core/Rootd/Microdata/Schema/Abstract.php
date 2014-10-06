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

class Rootd_Microdata_Schema_Abstract extends Rootd_Object
{

    protected $_itemType        = '';
    protected $_defaultRenderer = 'Datatype/Text';

    /**
     * Initialize empty template.
     * 
     * @param array $data Internal object data.
     */
    public function __construct($data = null)
    {
        parent::__construct($data);

        $this->setData('template', '');
    }

    /**
     * Post-property merge rendering.
     * 
     * @param string $property The property name.
     * @param string $content  The rendered property content.
     * 
     * @return string
     */
    protected function _afterMergeProperty($property, $content = '')
    {
        return '</span>';
    }

    /**
     * Post-rendering.
     * 
     * @param string $output The rendered content.
     * 
     * @return string
     */
    protected function _afterRender($output = '')
    {
        return $output . '</span>';
    }

    /**
     * Pre-property merge rendering.
     * 
     * @param string $property The property name.
     * @param string $content  The rendered property content.
     * 
     * @return string
     */
    protected function _beforeMergeProperty($property, $content = '')
    {
        return '<span itemprop="' . $property . '">';
    }

    /**
     * Pre-rendering.
     * 
     * @return string
     */
    protected function _beforeRender()
    {
        return '<span itemscope itemtype="' . $this->getSchema() . '">';
    }

    /**
     * Write rendered property to template if available.
     * 
     * @param string $key     The property name.
     * @param string $content The rendered property.
     * @param string $output  The template or rendered output.
     * 
     * @return string
     */
    protected function _writeToTemplate($key, $content, $output = null)
    {
        $template   = is_null($output) ? $this->getTemplate() : $output;
        $output     = $content;

        if ($template != '') {
            $directive  = '{{' . $key . '}}';
            $output     = str_replace($directive, $content, $template);
        }

        return $output;
    }

    /**
     * Get the default property renderer.
     * 
     * @return Rootd_Microdata_Schema_Abstract
     */
    public function getDefaultRenderer()
    {
        $class      = 'Rootd_Microdata_Schema_' . $this->translateName($this->_defaultRenderer);
        $renderer   = null;

        if (class_exists($class)) {
            $renderer = Rootd::getSingleton($class);
        }

        return $renderer;
    }

    /**
     * Get schema properties.
     * 
     * @return array
     */
    public function getProperties()
    {
        return array();
    }

    /**
     * Get the property renderer instance.
     * 
     * @param string $propertyName The property name.
     * 
     * @return Rootd_Microdata_Schema_Abstract
     */
    public function getRenderer($propertyName)
    {
        $properties = $this->getProperties();

        if (array_key_exists($propertyName, $properties)) {
            $class    = 'Rootd_Microdata_Schema_' . $this->translateName($properties[$propertyName]);
            $renderer = Rootd::getSingleton($class);

            if ($renderer instanceof Rootd_Microdata_Schema_Abstract) {
                return $renderer;
            }
        }

        return null;
    }

    /**
     * Get the class schema (item type) URL.
     * 
     * @return string
     */
    public function getSchema()
    {
        return $this->_itemType;
    }

    /**
     * Merge the rendered properties into the template.
     * 
     * @param array $data The rendered properties.
     * 
     * @return string
     */
    public function merge($data = array())
    {
        $output = null;

        foreach ($data as $key => $content) {
            $property     = $this->_beforeMergeProperty($key, $content);

            $property    .= $content;

            $property    .= $this->_afterMergeProperty($key, $content);

            $output       = $this->_writeToTemplate($key, $property, $output);
        }

        return $output;
    }

    /**
     * Render the schema.
     * 
     * @param array|string  $data The schema property data.
     * @param string        $key  A single property name. Required
     *                            when rendering a single property
     *                            or the renderer has reached the
     *                            end of the rendering chain.
     * 
     * @return string
     */
    public function render($data = array(), $key = null)
    {
        $output = $this->_beforeRender();

        if (!is_array($data)) {
            $data = array( "{$key}" => (string) $data );
        }

        if (isset($data['_template'])) {
            $this->setTemplate($data['_template']);
            unset($data['_template']);
        }

        $renderedProperties = array();

        foreach ($data as $key => $value) {
            $renderer = $this->getRenderer($key);

            if (!$renderer) {
                $renderer = $this->getDefaultRenderer();
            }

            $renderedProperties[$key] = $renderer->render($value, $key);
        }

        $output .= $this->merge($renderedProperties);
        $output  = $this->_afterRender($output);

        return $output;
    }

    /**
     * Set the schema template.
     * 
     * @param string $template Template string or absolute path to
     *                         the template file.
     */
    public function setTemplate($template = '')
    {
        if (is_file($template))
        {
            $template = file_get_contents($template);
        }

        $this->setData('template', $template);

        return $this;
    }

    /**
     * Translate a schema path to its class name part.
     * 
     * @param string $name The schema path.
     * 
     * @return string
     */
    public function translateName($name = '')
    {
        return str_replace(' ', '_', ucwords(str_replace('/', ' ', strtolower($name))));
    }

}