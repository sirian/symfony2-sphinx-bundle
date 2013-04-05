<?php

namespace Sirian\SphinxBundle\SphinxQL;

class Result
{
    protected $data;
    protected $meta = array();

    public function __construct(Data $data, $meta = array())
    {
        $this->data = $data;
        $this->meta = $meta;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getTotal()
    {
        return $this->getMetaField('total', 0);
    }

    public function getTotalFound()
    {
        return $this->getMetaField('total_found', 0);
    }

    public function getMetaField($field, $defaultValue)
    {
        return isset($this->meta[$field]) ? $this->meta[$field] : $defaultValue;
    }
}
