<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Formatter;

use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;

class JsonFormatter implements FormatterInterface
{

    public function format(PublicEvent $event)
    {
        return json_encode([
            'event_name' => $event->getOriginalEventName(),
            'event' => (array)$event->getOriginalEvent()
        ]);
    }
}