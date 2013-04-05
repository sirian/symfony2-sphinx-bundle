<?php

namespace Sirian\SphinxBundle\SphinxQL;

class Query
{
    /**
     * @var Connection $connection
     */
    protected $connection;
    protected $sql;
    protected $prepared = null;
    protected $params = array();

    public function __construct(Connection $connection, $sql, $params = array())
    {
        $this->connection = $connection;
        $this->sql = $sql;
        $this->params = $params;
    }

    public function execute($params = null)
    {
        if (is_null($params)) {
            $params = $this->params;
        }
        $sql = $this->connection->execute($this->sql, $params);
        $data =  new Data($this->fetchAllAssoc($sql));
        $meta = $this->parseMeta($this->connection->execute('SHOW META'));
        return new Result($data, $meta, $sql);
    }

    protected function fetchAllAssoc(\mysqli_result $a)
    {
        $res = [];
        foreach ($a as $row) {
            $res[] = $row;
        }
        return $res;
    }

    protected function parseMeta(\mysqli_result $a)
    {
        $res = [];
        foreach ($a as $row) {
            $res[$row['Variable_name']] = $row['Value'];
        }
        return $res;
    }
}
