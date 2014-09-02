<?php

class Rootd_Element_Form_Button extends Rootd_Element_Form_Abstract
{

    public function _construct()
    {
        $this->setTagName('button')
            ->setAttribute('type', 'button');
    }

    public function _beforeRender()
    {
        $this->setInnerHtml($this->getLabel());

        return '';
    }

}