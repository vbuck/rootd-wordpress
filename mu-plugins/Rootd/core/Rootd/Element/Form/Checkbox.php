<?php

class Rootd_Element_Form_Checkbox extends Rootd_Element_Form_Abstract
{

    public function _construct()
    {
        $this->setTagName('input')
            ->setAttribute('type', 'checkbox')
            ->setSelfClose(true);
    }

    protected function _beforeRender()
    {
        $paramKey   = preg_replace('/[\[\]]*/', '', $this->getAttribute('name'));
        $params     = Rootd::getRequest()->getParam($paramKey);

        if (is_array($params) && in_array($this->getAttribute('value'), $params)) {
            $this->setAttribute('checked', 'checked');
        } else {
            $this->setAttribute('checked', false);
        }

        $this->setAfterElementHtml(" <span>{$this->getLabel()}</span>");
    }

    public function getAllowedAttributes()
    {
        return array_merge(
            parent::getAllowedAttributes(), 
            array('checked')
        );
    }

}