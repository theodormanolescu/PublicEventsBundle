<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\ArrayFormatter;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\FormatterInterface;
use Elefant\PublicEventsBundle\PublicEvents\Handler\LoggerHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
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
            ['formatted']
        );

        $filter = new NameFilter('/name/');


        $loggerHandler
            ->setLogger($logger)
            ->setFormatter(HandlerMocker::getMockFormatter($this, ['formatted']))
            ->addFilter($filter);

        $loggerHandler->handle(new PublicEvent('name', $event));
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

        $loggerHandler->handle(new PublicEvent('matches?', new Event()));
    }

    public function testCannotHandleWithoutLogger()
    {
        $loggerHandler = new LoggerHandler();
        $event = new Event();
        $filter = new NameFilter('/name/');

        $loggerHandler
            ->addFilter($filter)
            ->setFormatter(new ArrayFormatter());

        $loggerHandler->handle(new PublicEvent('name', $event));
    }
}
