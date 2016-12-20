<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Formatter;

use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;

interface FormatterInterface
{
    public function format(PublicEvent $event);
}
