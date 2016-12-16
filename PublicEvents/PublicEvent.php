<?php

namespace Elefant\PublicEventsBundle\PublicEvents;

use Symfony\Component\EventDispatcher\Event;

class PublicEvent extends Event
{
    /** @var  Event */
    private $originalEvent;
    private $originalEventName;

    /**
     * @param Event $originalEvent
     * @param $originalEventName
     */
    public function __construct($originalEventName, Event $originalEvent)
    {
        $this->originalEvent = $originalEvent;
        $this->originalEventName = $originalEventName;
    }

    /**
     * @return Event
     */
    public function getOriginalEvent()
    {
        return $this->originalEvent;
    }

    /**
     * @return mixed
     */
    public function getOriginalEventName()
    {
        return $this->originalEventName;
    }
}
