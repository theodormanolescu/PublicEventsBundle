<?php

namespace Elefant\PublicEventsBundle\DependencyInjection\Compiler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface;
use Elefant\PublicEventsBundle\PublicEvents\Handler\LoggerHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEventDispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ServicePass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('event_dispatcher')) {
            return;
        }

        if (!$container->has(PublicEventDispatcher::ID)) {
            return;
        }

        if (!$container->has('logger')) {
            foreach ($container->findTaggedServiceIds('elefant.public_events.handler') as $serviceId => $tags) {
                if ($container->getDefinition($serviceId)->getClass() === LoggerHandler::class) {
                    $container->removeDefinition($serviceId);
                }
            }
        }

        if ($container->hasParameter('elefant.public_events.has_rabbitmq_handler')) {
            if (!$container->hasExtension('old_sound_rabbit_mq')) {
                throw new \LogicException('you need RabbitMqBundle to use rabbitmq');
            }
        }

        foreach ($container->findTaggedServiceIds('elefant.public_events.handler') as $serviceId => $tags) {
            $handlerDefinition = $container->getDefinition($serviceId);

            foreach ($tags as $tag) {
                if ($tag['type'] === 'rabbitmq') {
                    $handlerDefinition->setArguments([new Reference(sprintf('old_sound_rabbit_mq.public_events_%s_producer', $tag['name'])), $tag['routing_key']]);
                }
            }
            foreach ($handlerDefinition->getMethodCalls() as $call) {
                if ($call[0] !== 'addFilter') {
                    continue;
                }
                $filter = $call[1][0];
                $filterClass = '';
                if ($filter instanceof Reference) {
                    $filterClass = $container->getDefinition((string)$filter)->getClass();
                }
                if ($filter instanceof Definition) {
                    $filterClass = $filter->getClass();
                }

                if (!class_implements($filterClass, FilterInterface::class)) {
                    throw new \InvalidArgumentException(sprintf('Invalid filter class %s, a filter must implement %s', $filterClass, FilterInterface::class));
                }
            }
            $container->getDefinition(PublicEventDispatcher::ID)->addMethodCall('addListener', ['elefant.public_event', [new Reference($serviceId), 'handle']]);
        }
    }
}
