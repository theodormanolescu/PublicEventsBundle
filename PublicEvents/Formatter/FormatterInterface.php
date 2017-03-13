<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Formatter;

use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;

interface FormatterInterface
{
    /**
     * @param PublicEvent $event
     * @return array
     */
    public function format(PublicEvent $event);
}
