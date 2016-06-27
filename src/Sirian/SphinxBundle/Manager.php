<?php

namespace Sirian\SphinxBundle;

use Sirian\SphinxBundle\SphinxQL\Connection;
use Sirian\SphinxBundle\SphinxQL\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Manager
{
    use ContainerAwareTrait;

    protected $connections = array();
    protected $defaultConnection = null;

    public function __construct($connections = array(), $defaultConnection)
    {
        $this->connections = $connections;
        $this->defaultConnection = $defaultConnection;
    }

    public function createQueryBuilder($connectionName = null)
    {
        $connection = $this->getConnection($connectionName);
        return new QueryBuilder($connection);
    }

    /**
     * @param $name
     * @return Connection
     * @throws \RuntimeException
     */
    public function getConnection($name = null)
    {
        if (null === $name) {
            $name = $this->defaultConnection;
        }

        if (!isset($this->connections[$name])) {
            throw new \RuntimeException(sprintf('Sphinx connection named "%s" not found', $name));
        }

        return $this->container->get($this->connections[$name]);
    }
}
