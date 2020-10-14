<?php

namespace Aymdev\CommonmarkBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConvertersPass implements CompilerPassInterface
{
    public const PARAMETER_CONVERTERS = 'aymdev_commonmark.converters';

    public function process(ContainerBuilder $container)
    {
        $converters = $container->getParameter(self::PARAMETER_CONVERTERS);

        foreach ($converters as $name => $config) {
            $config['name'] = $name;
            $converters[$name] = $config;
            $this->registerConverters($config, $container);
        }

        $container->setParameter(self::PARAMETER_CONVERTERS, $converters);
    }

    private function registerConverters(array $converterConfig, ContainerBuilder $container): array
    {
        // Create environment definition
        $environment = new ChildDefinition('aymdev_commonmark.environment');

        // Register and add extensions
        foreach ($converterConfig['extensions'] as $extensionName) {
            if (false === $container->has($extensionName)) {
                $definition = new Definition($extensionName);
                $container->setDefinition($extensionName, $definition);
            }

            $environment->addMethodCall('addExtension', [new Reference($extensionName)]);
        }

        $environmentId = 'aymdev_commonmark.environment.' . $converterConfig['name'];
        $container->setDefinition($environmentId, $environment);

        // Create converter definition
        $converterDefinition = new ChildDefinition('aymdev_commonmark.converter.type.' . $converterConfig['type']);
        $converterDefinition
            ->addArgument($converterConfig['options'] ?? [])
            ->addArgument(new Reference($environmentId))
        ;

        $container->setDefinition('aymdev_commonmark.converter.' . $converterConfig['name'], $converterDefinition);

        return $converterConfig;
    }
}