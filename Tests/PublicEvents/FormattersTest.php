<?php

namespace Elefant\PublicEventsBundle\Tests;

use Elefant\PublicEventsBundle\PublicEvents\Formatter\MetadataFormatter;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

class FormatterTest extends TestCase
{
    public function testMetadataFormatter()
    {
        $formatter = new MetadataFormatter();

        $this->assertEquals(
            [
                'event_name' => 'event_name',
                'event_source' => ['trace'],
                'event_class' => Event::class,
                'hostname' => php_uname('n')
            ],
            $formatter->format(new PublicEvent('event_name', new Event(), ['trace']))
        );
    }
}