<?php

namespace Elefant\PublicEventsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder
            ->root('elefant_public_events')
                ->fixXmlConfig('handler')
                ->children()
                    ->scalarNode('formatter')->defaultNull()->end()
                    ->booleanNode('enabled')->defaultTrue()->end()
                    ->arrayNode('handlers')
                        ->useAttributeAsKey('key')
                        ->canBeUnset()
                        ->prototype('array')
                        ->children()
                            ->scalarNode('type')->end()
                            ->variableNode('config')->end()
                            ->variableNode('filters')->end()
                            ->scalarNode('formatter')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}