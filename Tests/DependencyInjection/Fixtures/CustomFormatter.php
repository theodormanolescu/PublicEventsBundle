<?php

namespace Elefant\PublicEventsBundle\Tests\DependencyInjection\Fixtures;

use Elefant\PublicEventsBundle\PublicEvents\Formatter\FormatterInterface;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;

class CustomFormatter implements FormatterInterface
{

    public function format(PublicEvent $event)
    {
        // TODO: Implement format() method.
    }
}