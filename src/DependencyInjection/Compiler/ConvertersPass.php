<?php

namespace Aymdev\CommonmarkBundle\DependencyInjection\Compiler;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @internal
 */
class ConvertersPass implements CompilerPassInterface
{
    public const PARAMETER_CONVERTERS = 'aymdev_commonmark.converters';
    /** @var Reference[] converter service IDs */
    private $converters = [];

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
        if ($converterConfig['type'] === 'empty') {
            $environment = new Definition(Environment::class);
        } else {
            $environment = new ChildDefinition('aymdev_commonmark.environment');
        }

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
            ->setPublic(true)
        ;

        // Current service ID
        $container->setDefinition($converterConfig['name'], $converterDefinition);
        $container->registerAliasForArgument($converterConfig['name'], CommonMarkConverter::class, $converterConfig['name']);

        // Deprecated service ID
        $deprecatedConverterDefinition = clone $converterDefinition;

        $deprecationMessage = 'Using the %service_id% service ID is deprecated and will be removed in v2. ';
        $deprecationMessage .= 'You should use the converter name instead.';

        // Symfony <5.1
        if (Kernel::MAJOR_VERSION <= 4 || (Kernel::MAJOR_VERSION === 5 && Kernel::MINOR_VERSION === 0)) {
            $deprecatedConverterDefinition->setDeprecated(true, $deprecationMessage);
        } else {
            // Symfony >= 5.1
            $deprecatedConverterDefinition->setDeprecated('aymdev/commonmark-bundle', '1.3.0', $deprecationMessage);
        }

        $converterId = 'aymdev_commonmark.converter.' . $converterConfig['name'];
        $container->setDefinition($converterId, $deprecatedConverterDefinition);

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