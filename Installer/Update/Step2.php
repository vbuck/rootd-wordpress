<?php

/**
 * Rootd Framework installer step: core update completed.
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2015 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Installer_Update_Step2
    extends Rootd_Installer_Step_Abstract
{

    public function _construct()
    {
        $this->setId('update_complete');
        $this->setTemplate('Update/Step2.phtml');
    }

    public function getCurrentVersion()
    {
        return Rootd::getVersion();
    }

    public function install()
    {
        $this->getInstaller()
            ->setIsComplete(true);
    }

}