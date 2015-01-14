<?php

class Rootd_Element_Form_Multicheckbox extends Rootd_Element_Form_Checkbox
{

    public function _construct()
    {
        $this->setTagName('div');
    }

    protected function _beforeRender()
    {
        return '';
    }

    protected function _render($html = '') 
    {
        $html .= $this->getBeforeElementHtml();

        foreach ($this->getOptions() as $option) {
            $element        = new Rootd_Element_Form_Checkbox();
            $generatedId    = preg_replace('/\W*/', '', $option['value']);

            $element->setUseLabel(true)
                ->setAttribute('id', "{$this->getAttribute('id')}_{$generatedId}")
                ->setAttribute('name', "{$this->getAttribute('name')}[]")
                ->setAttribute('value', $option['value'])
                ->setLabel($option['label']);

            $html .= $element->render();
        }

        $html .= $this->getAfterElementHtml();

        return $html;
    }

}