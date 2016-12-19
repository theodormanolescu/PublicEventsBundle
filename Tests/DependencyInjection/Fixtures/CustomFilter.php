<?php

namespace Elefant\PublicEventsBundle\Tests\DependencyInjection\Fixtures;

use Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface;
use Symfony\Component\EventDispatcher\Event;

class CustomFilter implements FilterInterface
{

    /**
     * @param $eventName
     * @param Event|null $event
     * @return boolean
     */
    public function isPublic($eventName, Event $event = null)
    {
        // TODO: Implement isPublic() method.
    }
}
