<?php

namespace Sirian\SphinxBundle\SphinxQL;

class QueryBuilder
{
    protected $connection;

    protected $select = array();
    protected $from = array();
    protected $offset = 0;
    protected $limit = null;
    protected $match = array();
    protected $groupBy = array();
    protected $withinGroupOrderBy = array();
    protected $orderBy = array();
    protected $options = array();
    protected $where = array();
    protected $parameters = array();
    protected $sql;
    protected $compiled = false;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @return QueryBuilder
     */
    public function select($select = null)
    {
        $this->compiled = false;
        $this->resetSelect();

        if (is_null($select)) {
            return $this;
        }

        $selects = is_array($select) ? $select : func_get_args();
        return $this->addSelect($selects);
    }

    /**
     * @return QueryBuilder
     */
    public function addSelect($select)
    {
        $this->compiled = false;
        $selects = is_array($select) ? $select : func_get_args();
        foreach ($selects as $select) {
            $this->select[] = $select;
        }

        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function resetSelect()
    {
        $this->compiled = false;
        $this->select = array();
        return $this;
    }

    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return QueryBuilder
     */
    public function from($index)
    {
        $this->compiled = false;
        $this->resetFrom();
        $index = is_array($index) ? $index : func_get_args();
        return $this->addFrom($index);
    }

    /**
     * @return QueryBuilder
     */
    public function addFrom($index)
    {
        $this->compiled = false;
        $index = is_array($index) ? $index : func_get_args();
        foreach ($index as $from) {
            $this->from[] = $from;
        }
        return $this;
    }

    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @return QueryBuilder
     */
    public function resetFrom()
    {
        $this->compiled = false;
        $this->from = array();
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function where($predicate)
    {
        $this->compiled = false;
        $this->resetWhere();
        $this->andWhere($predicate);
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function andWhere($predicate)
    {
        $this->compiled = false;
        $this->where[] = $predicate;
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function resetWhere()
    {
        $this->compiled = false;
        $this->where = array();
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function match($predicate)
    {
        $this->compiled = false;
        $this->resetMatch();
        $this->addMatch($predicate);
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function addMatch($predicate)
    {
        $this->compiled = false;
        $this->match[] = $predicate;
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function resetMatch()
    {
        $this->compiled = false;
        $this->match = array();
        return $this;
    }

    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * @return QueryBuilder
     */
    public function groupBy($field)
    {
        $this->compiled = false;
        $this->resetGroupBy();
        $fields = is_array($field) ? $field : func_get_args();
        $this->addGroupBy($fields);
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function addGroupBy($field)
    {
        $this->compiled = false;
        $fields = is_array($field) ? $field : func_get_args();
        foreach ($fields as $field) {
            $this->groupBy[] = $field;
        }
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function resetGroupBy()
    {
        $this->compiled = false;
        $this->groupBy = array();
        return $this;
    }

    public function getWithinGroupOrderBy()
    {
        return $this->withinGroupOrderBy;
    }

    /**
     * @return QueryBuilder
     */
    public function withinGroupOrderBy($field, $direction = 'ASC')
    {
        $this->compiled = false;
        $this->resetWithinGroupOrderBy();
        $this->addWithinGroupOrderBy($field, $direction);
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function addWithinGroupOrderBy($field, $direction = 'ASC')
    {
        $this->compiled = false;
        $direction = strtoupper($direction) == 'ASC' ? 'ASC' : 'DESC';
        $this->withinGroupOrderBy[] = array($field, $direction);
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function resetWithinGroupOrderBy()
    {
        $this->compiled = false;
        $this->withinGroupOrderBy = array();
        return $this;
    }

    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @return QueryBuilder
     */
    public function orderBy($field, $direction = 'ASC')
    {
        $this->compiled = false;
        $this->resetOrderBy();
        return $this->addOrderBy($field, $direction);
    }

    /**
     * @return QueryBuilder
     */
    public function addOrderBy($field, $direction = 'ASC')
    {
        $this->compiled = false;
        $direction = strtoupper($direction) == 'ASC' ? 'ASC' : 'DESC';
        $this->orderBy[] = array($field, $direction);
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function resetOrderBy()
    {
        $this->compiled = false;
        $this->orderBy = array();
        return $this;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return QueryBuilder
     */
    public function setOffset($offset)
    {
        $this->compiled = false;
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function resetOffset()
    {
        $this->compiled = false;
        $this->offset = 0;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return QueryBuilder
     */
    public function setLimit($limit)
    {
        $this->compiled = false;
        $this->limit = $limit;

        return $this;
    }


    /**
     * @return QueryBuilder
     */
    public function resetLimit()
    {
        $this->compiled = false;
        $this->limit = null;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    public function getOption($name)
    {
        if (!$this->hasOption($name)) {
            throw new \RuntimeException(sprintf('Option %s is not set', $name));
        }

        return $this->options[$name];
    }

    /**
     * @return QueryBuilder
     */
    public function setOptions($options)
    {
        if (!is_array($options)) {
            throw new \LogicException('Options should be key-value array');
        }
        $this->compiled = false;
        $this->options = $options;

        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function setOption($name, $value)
    {
        $this->compiled = false;
        $this->options[$name] = $value;
        return $this;
    }


    /**
     * @return QueryBuilder
     */
    public function resetOptions()
    {
        $this->compiled = false;
        $this->options = array();
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function reset()
    {
        $this->compiled = false;
        return $this
            ->resetSelect()
            ->resetFrom()
            ->resetWhere()
            ->resetGroupBy()
            ->resetWithinGroupOrderBy()
            ->resetOrderBy()
            ->resetOffset()
            ->resetLimit()
            ->resetOptions()
        ;
    }

    /**
     * @return QueryBuilder
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function hasParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    public function getParameter($name)
    {
        if (!$this->hasParameter($name)) {
            throw new \RuntimeException(sprintf('Parameter %s is not set', $name));
        }

        return $this->parameters[$name];
    }

    /**
     * @return QueryBuilder
     */
    public function setParameters($parameters)
    {
        if (!is_array($parameters)) {
            throw new \LogicException('Parameters should be key-value array');
        }
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function addParameters($params)
    {
        $this->setParameters(array_merge($this->parameters, $params));
        return $this;
    }

    public function escape($string)
    {
        return $this->connection->escape($string);
    }

    public function escapeMatch($string)
    {
        return $this->connection->escapeMatch($string);
    }

    protected function compileWhere()
    {
        $parts = [];
        if (!empty($this->match)) {
            $parts[] = 'MATCH(\'' . implode(' & ', $this->match) . '\')';
        }

        foreach ($this->where as $part) {
            $parts[] = $part;
        }
        if (empty($parts)) {
            return '';
        }
        return 'WHERE ' . implode(' AND ', $parts) . ' ';
    }

    public function getSQL()
    {
        if ($this->compiled) {
            return $this->sql;
        }
        $this->compiled = true;

        $sql = 'SELECT ';

        $sql .= empty($this->select) ? '*' : implode(', ', $this->select) . ' ';
        $sql .= 'FROM ' . implode(', ', $this->from) . ' ';
        $sql .= $this->compileWhere();
        if (!empty($this->groupBy)) {
            $sql .= 'GROUP BY ' . implode(', ', $this->groupBy) . ' ';
        }
        if (!empty($this->withinGroupOrderBy)) {
            $sql .= 'WITHIN GROUP ORDER BY ' . implode(', ', array_map(function ($elem) {
                return $elem[0] . ' ' . $elem[1];
            }, $this->withinGroupOrderBy)) . ' ';
        }

        if (!empty($this->orderBy)) {
            $sql .= 'ORDER BY ' . implode(', ', array_map(function ($elem) {
                return $elem[0] . ' ' . $elem[1];
            }, $this->orderBy)) . ' ';
        }

        if (null !== $this->limit) {
            $sql .= 'LIMIT ' . ((int)$this->offset) . ', ' . ((int)$this->limit) . ' ';
        }

        if (!empty($this->options)) {
            $sql .= 'OPTION ' .  implode(', ', $this->buildOptionPairs($this->options));
        }

        $this->sql = $sql;
        return $this->sql;
    }

    private function buildOptionPairs(array $data)
    {
        $pairs = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = '(' . implode(', ', $this->buildOptionPairs($value)) .  ')';
            }
            $pairs[] = $key . ' = ' . $value;
        }
        return $pairs;
    }

    public function getQuery()
    {
        return new Query($this->connection, $this->getSQL(), $this->parameters);
    }
}
