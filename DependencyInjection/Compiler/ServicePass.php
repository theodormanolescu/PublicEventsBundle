<?php

namespace Elefant\PublicEventsBundle\DependencyInjection\Compiler;

use Elefant\PublicEventsBundle\PublicEvents\Handler\LoggerHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEventDispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

        foreach ($container->findTaggedServiceIds('elefant.public_events.handler') as $serviceId => $tags) {
            $container
                ->getDefinition(PublicEventDispatcher::ID)
                ->addMethodCall('addListener', ['elefant.public_event', [new Reference($serviceId), 'handle']]);
        }
    }
}
