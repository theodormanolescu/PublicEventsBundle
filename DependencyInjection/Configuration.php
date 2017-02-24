<?php

namespace Elefant\PublicEventsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                        ->prototype('array')
                        ->canBeUnset()
                            ->beforeNormalization()
                                ->ifTrue(function ($node) {
                                    return $node['type'] === 'rabbitmq';
                                })
                                ->then(function ($node) {
                                    $optionsResolver = new OptionsResolver();

                                    $node['config'] = $optionsResolver
                                        ->setDefault('connection', 'default')
                                        ->setDefault('routing_key', 'public_event')
                                        ->setDefault('exchange_options', ['name' => 'public_events', 'type' => 'direct'])
                                        ->setDefault('queue_options', [])
                                        ->setDefault('qos_options', [])
                                        ->setDefault('idle_timeout', null)
                                        ->setDefault('idle_timeout_exit_code', null)
                                        ->setRequired('callback')
                                        ->resolve($node['config']);

                                    return $node;
                                })
                            ->end()
                        ->children()
                            ->scalarNode('type')->end()
                            ->variableNode('config')->defaultValue([])->end()
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