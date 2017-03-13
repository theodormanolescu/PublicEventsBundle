<?php

namespace Elefant\PublicEventsBundle\PublicEvents;

use Symfony\Component\EventDispatcher\Event;

class PublicEvent extends Event
{
    /** @var  Event */
    private $originalEvent;
    private $originalEventName;
    private $trace;

    /**
     * @param Event $originalEvent
     * @param $originalEventName
     */
    public function __construct($originalEventName, Event $originalEvent, $trace = [])
    {
        $this->originalEvent = $originalEvent;
        $this->originalEventName = $originalEventName;
        $this->trace = $trace;
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

    public function getTrace()
    {
        return $this->trace;
    }

    
}
