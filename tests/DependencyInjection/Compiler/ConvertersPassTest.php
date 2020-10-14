<?php

namespace Tests\AymDev\CommonMarkBundle\DependencyInjection\Compiler;

use Aymdev\CommonmarkBundle\DependencyInjection\AymdevCommonMarkExtension;
use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConvertersPassTest extends TestCase
{
    public function testContainerServicesRegistration()
    {
        $convertersPass = new ConvertersPass();
        $container = new ContainerBuilder();
        $extension = new AymdevCommonMarkExtension();

        $converters = [
            'converters' => [
                'cm_converter' => [],
                'gh_converter' => [
                    'type' => 'github',
                    'options' => [
                        'renderer' => [
                            'soft_break' => "\n",
                        ],
                        'use_underscore' => false,
                        'table_of_contents' => [
                            'position' => 'top',
                            'style' => 'bullet',
                        ],
                    ],
                    'extensions' => [
                        'League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension',
                        'League\CommonMark\Extension\TableOfContents\TableOfContentsExtension',
                    ],
                ],
            ],
        ];
        $extension->load([$converters], $container);
        $convertersPass->process($container);

        self::assertTrue($container->hasParameter('aymdev_commonmark.converters'));

        foreach ($container->getParameter('aymdev_commonmark.converters') as $converter) {
            $environmentId = 'aymdev_commonmark.environment.' . $converter['name'];
            self::assertTrue($container->has($environmentId));

            foreach ($converter['extensions'] as $extension) {
                self::assertTrue($container->has($extension));
            }

            $converterId = 'aymdev_commonmark.converter.' . $converter['name'];
            self::assertTrue($container->has($converterId));
        }
    }
}