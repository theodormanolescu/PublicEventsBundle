<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\RabbitMqHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;

class RabbitmqHandlerTest extends TestCase
{
    public function testProducer()
    {
        $producer = $this->getMockBuilder(ProducerInterface::class)->getMock();

        $producer->expects($this->once())
            ->method('publish')
            ->with('["message"]', 'routing_key');

        $producerHandler = new RabbitMqHandler($producer, 'routing_key');
        $producerHandler->addFilter(new NameFilter('/.*/'))
            ->addFormatter(HandlerMocker::getMockFormatter($this, ['message']));

        $producerHandler->handle(new PublicEvent('test_event', new Event()));
    }

    public function testLogError()
    {
        $producer = $this->getMockBuilder(ProducerInterface::class)->getMock();
        $producer->expects($this->once())
            ->method('publish')
            ->willThrowException(new \Exception('test exception'));

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->once())
            ->method('error')
            ->with();

        $producerHandler = new RabbitMqHandler($producer, 'routing_key');
        $producerHandler->addFilter(new NameFilter('/.*/'))
            ->addFormatter(HandlerMocker::getMockFormatter($this, ['message']))
            ->setLogger($logger);

        $producerHandler->handle(new PublicEvent('test_event', new Event()));
    }
}