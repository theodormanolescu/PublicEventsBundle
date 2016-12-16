<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents;

use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
use Elefant\PublicEventsBundle\PublicEvents\PublicEventDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublicEventDispatcherTest extends TestCase
{

    public function testPublicEventDispatcherDispatchesPublicEvent()
    {
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with('elefant.public_event', new PublicEvent('test_event', new Event()));

        $publicEventDispatcher = new PublicEventDispatcher($eventDispatcher);

        $publicEventDispatcher->dispatch('test_event', new Event());
    }

    public function testPublicEventDispatcherPreservesOriginalEventDispatcher()
    {
        $listener = function () {
            return 'this is a listener';
        };

        $subscriber = $this->getMockBuilder(EventSubscriberInterface::class)->getMock();
        $eventName = 'event_name';

        $eventDispatcher = $this
            ->getMockBuilder(EventDispatcherInterface::class)
            ->setMethods(['dispatch', 'addListener', 'addSubscriber', 'getListenerPriority', 'getListeners', 'hasListeners', 'removeListener', 'removeSubscriber', 'customMethod'])
            ->getMock();
        $eventDispatcher
            ->expects($this->once())
            ->method('addListener')
            ->with($eventName, $listener);
        $eventDispatcher->expects($this->once())
            ->method('addSubscriber')
            ->with($subscriber);
        $eventDispatcher->expects($this->once())
            ->method('getListenerPriority')
            ->with($eventName, $listener);
        $eventDispatcher->expects($this->once())
            ->method('getListeners')
            ->with($eventName)
            ->willReturn([$listener]);
        $eventDispatcher->expects($this->once())
            ->method('hasListeners')
            ->with($eventName)
            ->willReturn(true);
        $eventDispatcher->expects($this->once())
            ->method('removeListener')
            ->with($eventName, $listener);
        $eventDispatcher->expects($this->once())
            ->method('removeSubscriber')
            ->with($subscriber);
        $eventDispatcher->expects($this->once())
            ->method('customMethod')
            ->with('custom_argument');

        $publicEventDispatcher = new PublicEventDispatcher($eventDispatcher);

        $publicEventDispatcher->addListener($eventName, $listener);
        $publicEventDispatcher->addSubscriber($subscriber);
        $this->assertEquals(0, $publicEventDispatcher->getListenerPriority($eventName, $listener));
        $this->assertEquals([$listener], $publicEventDispatcher->getListeners($eventName));
        $this->assertTrue($publicEventDispatcher->hasListeners($eventName));
        $publicEventDispatcher->removeListener($eventName, $listener);
        $publicEventDispatcher->removeSubscriber($subscriber);
        $publicEventDispatcher->customMethod('custom_argument');
    }
}
