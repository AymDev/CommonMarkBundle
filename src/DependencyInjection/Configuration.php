<?php

namespace Aymdev\CommonmarkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('aymdev_commonmark');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('converters')
                ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->enumNode('type')
                                ->values(['commonmark', 'github', 'empty'])
                                ->defaultValue('commonmark')
                            ->end()
                            ->variableNode('options')->end()
                            ->arrayNode('extensions')->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
