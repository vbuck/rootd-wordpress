<?php

/**
 * Rootd Framework installer.
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Installer 
{

    const DOWNLOAD_URL      = 'http://www.rickbuczynski.com/get-rootd-framework/download';
    const PARAM_PREFIX      = 'rootd_';
    const REQUIRED_VERSION  = '5.3.0';
    const SESSION_DATA_KEY  = 'rootd_installer_step_data';

    /* @var $_instance Rootd_Installer */
    private static $_instance;

    protected $_steps = array();

    /**
     * Autoload installation classes.
     * 
     * @return void
     */
    protected static function _loadFiles()
    {
        $files = self::_getFiles();
        sort($files);
        array_reverse($files);

        foreach ($files as $file) {
            require_once $file;
        }
    }

    /**
     * Get installer files.
     * 
     * @param string $path  The installer files path.
     * @param array  $files A referenced array of installer files.
     * 
     * @return array
     */
    protected static function _getFiles($path = '', &$files = array())
    {
        if (!$path) {
            $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Installer' . DIRECTORY_SEPARATOR;
        }

        $dh = opendir($path);
        while(false !== ($file = readdir($dh)))
        {
            if (preg_match('/^\.+$/', $file)) {
                continue;
            } else if (
                is_file($path . $file) && 
                strcasecmp(substr($file, -4), '.php') == 0
            )
            {
                $files[] = $path . $file;
            } else if( is_dir($path . $file) ) {
                self::_getFiles($path . $file . DIRECTORY_SEPARATOR, $files);
            }
        }

        return $files;
    }

    /**
     * Add steps to installation.
     * 
     * @return Rootd_Installer
     */
    protected function _loadSteps()
    {
        $this
            ->addStep('Rootd_Installer_Step1')
            ->addStep('Rootd_Installer_Step2')
            ->addStep('Rootd_Installer_Step3')
            ;

        return $this;
    }

    /**
     * Process the current step.
     * 
     * @return Rootd_Installer
     */
    protected function _processStep()
    {
        $params         = $this->getParams();
        $stepData       = $this->getStepData();
        $currentStep    = $stepData['current_step'];

        reset($this->_steps);

        // Set default step
        if (!$currentStep) {
            $currentStep = key($this->_steps);
        }

        // Requested step to install must match current
        if ( $params['action'] == 'complete' && $params['install'] !== $currentStep ) {
            return $this;
        } else if ( $params['action'] == 'complete' && $this->_steps[$currentStep]->install() ) { // Run step installation if requested
            $stepData['is_completed'] = true;
        }

        // Completed installations need to close out the session
        if ($this->getIsComplete()) {
            unset($_SESSION[Rootd_Installer::SESSION_DATA_KEY]);
        } else { // Advance step if completed
            if ($stepData['is_completed']) {
                $stepData['last_step']      = $currentStep;
                $stepData['is_completed']   = false;
                $currentStep                = $this->getNextStep();
            }

            $stepData['current_step'] = $currentStep;

            // Write step data to session
            $this->setStepData($stepData);
        }

        return $this;
    }

    /**
     * Add a step to the installation process.
     * 
     * @param string $class The installer step class.
     * @param string $id    An ID by which to mark the step.
     * 
     * @return Rootd_Installer
     */
    public function addStep($class, $id = null)
    {
        if (class_exists($class, false)) {
            $step = new $class();

            if (!is_null($id)) {
                $step->setId($id);
            }

            // ID must be supplied or set in the step class
            if ( $step->getId() && !array_key_exists($step->getId(), $this->_steps) ) {
                $this->_steps[$step->getId()] = $step;
            }
        }

        return $this;
    }

    /**
     * Get the installer instance.
     * 
     * @return Rootd_Installer
     */
    public static function getInstance()
    {
        if (self::$_instance) {
            return self::$_instance;
        }

        return null;
    }

    /**
     * Get the current step ID.
     * 
     * @return string
     */
    public function getCurrentStep()
    {
        $stepData = $this->getStepData();

        return $stepData['current_step'];
    }

    /**
     * Get the current step number.
     * 
     * @return integer
     */
    public function getCurrentStepIndex()
    {
        $currentStep    = $this->getCurrentStep();
        $index          = 1;

        foreach ($this->_steps as $key => $value) {
            if ($key == $currentStep) {
                return $index;
            }

            $index++;
        }

        return 1;
    }

    /**
     * Get the installation complete state.
     * 
     * @return boolean
     */
    public function getIsComplete()
    {
        $stepData = $this->getStepData();

        return $stepData['install_complete'];
    }

    /**
     * Get the last step ID.
     * 
     * @return string
     */
    public function getLastStep()
    {
        $stepData = $this->getStepData();

        return $stepData['last_step'];
    }

    /**
     * Get the next step.
     * 
     * @return string
     */
    public function getNextStep()
    {
        $currentStep = $this->getCurrentStep();

        reset($this->_steps);

        // Set default step
        if (!$currentStep) {
            $currentStep = key($this->_steps);
        }

        while (
            !is_null((key($this->_steps))) && 
            key($this->_steps) !== $currentStep
        )
        {
            next($this->_steps);
        }

        next($this->_steps);

        return key($this->_steps);
    }

    /**
     * Get the installation request parameters.
     * 
     * @return array
     */
    public function getParams()
    {
        $params = array('install' => null, 'action' => null);

        foreach ($_REQUEST as $key => $value) {
            if (substr($key, 0, strlen(self::PARAM_PREFIX)) == self::PARAM_PREFIX) {
                $params[substr($key, strlen(self::PARAM_PREFIX))] = urldecode($value);
            }
        }

        return $params;
    }

    /**
     * Get the step data.
     * 
     * @return array
     */
    public function getStepData()
    {
        $data = array(
            'last_step'         => null,
            'current_step'      => null,
            'is_completed'      => false,
            'install_complete'  => false,
            'total_steps'       => count($this->_steps)
        );

        if (isset($_SESSION[self::SESSION_DATA_KEY])) {
            $data = array_merge($data, json_decode($_SESSION[self::SESSION_DATA_KEY], true));
        }

        return $data;
    }

    /**
     * Get the total number of steps.
     * 
     * @return integer
     */
    public function getTotalSteps()
    {
        return count($this->_steps);
    }

    /**
     * Render the current step.
     * 
     * @return void
     */
    public function render()
    {
        $stepData       = $this->getStepData();
        $currentStep    = $stepData['current_step'];

        if ($currentStep) {
            echo $this->_steps[$currentStep]
                ->render();
        }
    }

    /**
     * Route the installation request.
     * 
     * @return Rootd_Installer
     */
    public function route()
    {
        $this->_processStep();

        add_action('admin_notices', array($this, 'render'));

        return $this;
    }

    /**
     * Run the installer.
     * 
     * @return Rootd_Installer
     */
    public function run()
    {
        $this->_loadSteps();

        $this->route();

        return $this;
    }

    /**
     * Mark the installation process as complete.
     * 
     * @param   boolean $state
     * @return  Rootd_Installer
     */
    public function setIsComplete($state = false)
    {
        $this->setStepData(array('install_complete' => $state));

        return $this;
    }

    /**
     * Write step data to the session.
     * 
     * @param   array $data
     * @return  Rootd_Installer
     */
    public function setStepData($data = array())
    {
        $_SESSION[self::SESSION_DATA_KEY] = json_encode(array_merge($this->getStepData(), $data));

        return $this;
    }

    /**
     * Start the installation process.
     * 
     * @return void
     */
    public static function start()
    {
        if (!self::$_instance) {
            self::_loadFiles();

            self::$_instance = new Rootd_Installer();
            self::$_instance->run();
        }
    }

    /**
     * Un-install framework files.
     * 
     * @return void
     */
    public static function uninstall()
    {
        try {
            $basePath = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'mu-plugins' . DIRECTORY_SEPARATOR . 'Rootd';

            self::uninstallByPath($basePath);
            @rmdir($basePath);
            @unlink($basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Rootd_Plugin.php');
        } catch(Exception $error) {
            throw new Exception('Failed to un-install Rootd Framework files. Please remove manually.');
        }
    }

    /**
     * Uninstall item(s) by path.
     * 
     * @param   string $path
     * @return  boolean
     */
    public static function uninstallByPath($path = '')
    {
        return is_file($path) ?
            @unlink($path) :
            array_map(array(__CLASS__, __FUNCTION__), glob($path.'/*')) == @rmdir($path);
    }

}