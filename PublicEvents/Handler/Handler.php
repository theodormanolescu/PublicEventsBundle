<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface;
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

    private function canHandle($eventName, Event $event = null)
    {
        foreach ($this->filters as $filter) {
            if ($filter->isPublic($eventName, $event)) {
                return true;
            }
        }
        return false;
    }

    public function handle($eventName, Event $event = null)
    {
        if (!$this->canHandle($eventName, $event)) {
            return;
        }
        $formatted = $this->format($eventName, $this->serializer->serialize($event, $this->serializingFormat));
        $this->doHandle($eventName, $formatted);
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
