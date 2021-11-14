<?php

namespace Tests\AymDev\CommonMarkBundle\Twig;

use Aymdev\CommonmarkBundle\Twig\CommonMarkExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

class CommonMarkExtensionTest extends TestCase
{
    public function testFilterBasicUsage()
    {
        $serviceLocator = new ServiceLocator([
            'a_conv' => function() {
                $converterMockA = $this->getMockBuilder(MarkdownConverter::class)
                    ->disableOriginalConstructor()
                    ->getMock();
                $converterMockA->method('convertToHtml')
                    ->willReturn(new RenderedContent(new Document(), 'a'));
                return $converterMockA;
            },
            'b_conv' => function() {
                $converterMockB = $this->getMockBuilder(MarkdownConverter::class)
                    ->disableOriginalConstructor()
                    ->getMock();
                $converterMockB->method('convertToHtml')
                    ->willReturn(new RenderedContent(new Document(), 'b'));
                return $converterMockB;
            },
        ]);

        $extension = new CommonMarkExtension($serviceLocator);

        $output = $extension->convertMarkdown('', 'a_conv');
        self::assertSame('a', $output);

        $output = $extension->convertMarkdown('', 'b_conv');
        self::assertSame('b', $output);
    }

    /**
     * The twig filter must work without the converter name argument if there is only one converter
     */
    public function testFilterConverterNameIsOptional()
    {
        $serviceLocator = new ServiceLocator([
            'unique' => function() {
                $converterMock = $this->getMockBuilder(MarkdownConverter::class)
                    ->disableOriginalConstructor()
                    ->getMock();
                $converterMock->expects(self::once())->method('convertToHtml')
                    ->willReturn(new RenderedContent(new Document(), 'some markdown content'));
                return $converterMock;
            },
        ]);
        $extension = new CommonMarkExtension($serviceLocator);

        $actualOutput = $extension->convertMarkdown('');
        self::assertSame('some markdown content', $actualOutput);
    }

    /**
     * An exception is thrown if the provided converter name is wrong
     */
    public function testFilterWithInvalidConverterName()
    {
        $serviceLocator = new ServiceLocator([
            'my_converter' => fn() => $this->getMockBuilder(MarkdownConverter::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ]);
        $extension = new CommonMarkExtension($serviceLocator);

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessageMatches('~The ".+" converter does not exists. Did you mean one of these ?.+~');

        $extension->convertMarkdown('', 'wrong_converter');
    }

    /**
     * Nothing is converted if the markdown content is null
     */
    public function testFilterWithNullContent()
    {
        $serviceLocator = new ServiceLocator([
            'my_converter' => fn() => $this->getMockBuilder(MarkdownConverter::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ]);
        $extension = new CommonMarkExtension($serviceLocator);

        self::assertNull($extension->convertMarkdown(null, 'my_converter'));
    }
}