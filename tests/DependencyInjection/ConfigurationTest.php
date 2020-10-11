<?php

namespace Tests\AymDev\CommonMarkBundle\DependencyInjection;

use Aymdev\CommonmarkBundle\DependencyInjection\AymdevCommonMarkExtension;
use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

class ConfigurationTest extends TestCase
{
    /**
     * Try multiple configuration files to validate desired format
     * @dataProvider provideConfiguration
     */
    public function testConfigurationFormat($config)
    {
        $container = new ContainerBuilder();
        $extension = new AymdevCommonMarkExtension();

        self::assertNull($extension->load($config, $container));
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

    public function provideConfiguration(): \Generator
    {
        $configFiles = glob(__DIR__ . '/../Fixtures/config/configuration_*.yaml');

        foreach ($configFiles as $file) {
            yield [Yaml::parse(file_get_contents($file))];
        }
    }
}