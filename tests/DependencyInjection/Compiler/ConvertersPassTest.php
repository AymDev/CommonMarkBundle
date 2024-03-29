<?php

namespace Tests\Aymdev\CommonmarkBundle\DependencyInjection\Compiler;

use Aymdev\CommonmarkBundle\AymdevCommonmarkBundle;
use Aymdev\CommonmarkBundle\DependencyInjection\AymdevCommonmarkExtension;
use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use Aymdev\CommonmarkBundle\Twig\CommonMarkExtension;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Twig\Environment;

class ConvertersPassTest extends TestCase
{
    protected function tearDown(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(AymdevCommonmarkTestKernel::KERNEL_CACHE_DIR);
        parent::tearDown();
    }

    public function testContainerServicesRegistration(): void
    {
        $convertersPass = new ConvertersPass();
        $container = new ContainerBuilder();
        $extension = new AymdevCommonmarkExtension();

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

        /** @var converterConfig[] $converters */
        $converters = $container->getParameter('aymdev_commonmark.converters');

        foreach ($converters as $converter) {
            $environmentId = 'aymdev_commonmark.environment.' . $converter['name'];
            self::assertTrue($container->has($environmentId));

            foreach ($converter['extensions'] as $commonmarkExtension) {
                self::assertTrue($container->has($commonmarkExtension));
            }

            // Current service ID
            self::assertTrue($container->has($converter['name']));

            $alias = MarkdownConverter::class . ' $';
            $alias .= lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $converter['name']))));
            self::assertTrue($container->hasAlias($alias));
        }
    }

    public function testTwigExtensionRegistration(): void
    {
        $kernel = new AymdevCommonmarkTestKernel([
            'converters' => [
                'my_converter' => [],
            ]
        ]);
        $kernel->boot();

        /** @var ContainerInterface $container */
        $container = $kernel->getContainer()->get('test.service_container');

        /** @var Environment $twig */
        $twig = $container->get('twig');

        // Twig extension is registered
        $extension = $twig->getExtension(CommonMarkExtension::class);
        self::assertInstanceOf(CommonMarkExtension::class, $extension);

        // Twig filter works correctly
        $markdown = '# test';
        $template = "{{ markdown|commonmark('my_converter') }}";
        $expectedOutput = '<h1>test</h1>';

        $html = $twig->createTemplate($template)
            ->render(['markdown' => $markdown]);
        $html = trim($html);
        self::assertSame($expectedOutput, $html);
    }

    /**
     * Test empty environment
     * The InlinesOnlyExtension needs to be added to an empty environment
     * It must not be combined with the CommonMarkCoreExtension
     */
    public function testEmptyEnvironmentSetup(): void
    {
        $kernel = new AymdevCommonmarkTestKernel([
            'converters' => [
                'my_converter' => [
                    'type' => 'empty',
                    'extensions' => [
                        InlinesOnlyExtension::class,
                    ]
                ],
            ]
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        /** @var MarkdownConverter $converter */
        $converter = $container->get('my_converter');

        // converting works correctly
        self::assertSame('# test', trim($converter->convertToHtml('# test')));
    }
}

class AymdevCommonmarkTestKernel extends Kernel
{
    public const KERNEL_CACHE_DIR = __DIR__ . '/../../cache';

    /** @var mixed[] */
    private array $aymdevCommonmarkConfig;

    /**
     * @param mixed[] $aymdevCommonmarkConfig
     */
    public function __construct(array $aymdevCommonmarkConfig = [])
    {
        $this->aymdevCommonmarkConfig = $aymdevCommonmarkConfig;
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new AymdevCommonmarkBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('aymdev_commonmark', $this->aymdevCommonmarkConfig);

            // Parameter is undefined but required
            $container->setParameter('kernel.secret', '$ecret');

            // Makes the test.service_container service available
            $container->prependExtensionConfig('framework', ['test' => true]);
        });
    }

    public function getCacheDir(): string
    {
        return self::KERNEL_CACHE_DIR . '/' . spl_object_hash($this);
    }

    public function getLogDir(): string
    {
        return $this->getCacheDir();
    }
}
