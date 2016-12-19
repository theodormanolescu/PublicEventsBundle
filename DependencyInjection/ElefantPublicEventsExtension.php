<?php

namespace Elefant\PublicEventsBundle\DependencyInjection;

use Elefant\PublicEventsBundle\PublicEvents\Filter\ClassFilter;
use Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface;
use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\GuzzleHandler;
use Elefant\PublicEventsBundle\PublicEvents\Handler\LoggerHandler;
use Elefant\PublicEventsBundle\PublicEvents\Handler\RabbitMqProducerHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEventDispatcher;
use Elefant\PublicEventsBundle\PublicEvents\Serializer\PHPSerializer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElefantPublicEventsExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        if (!$config['enabled']) {
            return;
        }

        $publicEventDispatcherDefinition = new Definition(PublicEventDispatcher::class);
        $container->setDefinition(PublicEventDispatcher::ID, $publicEventDispatcherDefinition)
            ->setPublic(false)
            ->setDecoratedService('event_dispatcher')
            ->setArguments([new Reference(PublicEventDispatcher::ID . '.inner')]);

        foreach ($config['handlers'] as $key => $handler) {
            switch ($handler['type']) {
                case 'logger':
                    $this->loadLoggerHandler($key, $handler, $container, $handler['type']);
                    break;
                case 'guzzle':
                    $this->loadGuzzleHandler($key, $handler, $container, $handler['type']);
                    break;
                case 'rabbitmq_producer':
                    $this->loadRabbitMqProducerHandler($key, $handler, $container, $handler['type']);
                    break;
            }
        }
    }

    private function loadLoggerHandler($name, array $config, ContainerBuilder $container, $type)
    {
        return $this->loadHandler($name, $config, $container, LoggerHandler::class, $type);
    }

    private function loadGuzzleHandler($name, array $config, ContainerBuilder $container, $type)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->setRequired('client')
            ->setDefault('method', 'get')
            ->setDefault('uri', '/')
            ->setDefault('headers', []);

        $guzzleConfig = $optionsResolver->resolve($config['config']);

        $handlerDefinition = $this->loadHandler($name, $config, $container, GuzzleHandler::class, $type);
        $handlerDefinition->setArguments([new Reference($guzzleConfig['client']), $guzzleConfig['method'], $guzzleConfig['uri'], $guzzleConfig['headers']]);

        return $handlerDefinition;
    }

    private function loadRabbitMqProducerHandler($name, array $config, ContainerBuilder $container, $type)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->setRequired('producer')
            ->setDefault('routing_key', $name);

        $producerConfig = $optionsResolver->resolve($config['config']);
        $handlerDefinition = $this->loadHandler($name, $config, $container, RabbitMqProducerHandler::class, $type);
        $handlerDefinition->setArguments([new Reference(sprintf('old_sound_rabbit_mq.%s_producer', $producerConfig['producer'])), $producerConfig['routing_key']]);

        return $handlerDefinition;
    }

    private function loadHandler($name, array $config, ContainerBuilder $container, $handlerClass, $type)
    {
        $handlerDefinition = $container
            ->register(sprintf('elefant.public_events.%s_handler', $name), $handlerClass)
            ->addMethodCall('setSerializer', [new Definition(PHPSerializer::class)])
            ->addTag('elefant.public_events.handler', ['type' => $type]);

        if (!isset($config['filters'])) {
            $config['filters'] = [['name' => '/.*/']];
        }

        foreach ($config['filters'] as $index => $filter) {
            if (is_string($filter)) {
                $handlerDefinition->addMethodCall('addFilter', [new Reference($filter)]);
            }
            if (!empty($filter['name'])) {
                $filterDefinition = $container
                    ->register(sprintf('elefant.public_events.%s_%s_name_filter', $name, $index), NameFilter::class)
                    ->setArguments([$filter['name']]);
                $handlerDefinition->addMethodCall('addFilter', [$filterDefinition]);
            }
            if (!empty($filter['class'])) {
                $filterDefinition = $container
                    ->register(sprintf('elefant.public_events.%s_%s_class_filter', $name, $index), ClassFilter::class)
                    ->setArguments([$filter['class']]);
                $handlerDefinition->addMethodCall('addFilter', [$filterDefinition]);
            }
        }

        return $handlerDefinition;
    }
}
