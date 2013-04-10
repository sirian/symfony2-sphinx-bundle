<?php

namespace Sirian\SphinxBundle\Profiler;

use Sirian\SphinxBundle\Logger\QueryLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SphinxDataCollector extends DataCollector
{
    protected $logger;

    public function __construct(QueryLogger $logger)
    {
        $this->logger = $logger;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [
            'queries' => $this->logger->getQueries()
        ];
    }

    public function getQueries()
    {
        return $this->data['queries'];
    }

    public function getTime()
    {
        $time = 0;
        foreach ($this->data['queries'] as $query) {
            $time += $query['time'];
        }

        return $time;
    }

    public function getQueryCount()
    {
        return count($this->data['queries']);
    }

    public function getName()
    {
        return 'sphinx';
    }
}
