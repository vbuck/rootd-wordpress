<?php

class Rootd_Element_Form_Abstract extends Rootd_Element_Abstract
{

    protected $_attributes  = array();
    protected $_isTypeSet   = false;

    public function _construct()
    {
        $this->setTagName('input');
    }

    protected function _beforeRender()
    {
        if ($this->getType()) {
            $this->setAttribute('type', $this->getType());
        }

        return parent::_beforeRender();
    }

    public function getAllowedAttributes()
    {
        return array('id', 'name', 'type', 'class', 'value', 'placeholder', 'disabled', 'readonly', 'style', 'onchange', 'onclick', 'onfocus', 'onblur');
    }

    public function setType($type)
    {
        if (!$this->_isTypeSet) {
            $this->setData('type', $type);

            $this->_isTypeSet = true;
        }

        return $this;
    }

}