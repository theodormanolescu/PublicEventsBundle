<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Formatter;

use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;

class ArrayFormatter implements FormatterInterface
{

    public function format(PublicEvent $event)
    {
        return [
            'event_name' => $event->getOriginalEventName(),
            'event' => (array)$event->getOriginalEvent(),
            'event_source' => $event->getTrace()
        ];
    }
}