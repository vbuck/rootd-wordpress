<?php

class Rootd_Element_Form_Date extends Rootd_Element_Form_Abstract
{

    public function _construct()
    {
        $this->setTagName('input')
            ->setAttribute('type', 'text')
            ->setSelfClose(true);
    }

}