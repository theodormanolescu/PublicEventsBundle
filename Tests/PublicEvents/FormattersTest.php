<?php

namespace Elefant\PublicEventsBundle\Tests;

use Elefant\PublicEventsBundle\PublicEvents\Formatter\ArrayFormatter;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\JsonFormatter;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

class FormatterTest extends TestCase
{
    public function testJsonFormatter()
    {
        $formatter = new JsonFormatter();

        $this->assertEquals(
            json_encode([
                'event_name' => 'event_name',
                'event' => (array)(new Event()),
                'event_source' => ['trace']
            ]),
            $formatter->format(new PublicEvent('event_name', new Event(), ['trace']))
        );
    }

    public function testArrayFormatter()
    {
        $formatter = new ArrayFormatter();

        $this->assertEquals(
            [
                'event_name' => 'event_name',
                'event' => (array)(new Event()),
                'event_source' => ['trace']
            ],
            $formatter->format(new PublicEvent('event_name', new Event(), ['trace']))
        );
    }
}