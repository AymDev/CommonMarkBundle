<?php

namespace Aymdev\CommonmarkBundle\DependencyInjection;

use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use Aymdev\CommonmarkBundle\Twig\CommonMarkExtension;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Environment;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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
        // Environment
        $container
            ->setDefinition('aymdev_commonmark.environment', new Definition(ConfigurableEnvironmentInterface::class))
            ->setFactory([Environment::class, 'createCommonMarkEnvironment'])
            ->setPublic(false)
        ;

        // Converters
        $container
            ->setDefinition('aymdev_commonmark.converter.type.commonmark', new Definition(CommonMarkConverter::class))
            ->setPublic(false)
        ;

        $container
            ->setDefinition('aymdev_commonmark.converter.type.github', new Definition(GithubFlavoredMarkdownConverter::class))
            ->setPublic(false)
        ;

        $container
            ->setAlias('aymdev_commonmark.converter.type.empty', 'aymdev_commonmark.converter.type.commonmark')
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