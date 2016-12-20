<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Handler\RabbitMqProducerHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
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
            ->with('message','routing_key');

        $producerHandler = new RabbitMqProducerHandler($producer, 'routing_key');
        HandlerMocker::addFilterAndJsonFormatter($producerHandler);
        $producerHandler->setFormatter(HandlerMocker::getMockFormatter($this, 'message'));

        $producerHandler->handle(new PublicEvent('test_event', new Event()));
    }
}