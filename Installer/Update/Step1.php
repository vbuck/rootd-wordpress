<?php

/**
 * Rootd Framework installer step: core update.
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2015 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Installer_Update_Step1 
    extends Rootd_Installer_Step2 // We inherit from the base installation step
{

    public function _construct()
    {
        $this->setId('update');
        $this->setTemplate('Update/Step1.phtml');
    }

    public function getCurrentVersion()
    {
        return Rootd::getVersion();
    }

    public function getUpdateVersion()
    {
        return Rootd_Watchdog::getCoreVersionInfo()->getVersion();
    }

    public function install()
    {
        // Follow same steps as parent to update
        return parent::install();
    }

}