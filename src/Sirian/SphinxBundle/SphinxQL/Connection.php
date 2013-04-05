<?php

namespace Sirian\SphinxBundle\SphinxQL;

use Sirian\SphinxBundle\Sphinx\SphinxException;
use Symfony\Component\Stopwatch\Stopwatch;

class Connection extends \Mysqli
{
    /**
     * @var Stopwatch
     */
    protected $stopWatch;

    public function __construct($options)
    {
        parent::__construct($options['host'], '', '', '', $options['port']);
    }

    public function setStopWatch($stopWatch)
    {
        $this->stopWatch = $stopWatch;
    }

    protected function constructPdoDsn($params)
    {
        $dsn = 'mysql:';
        if (isset($params['host']) && $params['host'] != '') {
            $dsn .= 'host=' . $params['host'] . ';';
        }
        if (isset($params['port'])) {
            $dsn .= 'port=' . $params['port'] . ';';
        }
        if (isset($params['dbname'])) {
            $dsn .= 'dbname=' . $params['dbname'] . ';';
        }
        if (isset($params['unix_socket'])) {
            $dsn .= 'unix_socket=' . $params['unix_socket'] . ';';
        }
        if (isset($params['charset'])) {
            $dsn .= 'charset=' . $params['charset'] . ';';
        }

        return $dsn;
    }

    public function escape($string)
    {
        return $this->real_escape_string($string);
    }

    public function escapeMatch($string)
    {
        $from = array('\\', '(', ')', '|', '-', '!', '@', '~', '"', '&', '/', '^', '$', '=');
        $to = array('\\\\', '\(', '\)', '\|', '\-', '\!', '\@', '\~', '\"', '\&', '\/', '\^', '\$', '\=');

        return str_replace($from, $to, $string);
    }


    public function query ($query, $mode = MYSQLI_STORE_RESULT)
    {
        if ($this->stopWatch) {
            $this->stopWatch->start('sphinx');
        }

        $res = parent::query($query, $mode);
        if ($this->stopWatch) {
            $this->stopWatch->stop('sphinx');
        }
        if (!$res) {
            throw new SphinxException($this->error);
        }
        return $res;
    }

    public function execute($sql, $params = array())
    {
        $sql = preg_replace_callback('/(?P<type>[ifsem]?):(?P<param>[_a-zA-Z0-9]+)/', function ($matches) use ($params) {
            $val = $params[$matches['param']];
            $type = 's';

            if (!empty($matches['type'])) {
                $type = $matches['type'];
            }
            if (!is_array($val)) {
                $val = array($val);
            }

            $parts = array();
            foreach ($val as $row) {
                switch ($type) {
                    case 'e': //expression
                        $parts[] = $row;
                        break;
                    case 'f': //float
                        $parts[] = sprintf('%f', $row);
                        break;
                    case 'i': //integer
                        $parts[] = (int)$row;
                        break;
                    case 'm': //sphinx match
                        $parts[] = $this->escape($this->escapeMatch($row)) ;
                        break;
                    default:
                        $parts[] = "'" . $this->escape($row) . "'";
                }
            }
            return implode(',', $parts);
        }, $sql);

        return $this->query($sql);
    }
}
