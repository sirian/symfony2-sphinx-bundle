<?php

namespace Sirian\SphinxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('sirian_sphinx');

        $rootNode
            ->append($this->getConnectionsNode())
            ->children()
                ->scalarNode('default_connection')->end()
        ;

        return $treeBuilder;
    }

    protected function getConnectionsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('connections');
        $node
            ->requiresAtLeastOneElement()
            ->addDefaultChildrenIfNoneSet('default_connection')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
                ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                ->scalarNode('dbname')->defaultValue('default')->end()
                ->integerNode('port')->defaultValue(9306)->end()
            ->end()
        ;


        return $node;
    }
}
