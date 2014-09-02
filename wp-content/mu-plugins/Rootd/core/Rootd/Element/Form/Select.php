<?php

class Rootd_Element_Form_Select extends Rootd_Element_Form_Abstract
{

    public function _construct()
    {
        $this->setTagName('select');
    }

    protected function _beforeRender()
    {
        if ( ($value = $this->getAttribute('value')) ) {
            $this->setValue($value);
        }

        $this->setInnerHtml($this->_renderOptions());

        return '';
    }

    protected function _renderOptions()
    {
        $options    = array();
        $value      = $this->getValue();

        if (is_array($this->getOptions())) {
            foreach ($this->getOptions() as $option) {
                $selected   = $value == $option['value'] ? ' selected="selected"' : '';
                $options[]  = '<option value="' . esc_attr($option['value']) . '"' . $selected . '>' . $option['label'] . '</option>';
            }
        }

        return implode("\n", $options);
    }

}