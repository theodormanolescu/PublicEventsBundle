<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\Handler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

class HandlerTest extends TestCase
{
    public function testMultipleFormatters()
    {
        $handler = new TestHandler();
        $handler->addFilter(new NameFilter('/.*/'));
        $handler->addFormatter(HandlerMocker::getMockFormatter($this, ['formatter1']));
        $handler->addFormatter(HandlerMocker::getMockFormatter($this, ['formatter2']));

        $handler->handle(new PublicEvent('name', new Event()));

        $this->assertEquals(['formatter1', 'formatter2'], $handler->formattedEvent);
    }
}

class TestHandler extends Handler
{
    public $formattedEvent;

    protected function doHandle($formattedEvent)
    {
        $this->formattedEvent = $formattedEvent;
    }
}
