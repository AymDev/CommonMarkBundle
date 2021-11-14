<?php

namespace Tests\AymDev\CommonMarkBundle\DependencyInjection;

use Aymdev\CommonmarkBundle\DependencyInjection\AymdevCommonMarkExtension;
use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use League\CommonMark\Environment;
use League\CommonMark\MarkdownConverter;
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

        // "empty" Environment
        $emptyEnvironment = $container->get('aymdev_commonmark.environment.empty');
        self::assertInstanceOf(Environment::class, $emptyEnvironment);
        self::assertCount(0, $emptyEnvironment->getExtensions());

        // CommonMark Environment
        $commonMarkEnvironment = $container->get('aymdev_commonmark.environment.commonmark');
        self::assertInstanceOf(Environment::class, $commonMarkEnvironment);
        self::assertNotCount(0, $commonMarkEnvironment->getExtensions());

        // GitHub Environment
        $githubEnvironment = $container->get('aymdev_commonmark.environment.github');
        self::assertInstanceOf(Environment::class, $githubEnvironment);
        self::assertNotCount(0, $githubEnvironment->getExtensions());

        // converter
        $converterDefinition = $container->getDefinition('aymdev_commonmark.converter');
        self::assertSame(MarkdownConverter::class, $converterDefinition->getClass());
    }
}