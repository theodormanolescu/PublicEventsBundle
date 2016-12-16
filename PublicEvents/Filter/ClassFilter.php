<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Filter;

use Symfony\Component\EventDispatcher\Event;

class ClassFilter implements FilterInterface
{

    private $className;

    /**
     * @param $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param $eventName
     * @param Event|null $event
     * @return boolean
     */
    public function isPublic($eventName, Event $event = null)
    {
        return $event instanceof $this->className;
    }
}
