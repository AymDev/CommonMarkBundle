<?php

namespace Tests\AymDev\CommonMarkBundle\Twig;

use Aymdev\CommonmarkBundle\Twig\CommonMarkExtension;
use League\CommonMark\MarkdownConverter;
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
                $converterMockA->method('convertToHtml')->willReturn('a');
                return $converterMockA;
            },
            'b_conv' => function() {
                $converterMockB = $this->getMockBuilder(MarkdownConverter::class)
                    ->disableOriginalConstructor()
                    ->getMock();
                $converterMockB->method('convertToHtml')->willReturn('b');
                return $converterMockB;
            },
        ]);

        $extension = new CommonMarkExtension($serviceLocator);

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
                $converterMock->expects(self::once())->method('convertToHtml')->willReturnArgument(0);
                return $converterMock;
            },
        ]);
        $extension = new CommonMarkExtension($serviceLocator);

        $expectedOutput = 'some markdown content';
        $actualOutput = $extension->convertMarkdown($expectedOutput);

        self::assertSame($expectedOutput, $actualOutput);
    }

    /**
     * An exception is thrown if the provided converter name is wrong
     */
    public function testFilterWithInvalidConverterName()
    {
        $serviceLocator = new ServiceLocator([
            'my_converter' => function() {
                return $this->getMockBuilder(MarkdownConverter::class)
                    ->disableOriginalConstructor()
                    ->getMock();
            },
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
            'my_converter' => function() {
                return $this->getMockBuilder(MarkdownConverter::class)
                    ->disableOriginalConstructor()
                    ->getMock();
            },
        ]);
        $extension = new CommonMarkExtension($serviceLocator);

        self::assertNull($extension->convertMarkdown(null, 'my_converter'));
    }
}