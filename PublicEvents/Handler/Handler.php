<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\SerializerInterface;

abstract class Handler implements HandlerInterface
{
    /** @var  FilterInterface[] */
    private $filters = [];
    /** @var  SerializerInterface */
    private $serializer;
    /** @var  string */
    private $serializingFormat = 'event';

    /**
     * @param FilterInterface $filter
     * @return Handler
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    private function canHandle(PublicEvent $event)
    {
        foreach ($this->filters as $filter) {
            if ($filter->isPublic($event->getOriginalEventName(), $event->getOriginalEvent())) {
                return true;
            }
        }
        return false;
    }

    public function handle(PublicEvent $event)
    {
        if (!$this->canHandle($event)) {
            return;
        }
        $formatted = $this->format($event->getOriginalEventName(), $this->serializer->serialize($event->getOriginalEvent(), $this->serializingFormat));
        $this->doHandle($event->getOriginalEventName(), $formatted);
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }

    public function setSerializingFormat($format)
    {
        $this->serializingFormat = $format;
        return $this;
    }

    abstract protected function doHandle($eventName, $formattedEvent);

    abstract protected function format($eventName, $serializedEvent);
}
