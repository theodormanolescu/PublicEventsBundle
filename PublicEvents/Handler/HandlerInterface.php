<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use Symfony\Component\EventDispatcher\Event;

interface HandlerInterface
{
    public function handle($eventName, Event $event = null);
}
