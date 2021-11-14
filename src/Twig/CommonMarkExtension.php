<?php

namespace Aymdev\CommonmarkBundle\Twig;

use League\CommonMark\MarkdownConverter;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @internal
 */
class CommonMarkExtension extends AbstractExtension
{
    private ServiceLocator $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
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
        if ($converterName === null && count($this->serviceLocator->getProvidedServices()) === 1) {
            /** @var MarkdownConverter $converter */
            $converterName = array_key_first($this->serviceLocator->getProvidedServices());
            $converter = $this->serviceLocator->get($converterName);
            return $converter->convertToHtml($markdown);
        }

        if (false === $this->serviceLocator->has($converterName)) {
            $message = 'The "%s" converter does not exists. Did you mean one of these ? %s';
            $availableConverters = implode(', ', array_keys($this->serviceLocator->getProvidedServices()));
            throw new \InvalidArgumentException(sprintf($message, $converterName, $availableConverters));
        }

        if ($markdown === null) {
            return null;
        }

        /** @var MarkdownConverter $converter */
        $converter = $this->serviceLocator->get($converterName);
        return $converter->convertToHtml($markdown)->getContent();
    }
}