<?php

namespace Sirian\SphinxBundle\SphinxQL;

class Data extends \ArrayIterator
{
    public function fetchAll($assocKey = null)
    {
        if (is_null($assocKey)) {
            return $this->getArrayCopy();
        }

        $res = [];
        foreach ($this as $row) {
            $res[$row[$assocKey]] = $row;
        }
        return $res;
    }

    public function getIds()
    {
        return $this->fetchColumn('id');
    }

    public function fetchColumn($key)
    {
        $arr = [];
        foreach ($this as $row) {
            $arr[] = $row[$key];
        }
        return $arr;
    }

    public function fetchRow()
    {
        $el = $this->current();
        $this->next();
        return $el;
    }
}
