<?php

/**
 * Rootd Framework installer step: complete.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Installer_Step3 extends Rootd_Installer_Step_Abstract
{

	public function _construct()
	{
		$this->setId('complete');
		$this->setTemplate('Step3.phtml');
	}

	public function install()
	{
		$this->getInstaller()
			->setIsComplete(true);
	}

}