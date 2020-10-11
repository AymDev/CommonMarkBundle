<?php

namespace Tests\AymDev\CommonMarkBundle\DependencyInjection;

use Aymdev\CommonmarkBundle\DependencyInjection\AymdevCommonMarkExtension;
use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AymDevCommonmarkExtensionTest extends TestCase
{
    /**
     * Asserts that the converters configuration is saved as a container parameter
     */
    public function testDefaultConfiguration()
    {
        $container = new ContainerBuilder();
        $extension = new AymdevCommonMarkExtension();

        $extension->load([], $container);
        self::assertNotNull($container->getParameter(ConvertersPass::PARAMETER_CONVERTERS));
    }

    /**
     * Asserts the default services are correctly defined
     */
    public function testDefaultServicesRegistration()
    {
        $container = new ContainerBuilder();
        $extension = new AymdevCommonMarkExtension();

        $extension->load([], $container);

        // CommonMark Environment
        $environment = $container->get('aymdev_commonmark.environment');
        self::assertInstanceOf(ConfigurableEnvironmentInterface::class, $environment);

        // CommonMark base converter
        $commonmarkConverter = $container->get('aymdev_commonmark.converter.type.commonmark');
        self::assertInstanceOf(CommonMarkConverter::class, $commonmarkConverter);
        
        // CommonMark GitHub converter
        $githubConverter = $container->get('aymdev_commonmark.converter.type.github');
        self::assertInstanceOf(GithubFlavoredMarkdownConverter::class, $githubConverter);
    }
}