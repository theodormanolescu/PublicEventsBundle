<?php

namespace Elefant\PublicEventsBundle\PublicEvents;

use Symfony\Component\EventDispatcher\Event;

interface EventFilter
{
    /**
     * @param $eventName
     * @param Event|null $event
     * @return boolean
     */
    public function isPublic($eventName, Event $event = null);
}
