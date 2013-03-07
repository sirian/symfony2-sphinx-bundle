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

    public function query($query, $index = '*', $comment = '')
    {
        $res = parent::query($query, $index, $comment);
        if (false === $res) {
            throw new SphinxException($this->GetLastError());
        }
        return $res;
    }
}
