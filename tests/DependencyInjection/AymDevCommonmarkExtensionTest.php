<?php

namespace Tests\AymDev\CommonMarkBundle\DependencyInjection;

use Aymdev\CommonmarkBundle\DependencyInjection\AymdevCommonmarkExtension;
use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AymDevCommonmarkExtensionTest extends TestCase
{
    /**
     * Asserts that the converters configuration is saved as a container parameter
     */
    public function testDefaultConfiguration(): void
    {
        $container = new ContainerBuilder();
        $extension = new AymdevCommonmarkExtension();

        $extension->load([], $container);
        self::assertNotNull($container->getParameter(ConvertersPass::PARAMETER_CONVERTERS));
    }

    /**
     * Asserts the default services are correctly defined
     */
    public function testDefaultServicesRegistration(): void
    {
        $container = new ContainerBuilder();
        $extension = new AymdevCommonmarkExtension();

        $extension->load([], $container);

        // "empty" Environment
        /** @var Environment $emptyEnvironment */
        $emptyEnvironment = $container->get('aymdev_commonmark.environment.empty');
        self::assertInstanceOf(Environment::class, $emptyEnvironment);
        self::assertCount(0, $emptyEnvironment->getExtensions());

        // CommonMark Environment
        /** @var Environment $commonMarkEnvironment */
        $commonMarkEnvironment = $container->get('aymdev_commonmark.environment.commonmark');
        self::assertInstanceOf(Environment::class, $commonMarkEnvironment);
        self::assertNotCount(0, $commonMarkEnvironment->getExtensions());

        // GitHub Environment
        /** @var Environment $githubEnvironment */
        $githubEnvironment = $container->get('aymdev_commonmark.environment.github');
        self::assertInstanceOf(Environment::class, $githubEnvironment);
        self::assertNotCount(0, $githubEnvironment->getExtensions());

        // converter
        $converterDefinition = $container->getDefinition('aymdev_commonmark.converter');
        self::assertSame(MarkdownConverter::class, $converterDefinition->getClass());
    }
}
