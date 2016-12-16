<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Handler\RabbitMqProducerHandler;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

class RabbitmqProducerHandlerTest extends TestCase
{
    public function testProducer()
    {
        $producer = $this->getMockBuilder(ProducerInterface::class)->getMock();

        $producer->expects($this->once())
            ->method('publish')
            ->with('{"event_name":"test_event","event":"serialized body"}','test_event');

        $producerHandler = new RabbitMqProducerHandler($producer);
        HandlerMocker::addAllFilterAndSerializer($this, $producerHandler);

        $producerHandler->handle('test_event', new Event());
    }
}