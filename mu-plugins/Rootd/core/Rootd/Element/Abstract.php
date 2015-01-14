<?php

class Rootd_Element_Abstract extends Rootd_Object
{

    public function _construct()
    {
        $this->setTagName('div');
        $this->setSelfClose(false);
    }

    protected function _afterRender($html = '')
    {
        return $html;
    }

    protected function _beforeRender()
    {
        return '';
    }

    protected function _render($html = '')
    {
        if ($this->getUseLabel()) {
            $html .= '<label for="' . esc_attr($this->getAttribute('id')) . '">';
        }

        $html .= $this->getBeforeElementHtml();

        $html .= "<{$this->getTagName()} {$this->getAttributesHtml()}";

        if ($this->getSelfClose()) {
            $html .= ' />';
        } else {
            $html .= '>';
            $html .= $this->getInnerHtml();
            $html .= "</{$this->getTagName()}>";
        }

        $html .= $this->getAfterElementHtml();

        if ($this->getUseLabel()) {
            $html .= '</label>';
        }

        return $html;
    }

    public function getAllowedAttributes()
    {
        return array();
    }

    public function getAttribute($attribute)
    {
        if (isset($this->_attributes[$attribute])) {
            return $this->_attributes[$attribute];
        }

        return null;
    }

    public function getAttributes()
    {
        return $this->_attributes;
    }

    public function getAttributesHtml()
    {
        $attributes = array();

        foreach ($this->_attributes as $key => $value) {
            $attributes[] = $key . '="' . esc_attr($value) . '"';
        }

        return implode(' ', $attributes);
    }

    public function setAttribute($attribute, $value = '')
    {
        if ($value === false) {
            $this->unsetAttribute($attribute);
        } else if (!count($this->getAllowedAttributes()) || in_array($attribute, $this->getAllowedAttributes())) {
            $this->_attributes[strtolower($attribute)] = $value;
        }

        return $this;
    }

    public function setAttributes($attributes = null)
    {
        if (is_array($attributes)) {
            $this->_attributes = array();

            foreach ($attributes as $key => $value) {
                $this->setAttribute($key, $value);
            }
        }

        return $this;
    }

    public function unsetAttribute($attribute)
    {
        if (!is_null($this->getAttribute($attribute))) {
            unset($this->_attributes[$attribute]);
        }

        return $this;
    }

    public function render()
    {
        $html   = $this->_beforeRender();
        $html   = $this->_render($html);
        $html   = $this->_afterRender($html);

        return $html;
    }

}