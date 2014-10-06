<?php

/**
 * Rootd Framework installer step: system check.
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Installer_Step2 
    extends Rootd_Installer_Step_Abstract
{

    /**
     * Prepare step.
     * 
     * @return void
     */
    public function _construct()
    {
        $this->setId('system_check');
        $this->setTemplate('Step2.phtml');
    }

    /**
     * Verify installation pre-reqequisites.
     * 
     * @return Rootd_Installer_Step2
     */
    public function checkSystem()
    {
        // Must meet PHP version requirement
        if (version_compare(Rootd_Installer::REQUIRED_VERSION, phpversion()) == 1) {
            $this->addError('PHP version ' . Rootd_Installer::REQUIRED_VERSION . ' or higher is required.');
        }

        // Temporary folder must be writeable
        if (!is_writeable(get_temp_dir())) {
            $this->addError('Your temporary path must be writeable: <code>' . get_temp_dir() . '</code>');
        }

        // 'mu-plugins' must already exist and be writeable, or else the 'wp-content' folder must be writeable
        if ( file_exists($this->getMuPluginPath()) && !is_writeable($this->getMuPluginPath()) ) {
            if(!is_writeable(WP_CONTENT_DIR)) {
                $this->addError('
                    We do not have permission to write to this folder: <code>' . WP_CONTENT_DIR . '</code> 
                    Please add write permissions or else manually create a writeable <code>mu-plugins</code> folder here.
                ');
            }
        }
    }

    /**
     * Get the mu-plugins path.
     * 
     * @return string
     */
    public function getMuPluginPath()
    {
        return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'mu-plugins';
    }

    /**
     * Perform the installation step.
     * 
     * @return boolean
     */
    public function install()
    {
        // Attempt to create 'mu-plugins' folder
        if (!file_exists($this->getMuPluginPath())){
            if (!@mkdir($this->getMuPluginPath())) {
                $this->addError("Unable to create folder: <code>{$this->getMuPluginPath()}</code>");
            }
        }

        try {
            $contents   = file_get_contents(Rootd_Installer::DOWNLOAD_URL);
            $file       = tempnam(get_temp_dir(), 'rootd_') . '.zip';

            $fp = fopen($file, 'w');
            flock($fp, LOCK_EX);
            fputs($fp, $contents);
            flock($fp, LOCK_UN);
            fclose($fp);

            $this->unzip($file, $this->getMuPluginPath());

            @unlink($file);
        } catch (Exception $error){
            $this->addError($error->getMessage());
        }

        return !$this->hasErrors();
    }

    /**
     * Extract an archive.
     * 
     * @param string $target      The target archive path.
     * @param string $destination The destination path.
     * 
     * @return boolean
     */
    public function unzip($target, $destination)
    {
        $status = false;

        // Try to use internal archive tools
        try {
            require_once(ABSPATH . '/wp-admin/includes/file.php');

            global $wp_filesystem;

            WP_Filesystem();

            if ($wp_filesystem) {
                $result = unzip_file($target, $destination);

                if (is_wp_error($result)) {
                    $code = $result->get_error_code();
                    throw new Exception("{$code}: {$result->get_error_message($code)}");
                } else {
                    $status = true;
                }
            }
        } catch (Exception $error) {
            $status = false;
            $this->addError($error->getMessage());
        }

        return $status;
    }

    /**
     * Render the step.
     * 
     * @return string
     */
    public function render() {
        $this->checkSystem();

        return parent::render();
    }

}