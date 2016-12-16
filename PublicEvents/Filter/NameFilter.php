<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Filter;

use Symfony\Component\EventDispatcher\Event;

class NameFilter implements FilterInterface
{
    private $regex;

    /**
     * @param $regex
     */
    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    public function isPublic($eventName, Event $event = null)
    {
        return preg_match($this->regex, $eventName);
    }
}
