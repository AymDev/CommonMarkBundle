<?php

namespace Aymdev\CommonmarkBundle\Twig;

use League\CommonMark\CommonMarkConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CommonMarkExtension extends AbstractExtension
{
    /**
     * @var array<string, CommonMarkConverter>
     */
    private $converters;

    /**
     * @param array<string, CommonMarkConverter> $converters
     */
    public function __construct(array $converters)
    {
        $this->converters = $converters;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('commonmark', [$this, 'convertMarkdown'], [
                'is_safe' => ['html']
            ])
        ];
    }

    public function convertMarkdown(?string $markdown, ?string $converterName = null): ?string
    {
        // Single converter setup
        if ($converterName === null && count($this->converters) === 1) {
            /** @var CommonMarkConverter $converter */
            $converter = $this->converters[array_key_first($this->converters)];
            return $converter->convertToHtml($markdown);
        }

        if (false === isset($this->converters[$converterName])) {
            $message = 'The "%s" converter does not exists. Did you mean one of these ? %s';
            $availableConverters = implode(', ', array_keys($this->converters));
            throw new \InvalidArgumentException(sprintf($message, $converterName, $availableConverters));
        }

        if ($markdown === null) {
            return null;
        }

        /** @var CommonMarkConverter $converter */
        $converter = $this->converters[$converterName];
        return $converter->convertToHtml($markdown);
    }
}