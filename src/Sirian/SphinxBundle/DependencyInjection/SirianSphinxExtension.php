<?php

namespace Sirian\SphinxBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class SirianSphinxExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (empty($config['default_connection'])) {
            $keys = array_keys($config['connections']);
            $config['default_connection'] = reset($keys);
        }

        $connections = [];
        foreach ($config['connections'] as $name => $connectionOptions) {
            $connections[$name] = sprintf('sirian.sphinx.%s_connection', $name);

            $connectionDefinition = new DefinitionDecorator('sirian.sphinx.connection');
            $connectionDefinition->setArguments([$connectionOptions]);
            $container->setDefinition($connections[$name], $connectionDefinition);
        }

        $container->setParameter('sirian.sphinx.default_connection', $config['default_connection']);
        $container->setParameter('sirian.sphinx.connections', $connections);
    }
}
