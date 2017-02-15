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
    /** @var  bool */
    private $traceSource;

    /**
     * PublicEventDispatcher constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param bool $traceSource
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, $traceSource = false)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->traceSource = $traceSource;
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

        $publicEvent = $this->createPublicEvent($eventName, $event);
        $this->eventDispatcher->dispatch('elefant.public_event', $publicEvent);
        return $event;
    }

    private function createPublicEvent($eventName, Event $event)
    {
        $trace = [];
        if ($this->traceSource) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
            /**
             * $backtrace[0]: this::dispatch
             * $backtrace[1]: application caller
             * $backtrace[2]: deeper application caller.. up to script entry point
             */
            if (isset($backtrace[1])) {
                $trace = $backtrace[1];
            }
        }

        return new PublicEvent($eventName, $event, $trace);
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
