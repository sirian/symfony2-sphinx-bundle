<?php

namespace Sirian\SphinxBundle\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class QueryLogger
{
    protected $logger;
    protected $stopwatch;
    protected $queries = [];
    protected $currentQuery = 0;
    protected $start = 0;

    public function __construct(LoggerInterface $logger = null, Stopwatch $stopwatch = null)
    {
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }

    public function startQuery($sql, $params, $realSql)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('sphinx', 'sphinx');
        }

        $this->log($sql, $params);

        $this->start = microtime(true);
        $this->queries[$this->currentQuery] = array('sql' => $sql, 'params' => $params, 'time' => 0, 'realSql' => $realSql);
    }

    public function stopQuery()
    {
        if ($this->stopwatch) {
            $this->stopwatch->stop('sphinx', 'sphinx');
        }

        $this->queries[$this->currentQuery]['time'] = microtime(true) - $this->start;
        $this->currentQuery++;
    }

    protected function log($message, array $params)
    {
        if (null !== $this->logger) {
            $this->logger->debug($message, $params);
        }
    }

    public function getQueries()
    {
        return $this->queries;
    }
}
