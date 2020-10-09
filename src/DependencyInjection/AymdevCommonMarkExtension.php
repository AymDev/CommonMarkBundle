<?php

namespace Aymdev\CommonmarkBundle\DependencyInjection;

use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AymdevCommonMarkExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // Save config in container
        // Validates in compiler pass
        $container->setParameter(ConvertersPass::PARAMETER_CONVERTERS, $config['converters']);
    }
}