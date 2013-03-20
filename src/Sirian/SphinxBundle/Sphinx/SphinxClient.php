<?php

namespace Sirian\SphinxBundle\Sphinx;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SphinxClient extends \SphinxClient implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function execQueries()
    {
        $results = parent::RunQueries();
        $this->_reqs = array ();

        if (!is_array($results)) {
            throw new SphinxException();
        }


        $this->_error = $results[0]["error"];
        $this->_warning = $results[0]["warning"];

        if ($results[0]["status"] == SEARCHD_ERROR) {
            throw new SphinxException($this->GetLastError());
        } else {
            return $results[0];
        }
    }

    public function query($query, $index = '*', $comment = '')
    {
        if ($this->container->has('debug.stopwatch')) {
            $stopwatch = $this->container->get('debug.stopwatch');
            $stopwatch->start('sphinx');
        }

        $res = parent::query($query, $index, $comment);

        if ($this->container->has('debug.stopwatch')) {
            $stopwatch = $this->container->get('debug.stopwatch');
            $stopwatch->stop('sphinx');
        }

        if (!is_array($res)) {
            throw new SphinxException($this->GetLastError());
        }
        return $res;
    }
}
