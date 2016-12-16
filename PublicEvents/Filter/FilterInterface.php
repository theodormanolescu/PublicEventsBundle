<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Filter;

use Symfony\Component\EventDispatcher\Event;

interface FilterInterface
{
    /**
     * @param $eventName
     * @param Event|null $event
     * @return boolean
     */
    public function isPublic($eventName, Event $event = null);
}
