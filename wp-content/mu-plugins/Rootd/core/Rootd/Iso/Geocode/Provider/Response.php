<?php

class Rootd_Iso_Geocode_Provider_Response 
    implements IteratorAggregate, Countable
{

    protected $_items = array();

    public function addItem(Rootd_Object $item)
    {
        $this->_items[] = $item;

        return $this;
    }

    public function count()
    {
        return count($this->_items);
    }

    public function getFirstItem()
    {
        if (count($this->_items)) {
            reset($this->_items);

            return current($this->_items);
        }

        return new Rootd_Object();
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }

    public function getLastItem()
    {
        if (count($this->_items)) {
            reset($this->_items);
            
            return end($this->_items);
        }

        return new Rootd_Object();
    }

    public function getValues($field)
    {
        $values = array();

        foreach ($this->_items as $item) {
            if ($item->getData($field)) {
                $values[] = $item->getData($field);
            }
        }

        return $values;
    }

}