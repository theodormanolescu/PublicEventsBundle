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
        if ($regex[0] !== '/') {
            $regex = '/' . $regex;
        }
        if ($regex[strlen($regex)-1] !== '/') {
            $regex = $regex . '/';
        }
        $this->regex = $regex;
    }

    public function isPublic($eventName, Event $event)
    {
        return preg_match($this->regex, $eventName);
    }
}
