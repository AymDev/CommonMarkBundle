<?php

namespace Aymdev\CommonmarkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('aymdev_commonmark');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('converters')
                ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->enumNode('type')
                                ->values(['commonmark', 'github'])
                                ->defaultValue('commonmark')
                            ->end()
                            ->arrayNode('options')->ignoreExtraKeys()->end()
                            ->arrayNode('extensions')->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}