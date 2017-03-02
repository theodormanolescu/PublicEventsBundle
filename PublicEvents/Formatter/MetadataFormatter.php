<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Formatter;

use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;

class MetadataFormatter implements FormatterInterface
{

    public function format(PublicEvent $event)
    {
        return [
            'event_name' => $event->getOriginalEventName(),
            'event' => (array)$event->getOriginalEvent(),
            'event_source' => $event->getTrace(),
            'hostname' => php_uname('n')
        ];
    }
}
