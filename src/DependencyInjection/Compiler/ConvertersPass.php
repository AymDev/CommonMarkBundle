<?php

namespace Aymdev\CommonmarkBundle\DependencyInjection\Compiler;

use League\CommonMark\MarkdownConverter;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
class ConvertersPass implements CompilerPassInterface
{
    public const PARAMETER_CONVERTERS = 'aymdev_commonmark.converters';
    /** @var Reference[] converter service IDs */
    private array $converters = [];

    public function process(ContainerBuilder $container)
    {
        $converters = $container->getParameter(self::PARAMETER_CONVERTERS);

        foreach ($converters as $name => $config) {
            $config['name'] = $name;
            $converters[$name] = $config;
            $this->registerConverters($config, $container);
        }

        $container->setParameter(self::PARAMETER_CONVERTERS, $converters);
        $this->setupTwigExtension($container);
    }

    private function registerConverters(array $converterConfig, ContainerBuilder $container): array
    {
        // Create environment definition
        $environment = new ChildDefinition('aymdev_commonmark.environment.' . $converterConfig['type']);
        $environment->addArgument($converterConfig['options'] ?? []);

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
        $converterDefinition = new ChildDefinition('aymdev_commonmark.converter');
        $converterDefinition
            ->addArgument(new Reference($environmentId))
            ->setPublic(true)
        ;

        // Current service ID
        $container->setDefinition($converterConfig['name'], $converterDefinition);
        $container->registerAliasForArgument(
            $converterConfig['name'],
            MarkdownConverter::class,
            $converterConfig['name']
        );

        // Save converter for later twig extension arguments setup
        $this->converters[$converterConfig['name']] = new Reference($converterConfig['name']);

        return $converterConfig;
    }

    private function setupTwigExtension(ContainerBuilder $container): void
    {
        $twigExtensionDefinition = $container->getDefinition('aymdev_commonmark.twig_extension');
        $twigExtensionDefinition->setArgument(0, ServiceLocatorTagPass::register($container, $this->converters));
    }
}
