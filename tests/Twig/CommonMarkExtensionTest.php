<?php

namespace Tests\AymDev\CommonMarkBundle\Twig;

use Aymdev\CommonmarkBundle\Twig\CommonMarkExtension;
use League\CommonMark\CommonMarkConverter;
use PHPUnit\Framework\TestCase;

class CommonMarkExtensionTest extends TestCase
{
    public function testFilterBasicUsage()
    {
        $converterMockA = $this->getMockBuilder(CommonMarkConverter::class)->getMock();
        $converterMockA->method('convertToHtml')->willReturn('a');
        $converterMockB = $this->getMockBuilder(CommonMarkConverter::class)->getMock();
        $converterMockB->method('convertToHtml')->willReturn('b');

        $extension = new CommonMarkExtension([
            'a_conv' => $converterMockA,
            'b_conv' => $converterMockB,
        ]);

        $output = $extension->convertMarkdown('', 'b_conv');
        self::assertSame('b', $output);
    }

    /**
     * The twig filter must work without the converter name argument if there is only one converter
     */
    public function testFilterConverterNameIsOptional()
    {
        $converterMock = $this->getMockBuilder(CommonMarkConverter::class)->getMock();
        $converterMock->expects(self::once())->method('convertToHtml')->willReturnArgument(0);

        $expectedOutput = 'some markdown content';

        $extension = new CommonMarkExtension(['unique' => $converterMock]);
        $actualOutput = $extension->convertMarkdown($expectedOutput);

        self::assertSame($expectedOutput, $actualOutput);
    }

    /**
     * An exception is thrown if the provided converter name is wrong
     */
    public function testFilterWithInvalidConverterName()
    {
        $converterMock = $this->getMockBuilder(CommonMarkConverter::class)->getMock();
        $extension = new CommonMarkExtension(['my_converter' => $converterMock]);

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessageMatches('~The ".+" converter does not exists. Did you mean one of these ?.+~');

        $extension->convertMarkdown('', 'wrong_converter');
    }

    /**
     * Nothing is converted if the markdown content is null
     */
    public function testFilterWithNullContent()
    {
        $converterMock = $this->getMockBuilder(CommonMarkConverter::class)->getMock();
        $extension = new CommonMarkExtension(['my_converter' => $converterMock]);

        self::assertNull($extension->convertMarkdown(null, 'my_converter'));
    }
}