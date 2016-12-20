<?php

namespace Elefant\PublicEventsBundle\DependencyInjection;

use Elefant\PublicEventsBundle\PublicEvents\Filter\ClassFilter;
use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\ArrayFormatter;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\JsonFormatter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\GuzzleHandler;
use Elefant\PublicEventsBundle\PublicEvents\Handler\LoggerHandler;
use Elefant\PublicEventsBundle\PublicEvents\Handler\RabbitMqProducerHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEventDispatcher;
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
                    $this->loadLoggerHandler($key, $handler, $container, $handler['type'], $config['formatter']);
                    break;
                case 'guzzle':
                    $this->loadGuzzleHandler($key, $handler, $container, $handler['type'], $config['formatter']);
                    break;
                case 'rabbitmq_producer':
                    $this->loadRabbitMqProducerHandler($key, $handler, $container, $handler['type'], $config['formatter']);
                    break;
            }
        }
    }

    private function loadLoggerHandler($name, array $config, ContainerBuilder $container, $type, $defaultFormatter)
    {
        $handlerDefinition = $this->loadHandler($name, $config, $container, LoggerHandler::class, $type, $defaultFormatter);
        $handlerDefinition->addMethodCall('setLogger', [new Reference('logger')]);
    }

    private function loadGuzzleHandler($name, array $config, ContainerBuilder $container, $type, $defaultFormatter)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->setRequired('client')
            ->setDefault('method', 'get')
            ->setDefault('uri', '/')
            ->setDefault('headers', []);

        $guzzleConfig = $optionsResolver->resolve($config['config']);

        $handlerDefinition = $this->loadHandler($name, $config, $container, GuzzleHandler::class, $type, $defaultFormatter);
        $handlerDefinition->setArguments([new Reference($guzzleConfig['client']), $guzzleConfig['method'], $guzzleConfig['uri'], $guzzleConfig['headers']]);

        return $handlerDefinition;
    }

    private function loadRabbitMqProducerHandler($name, array $config, ContainerBuilder $container, $type, $defaultFormatter)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->setRequired('producer')
            ->setDefault('routing_key', $name);

        $producerConfig = $optionsResolver->resolve($config['config']);
        $handlerDefinition = $this->loadHandler($name, $config, $container, RabbitMqProducerHandler::class, $type, $defaultFormatter);
        $handlerDefinition->setArguments([new Reference(sprintf('old_sound_rabbit_mq.%s_producer', $producerConfig['producer'])), $producerConfig['routing_key']]);

        return $handlerDefinition;
    }

    private function loadHandler($name, array $config, ContainerBuilder $container, $handlerClass, $type, $defaultFormatter)
    {
        $handlerDefinition = $container
            ->register(sprintf('elefant.public_events.%s_handler', $name), $handlerClass)
            ->addTag('elefant.public_events.handler', ['type' => $type]);

        $this->setFormatter($name, $config, $handlerDefinition, $defaultFormatter);

        $this->addFilters($name, $config, $handlerDefinition, $container);


        return $handlerDefinition;
    }

    private function setFormatter($name, array $config, Definition $handlerDefinition, $defaultFormatter)
    {
        if (isset($config['formatter'])) {
            $defaultFormatter = $config['formatter'];
        }

        if (!$defaultFormatter) {
            throw new \InvalidArgumentException(sprintf('You should define a formatter for handler "%s" or define a global formatter under "elefant_public_events"', $name));
        }

        switch ($defaultFormatter) {
            case 'array':
                $formatter = new Definition(ArrayFormatter::class);
                break;
            case 'json':
                $formatter = new Definition(JsonFormatter::class);
                break;
            default:
                $formatter = new Reference($defaultFormatter);
        }

        $handlerDefinition->addMethodCall('setFormatter', [$formatter]);
    }

    private function addFilters($name, array $config, Definition $handlerDefinition, ContainerBuilder $container)
    {
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
    }
}
