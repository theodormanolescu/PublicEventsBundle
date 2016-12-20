<?php

namespace Elefant\PublicEventsBundle\PublicEvents;

use Elefant\PublicEventsBundle\PublicEvents\Handler\HandlerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublicEventDispatcher implements EventDispatcherInterface
{
    const ID = 'elefant.public_events.event_dispatcher';

    /** @var  EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Forward all calls to the inner EventDispatcher
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->eventDispatcher, $name], $arguments);
    }

    public function dispatch($eventName, Event $event = null)
    {
        $event = $this->eventDispatcher->dispatch($eventName, $event);
        if (!$event) {
            $event = new Event();
        }
        $this->eventDispatcher->dispatch('elefant.public_event', new PublicEvent($eventName, $event));
        return $event;
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        return $this->eventDispatcher->addListener($eventName, $listener, $priority);
    }


    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        return $this->eventDispatcher->addSubscriber($subscriber);
    }

    public function removeListener($eventName, $listener)
    {
        return $this->eventDispatcher->removeListener($eventName, $listener);
    }


    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        return $this->eventDispatcher->removeSubscriber($subscriber);
    }

    public function getListeners($eventName = null)
    {
        return $this->eventDispatcher->getListeners($eventName);
    }

    public function getListenerPriority($eventName, $listener)
    {
        return $this->eventDispatcher->getListenerPriority($eventName, $listener);
    }

    public function hasListeners($eventName = null)
    {
        return $this->eventDispatcher->hasListeners($eventName);
    }
}
