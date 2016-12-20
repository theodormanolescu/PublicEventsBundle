<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;

interface HandlerInterface
{
    public function handle(PublicEvent $event);
}
