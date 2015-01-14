<?php

/**
 * Admin page abstract class.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Adminpage_Abstract
{

    const DEFAULT_TAB           = 'default';

    /* @var $_capability string */
    protected $_capability;
    /* @var $_container array */
    protected $_container;
    /* @var $_footerTemplate string */
    protected $_footerTemplate;
    /* @var $_headerTemplate string */
    protected $_headerTemplate;
    /* @var $_menuSlug string */
    protected $_menuSlug;
    /* @var $_menuTitle string */
    protected $_menuTitle;
    /* @var $_messages array */
    protected $_messages    = array();
    /* @var $_pageTitle string */
    protected $_pageTitle;
    protected $_parentSlug  = 'options-general.php';
    protected $_renderArea  = 'plugin';
    protected $_tabs        = array();

    /**
     * Calls extending initializer.
     *
     * @return void
     */
    public function __construct()
    {
        // Add default tab
        $this->addTab(self::DEFAULT_TAB, $this->__('Home'), 0);

        $this->setHeaderTemplate(Rootd::getBasePath('core') . 'Rootd' . DIRECTORY_SEPARATOR . 'Adminpage' . DIRECTORY_SEPARATOR . 'Header.phtml')
            ->setFooterTemplate(Rootd::getBasePath('core') . 'Rootd' . DIRECTORY_SEPARATOR . 'Adminpage' . DIRECTORY_SEPARATOR . 'Footer.phtml');

        $this->_construct();
    }

    /**
     * Internal constructor.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function _construct()
    {
        return $this;
    }

    /**
     * Post-render actions.
     * 
     * @param string $output The rendered content.
     * 
     * @return string
     */
    protected function _afterRender($output = '')
    {
        return $output;
    }


    /**
     * Pre-render actions.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    protected function _beforeRender()
    {
        return $this;
    }

    /**
     * Pre-save actions.
     * 
     * @param WP_Post $post
     * 
     * @return Rootd_Adminpage_Abstract
     */
    protected function _beforeSave(Rootd_Object $post)
    {
        return $this;
    }

    /**
     * Fetch the view from the template.
     * 
     * @return string
     */
    protected function _fetchView($template)
    {
        $view = '';

        try {
            ob_start();

            include $this->_getTemplatePath($template);
            
            $view = ob_get_contents();

            ob_end_clean();
        } catch(Exception $error) {
            return $this->__('Failed to fetch view in ' . get_class($this));
        }

        return $view;
    }

    /**
     * Get the highest registerd tab order.
     * 
     * @return integer
     */
    function _getMaxTabOrder()
    {
        $orders = array();

        foreach ($this->_tabs as $tab) {
            $orders[] = $tab->getOrder();
        }

        $maxOrder = max($orders);
        unset($orders);

        return $maxOrder;
    }

    /**
     * Get the path to the template.
     *
     * Returned path is relative to the module.
     * 
     * @return string
     */
    protected function _getTemplatePath($template)
    {
        // Do not resolve absolute paths
        if (file_exists($template)) {
            return $template;
        }

        $moduleName = array_shift((explode('_', get_class($this))));
        $path       = Rootd::getBasePath($this->_renderArea) . $moduleName . DIRECTORY_SEPARATOR . $template;

        return $path;
    }

    /**
     * Register page form fields.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    protected function _prepareForm()
    {
        return $this;
    }

    /**
     * Backend renderer.
     * 
     * @return string
     */
    protected function _render()
    {
        $tab    = $this->getActiveTab();
        $page   = '';

        $page  .= $this->_fetchView($this->_headerTemplate);
        $page  .= $this->_fetchView($tab->getTemplate());
        $page  .= $this->_fetchView($this->_footerTemplate);

        return $page;
    }

    /**
     * Backend save handler.
     * 
     * @param  WP_Post $post
     * 
     * @return Rootd_Adminpage_Abstract
     */
    protected function _save(Rootd_Object $post)
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
     * Add a system message to the page.
     * 
     * @param string $message The message.
     * @param string $type    The message type.
     *
     * @return Rootd_Adminpage_Abstract
     */
    public function addMessage($message, $type = Rootd_Session::MESSAGE_TYPE_DEFAULT)
    {
        Rootd::getSession()->addMessage($message, $type);

        return $this;
    }

    /**
     * Add a tabbed content area to the page.
     * 
     * @param string   $id       The tab ID.
     * @param string   $title    The tab content title.
     * @param string   $order    The tab order.
     * @param string   $template The tab template, relative to the module.
     * @param callable $callback An optional callback to determine tab visibility.
     *
     * @return Rootd_Adminpage_Abstract
     */
    public function addTab($id, $title = '', $order = null, $template = '', $callback = null)
    {
        if (isset($this->_tabs[$id])) {
            throw new Exception("Cannot add tab: {$id} already exists.");
        }

        if (!is_numeric($order)) {
            $order = $this->_getMaxTabOrder() + 1;
        }

        $this->_tabs[$id] = new Rootd_Object(
            array(
                'id'        => $id,
                'title'     => $title,
                'order'     => $order,
                'template'  => $template,
                'callback'  => $callback,
            )
        );

        return $this;
    }

    /**
     * Get the active tab.
     * 
     * @return Rootd_Object
     */
    public function getActiveTab()
    {
        if (is_array($this->_container)) {
            $default = $this->_container['tab'];
        } else {
            $default = self::DEFAULT_TAB;
        }

        return $this->getTab($this->getRequest()->getParam('tab', $default));
    }

    /**
     * Get the capability.
     * 
     * @return string
     */
    public function getCapability()
    {
        return $this->_capability;
    }

    /**
     * Get the form action URL.
     *
     * @param array $params
     * 
     * @return string
     */
    public function getFormAction($params = array())
    {
        return $this->getTabUrl($this->getActiveTab(), $params);
    }

    /**
     * Get the menu slug.
     * 
     * @return string
     */
    public function getMenuSlug($params = array())
    {
        if (!is_array($params)) {
            $params = array();
        }

        if (is_array($this->_container)) {
            $slug = $this->_container['slug'];
        } else {
            $slug = $this->_menuSlug;
        }

        if (!preg_match('/\?.+$/', $slug) && count($params) > 0) {
            $slug .= '&';
        } else {
            $slug = preg_replace('/\?*/', '', $slug);
        }

        return $slug . http_build_query($params);
    }

    /**
     * Get the menu title.
     * 
     * @return string
     */
    public function getMenuTitle()
    {
        return $this->_menuTitle;
    }

    /**
     * Get all session messages.
     * 
     * @return array
     */
    public function getMessages()
    {
        return Rootd::getSession()->getAllMessages();
    }

    /**
     * Get the parent slug.
     * 
     * @return string
     */
    public function getParentSlug()
    {
        // Use container slug if defined
        if (is_array($this->_container)) {
            return $this->_container['slug'];
        }

        return $this->_parentSlug;
    }

    /**
     * Get the request object.
     * 
     * @return Rootd_Request
     */
    public function getRequest()
    {
        return Rootd::getRequest();
    }

    /**
     * Get the default tab.
     * 
     * @return Rootd_Object
     */
    public function getDefaultTab()
    {
        return $this->getTab(self::DEFAULT_TAB);
    }

    /**
     * Get a registered tab by ID.
     * 
     * @param string $id The tab ID.
     * 
     * @return Rootd_Object|null
     */
    public function getTab($id)
    {
        if (isset($this->_tabs[$id])) {
            return $this->_tabs[$id];
        }

        return null;
    }

    /**
     * Get all registered tabs.
     * 
     * @return array
     */
    public function getTabs()
    {
        return $this->_tabs;
    }

    /**
     * Get the visibility state of the given tab.
     * 
     * @param Rootd_Object $tab The tab object.
     * 
     * @return boolean
     */
    public function getTabState(Rootd_Object $tab)
    {
        if (is_callable($tab->getCallback())) {
            return (bool) call_user_func(
                $tab->getCallback(), 
                new Rootd_Object(
                    array(
                        'active_tab' => $this->getActiveTab(),
                        'request'    => $this->getRequest(),
                        'page'       => $this,
                    )
                )
            );
        }

        return true;
    }

    /**
     * Get the tab content URL.
     * 
     * @param Rootd_Object $tab    The tab object.
     * @param array        $params Optional parameters.
     * 
     * @return string
     */
    public function getTabUrl(Rootd_Object $tab, $params = array()) 
    {
        if (!is_array($params)) {
            $params = array();
        }

        if (is_array($this->_container)) {
            return 'admin.php?' . http_build_query(
                array_merge(
                    $params, 
                    array(
                        'page' => $this->getParentSlug(),
                        'tab'  => $tab->getId(),
                    )
                )
            );
        }

        return $this->getParentSlug() . '?' . http_build_query(
            array_merge(
                $params, 
                array(
                    'page' => $this->getMenuSlug(),
                    'tab'  => $tab->getId(),
                )
            )
        );
    }

    /**
     * Get the template.
     * 
     * @return string
     */
    public function getTemplate($tab = self::DEFAULT_TAB)
    {
        if ( ($tab = $this->getTab($tab)) ) {
            return $tab['template'];
        }
        
        return null;
    }

    /**
     * Get the page or tab title.
     *
     * @param string $tab The tab ID.
     * 
     * @return string
     */
    public function getTitle($tab = null)
    {
        if (is_null($tab)) {
            return $this->_pageTitle;
        } else if ( ($tab = $this->getTab($tab)) ) {
            return $tab->getTitle();
        }

        return '';
    }

    /**
     * Initialize the page in WordPress.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function initialize()
    {
        $this->_prepareForm();

        return $this;
    }

    /**
     * Convenience method to enqueue scripts and stylesheets.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function loadScripts()
    {
        return $this;
    }

    /**
     * Register the page for use.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public static function register()
    {
        if (is_admin()) {
            $class      = get_called_class();
            $instance   = new $class();

            add_action('admin_menu', array($instance, 'setupPage'));
            add_action('admin_init', array($instance, 'initialize'));
            add_action('admin_enqueue_scripts', array($instance, 'loadScripts'));

            return $instance;
        }
    }

    /**
     * Frontend page renderer.
     * 
     * @return void
     */
    public function render()
    {
        $this->_beforeRender();
        $output = $this->_render();
        $output = $this->_afterRender($output);

        echo $output;
    }

    /**
     * Remove a tab by ID.
     * 
     * @param string $id The tab ID.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function removeTab($id)
    {
        if (isset($this->_tabs[$id])) {
            unset($this->_tabs[$id]);
        }

        return $this;
    }

    /**
     * Set the capability.
     * 
     * @param string $capability The capability.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function setCapability($capability = '')
    {
        $this->_capability = $capability;

        return $this;
    }

    /**
     * Set the menu slug.
     * 
     * @param string $slug The menu slug.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function setMenuSlug($slug = '')
    {
        $this->_menuSlug = $slug;

        return $this;
    }

    /**
     * Set the menu title.
     * 
     * @param string $title The menu title.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function setMenuTitle($title = '')
    {
        $this->_menuTitle = $title;

        return $this;
    }

    /**
     * Set the page footer template.
     * 
     * @param string $template The page footer template path, relative to the module.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function setFooterTemplate($template)
    {
        $this->_footerTemplate = $template;

        return $this;
    }

    /**
     * Set the page header template.
     * 
     * @param string $template The page header template path, relative to the module.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function setHeaderTemplate($template)
    {
        $this->_headerTemplate = $template;

        return $this;
    }

    /**
     * Configure the container (parent) page.
     * 
     * @param string  $title    The container title in menu.
     * @param string  $slug     The container page slug.
     * @param string  $tab      The base tab ID to use.
     * @param string  $icon     The icon URL for the menu item.
     * @param integer $position The position in menu.
     *
     * @return Rootd_Adminpage_Abstract
     */
    public function setContainer($title, $slug, $tab = self::DEFAULT_TAB, $icon = '', $position = null)
    {
        $this->_container = array(
            'title'     => $title,
            'slug'      => $slug,
            'tab'       => $tab,
            'icon'      => $icon,
            'position'  => $position,
        );

        return $this;
    }

    /**
     * Set the parent slug.
     * 
     * @param string $slug The slug name.
     *
     * @return Rootd_Adminpage_Abstract
     */
    public function setParentSlug($slug = '')
    {
        $this->_parentSlug = $slug;

        return $this;
    }

    /**
     * Set the tab template.
     * 
     * @param string $template The page template path, relative to the module.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function setTemplate($template, $tab = self::DEFAULT_TAB)
    {
        if ( ($tab = $this->getTab($tab)) ) {
            $tab->setTemplate($template);
        }

        return $this;
    }

    /**
     * Set the page or tab title.
     * 
     * @param string $title The page title.
     * @param string $tab   The tab ID.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function setTitle($title = '', $tab = null)
    {
        // Sets page title if tab not specified
        if (is_null($tab)) {
            $this->_pageTitle = $title;
        } else if ( ($tab = $this->getTab($tab)) ) { // Else set tab title
            $tab->setTitle($title);
        }

        return $this;
    }

    /**
     * Register the page setup processes.
     * 
     * @return Rootd_Adminpage_Abstract
     */
    public function setupPage()
    {
        // Render page with container
        if (is_array($this->_container)) {
            add_menu_page(
                $this->getTitle(),
                $this->_container['title'],
                $this->getCapability(),
                $this->_container['slug'],
                array($this, 'render'),
                $this->_container['icon'],
                $this->_container['position']
            );

            foreach ($this->getTabs() as $tab) {
                if ($this->getTabState($tab)) {
                    // Generate extra slug params for menu items (do not include for container base tab)
                    $params = is_array($tab->getParams()) ? $tab->getParams() : array();

                    if ($tab->getId() !== $this->_container['tab']) {
                        $params['tab'] = $tab->getId();
                    }

                    add_submenu_page(
                        $this->getParentSlug(),
                        $tab->getTitle(),
                        $tab->getTitle(),
                        $this->getCapability(),
                        $this->getMenuSlug($params),
                        array($this, 'render')
                    );
                }
            }
        } else { // Otherwise render as single, tabbed page
            add_submenu_page(
                $this->getParentSlug(),
                $this->getTitle(),
                $this->getMenuTitle(),
                $this->getCapability(),
                $this->getMenuSlug(),
                array($this, 'render')
            );
        }

        return $this;
    }

}