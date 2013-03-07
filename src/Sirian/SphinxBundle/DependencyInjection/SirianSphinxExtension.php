<?php

namespace Sirian\SphinxBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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

        $sphinx = $container->getDefinition('sirian.sphinx.client');
        $searchConfig = $config['searchd'];
        if ($searchConfig['socket']) {
            $sphinx->addMethodCall('setServer', [$searchConfig['socket']]);
        } else {
            $sphinx->addMethodCall('setServer', [$searchConfig['host'], $searchConfig['port']]);
        }
    }
}
