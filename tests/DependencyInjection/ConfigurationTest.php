<?php

namespace Tests\AymDev\CommonMarkBundle\DependencyInjection;

use Aymdev\CommonmarkBundle\DependencyInjection\AymdevCommonmarkExtension;
use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

class ConfigurationTest extends TestCase
{
    /**
     * Try multiple configuration files to validate desired format
     * @param mixed[] $config
     * @dataProvider provideConfiguration
     */
    public function testConfigurationFormat(array $config): void
    {
        $container = new ContainerBuilder();
        $extension = new AymdevCommonmarkExtension();

        $extension->load($config, $container);

        /** @var converterConfig[] $converters */
        $converters = $container->getParameter(ConvertersPass::PARAMETER_CONVERTERS);

        foreach ($converters as $converter) {
            // Validate converter type
            self::assertContains($converter['type'], ['commonmark', 'github']);

            // Checks for converter options
            if (isset($converter['options'])) {
                self::assertIsArray($converter['options']);
            }

            // Checks for converter extensions
            self::assertIsArray($converter['extensions']);
        }
    }

    /**
     * @return \Generator<mixed[][]>
     */
    public function provideConfiguration(): \Generator
    {
        /** @var string[] $configFiles */
        $configFiles = glob(__DIR__ . '/../Fixtures/config/configuration_*.yaml');

        foreach ($configFiles as $file) {
            /** @var string $content */
            $content = file_get_contents($file);
            /** @var mixed[] $result */
            $result = Yaml::parse($content);
            yield [$result];
        }
    }

    public function testConverterOptionsAreWellParsed(): void
    {
        /** @var string $content */
        $content = file_get_contents(__DIR__ . '/../Fixtures/config/configuration_options.yaml');
        /** @var array{aymdev_commonmark: array{converters: converterConfig[]}} $config */
        $config = Yaml::parse($content);

        $container = new ContainerBuilder();
        $extension = new AymdevCommonmarkExtension();

        $extension->load($config, $container);
        /** @var converterConfig[] $converter */
        $converter = $container->getParameter(ConvertersPass::PARAMETER_CONVERTERS);

        $expectedOptions = $config['aymdev_commonmark']['converters']['my_converter']['options'];
        self::assertSame($expectedOptions, $converter['my_converter']['options']);
    }
}
