<?php

namespace Aymdev\CommonmarkBundle\DependencyInjection;

use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use Aymdev\CommonmarkBundle\Twig\CommonMarkExtension;
use League\CommonMark\Environment;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @internal
 */
class AymdevCommonmarkExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->createBundleServiceDefinitions($container);

        // Save config in container
        // Validates in compiler pass
        $container->setParameter(ConvertersPass::PARAMETER_CONVERTERS, $config['converters']);
    }

    /**
     * Create service definitions for the bundle
     */
    private function createBundleServiceDefinitions(ContainerBuilder $container): void
    {
        // Environments
        $container
            ->setDefinition('aymdev_commonmark.environment.empty', new Definition(Environment::class))
            ->setPublic(false)
        ;
        $container
            ->setDefinition('aymdev_commonmark.environment.commonmark', new Definition(Environment::class))
            ->setFactory([Environment::class, 'createCommonMarkEnvironment'])
            ->setPublic(false)
        ;

        $container
            ->setDefinition('aymdev_commonmark.environment.github', new Definition(Environment::class))
            ->setFactory([Environment::class, 'createGFMEnvironment'])
            ->setPublic(false)
        ;

        // Converter
        $container
            ->setDefinition('aymdev_commonmark.converter', new Definition(MarkdownConverter::class))
            ->setPublic(false)
        ;

        // Twig extension
        $container
            ->setDefinition('aymdev_commonmark.twig_extension', new Definition(CommonMarkExtension::class))
            ->addTag('twig.extension')
            ->setPublic(false)
        ;
    }
}