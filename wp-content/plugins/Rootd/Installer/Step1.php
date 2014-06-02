<?php

/**
 * Rootd Framework installer step: pre-install.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Installer_Step1 extends Rootd_Installer_Step_Abstract
{

	public function _construct()
	{
		$this->setId('pre_install');
		$this->setTemplate('Step1.phtml');
	}

}