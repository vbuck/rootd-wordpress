<?php

/**
 * Rootd admin modal class. 
 * Based on the work of aut0poietic.
 *
 * A disastrous mess ... but it works :)
 *
 * @see       https://github.com/aut0poietic/wp-admin-modal-example
 *
 * @package   Rootd_Adminmodal
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Adminmodal_Abstract extends Rootd_Object
{

    protected $_controls    = array();
    protected $_instanceId  = 'adminmodal';
    protected $_templates   = array();

    public function __construct()
    {
        $this->setTemplate('base_views', 'Adminmodal/Views/Default.phtml')
            ->setShowCloseControl(true);

        $this->_construct();
    }

    public function _construct()
    {
        return $this;
    }

    /**
     * Translator.
     * 
     * @param string $text   Input.
     * @param string $domain Text-domain.
     * 
     * @return string
     */
    public function __($text = '', $domain = '')
    {
        return $text;
    }

    /**
     * Translator with output.
     * 
     * @param string $text   Input.
     * @param string $domain Text-domain.
     * 
     * @return void
     */
    public function _e($text = '', $domain = '')
    {
        echo $text;
    }

    /**
     * Translator for singular/plural.
     * 
     * @param string $text   Input.
     * @param string $domain Text-domain.
     * 
     * @return string
     */
    public function _n($singular = '', $plural ='', $number = null, $domain ='')
    {
        return $singular;
    }

    /**
     * Load the specified template.
     * 
     * @param string $id
     * 
     * @return string
     */
    protected function _fetchView($id, $area = 'plugin', $moduleName = null)
    {
        $view = '';

        try {
            ob_start();

            include $this->_getTemplatePath($id, $area, $moduleName);
            
            $view = ob_get_contents();

            ob_end_clean();
        } catch(Exception $error) {
            return $this->__('Failed to fetch view in ' . get_class($this));
        }

        return $view;
    }

    /**
     * Get the path to the template by id.
     * 
     * @param string $id   The template id.
     * @param string $area The render area.
     * 
     * @return string|null
     */
    protected function _getTemplatePath($id, $area = 'plugin', $moduleName = null)
    {
        $path = null;

        if (isset($this->_templates[$id])) {
            $moduleName = $moduleName ? $moduleName : array_shift((explode('_', get_class($this))));
            $path       = Rootd::getBasePath($area) . $moduleName . DIRECTORY_SEPARATOR . $this->_templates[$id];
        }

        return $path;
    }

    protected function _loadScripts()
    {
        // Extending classes will add wp_enqueue_script calls here
        return $this;
    }

    protected function _loadTemplates()
    {
        // Extending classes will add include calls here
        return $this;
    }

    protected function _loadTranslations()
    {
        // Extending class will add wp_localize_script calls here
        return $this;
    }

    public function addControl($id, $label = '', $properties = array())
    {
        $element = new Rootd_Element_Form_Button();

        $element->setLabel($label)
            ->setAttributes($properties);

        $this->_controls[$id] = $element;

        return $this;
    }

    public function getControls()
    {
        return $this->_controls;
    }

    public function getInstanceId()
    {
        return $this->_instanceId;
    }

    public function getScripts($hook)
    {
        if (in_array($hook, array('post.php', 'post-new.php'))) {
            if (!Rootd::registry('adminmodal_scripts_loaded')) {
                $baseUrl = plugin_dir_url(Rootd::getBasePath('base')) . 'Rootd/core/Rootd/Adminmodal/';

                wp_enqueue_script(
                    'rootd_adminmodal', 
                    $baseUrl . 'js/adminmodal.js', 
                    array('jquery', 'backbone')
                );

                wp_enqueue_style(
                    'rootd_adminmodal', 
                    $baseUrl . 'css/adminmodal.css'
                );

                Rootd::register('adminmodal_scripts_loaded', true);
            }

            $this->_loadScripts();
            
            $this->getTranslations();
        }

        return $this;
    }

    public function getTemplates()
    {
        echo $this->_fetchView('base_views', 'core', 'Rootd');

        $this->_loadTemplates();

        // End output to browser for AJAX
        exit;
    }

    public function getTranslations()
    {
        wp_localize_script(
            'rootd_adminmodal',
            'rootd_adminmodal_l10n',
            array()
        );

        return $this;
    }

    public function initialize()
    {
        add_action('admin_enqueue_scripts', array($this, 'getScripts'));
        add_action('wp_ajax_templates_' . $this->getInstanceId(), array($this, 'getTemplates'));

        return $this;
    }

    public static function register()
    {
        if (is_admin()) {
            $class      = get_called_class();
            $instance   = new $class();

            add_action('admin_init', array($instance, 'initialize'));

            return $instance;
        }
    }

    public function setInstanceId($id)
    {
        $this->_instanceId = $id;

        return $this;
    }

    /**
     * Set a template.
     * 
     * @param string $id       The template ID.
     * @param string $template The template path relative to the module.
     *
     * @return Rootd_Adminmodal_Abstract
     */
    public function setTemplate($id, $template)
    {
        $this->_templates[$id] = $template;

        return $this;
    }

}