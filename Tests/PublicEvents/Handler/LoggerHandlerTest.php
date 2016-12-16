<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\LoggerHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\SerializerInterface;

class LoggerHandlerTest extends TestCase
{
    public function testHandle()
    {
        $loggerHandler = new LoggerHandler();
        $event = new Event();

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->once())->method('log')->with(
            'info',
            'public_event',
            ['event_name' => 'name', 'event' => 'serialized_event']
        );

        $serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($event)
            ->willReturn('serialized_event');
        
        $filter = new NameFilter('/name/');
        

        $loggerHandler
            ->setLogger($logger)
            ->setSerializer($serializer)
            ->setSerializingFormat('php')
            ->addFilter($filter);

        $loggerHandler->handle('name', $event);
    }

    public function testCannotHandle()
    {
        $loggerHandler = new LoggerHandler();

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->never())->method('log');

        $filter = new NameFilter('/won\'t match/');

        $loggerHandler
            ->setLogger($logger)
            ->addFilter($filter);

        $loggerHandler->handle('matches?', new Event());
    }

    public function testCannotHandleWithoutLogger()
    {
        $loggerHandler = new LoggerHandler();
        $event = new Event();
        $filter = new NameFilter('/name/');

        $loggerHandler
            ->addFilter($filter)
            ->setSerializer($this->getMockBuilder(SerializerInterface::class)->getMock());

        $loggerHandler->handle('name', $event);
    }
}
