<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class RabbitMqProducerHandler extends Handler
{
    /** @var  ProducerInterface */
    private $producer;

    /**
     * @param ProducerInterface $producer
     */
    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    protected function doHandle($eventName, $formattedEvent)
    {
        $this->producer->publish($formattedEvent, $eventName);
    }

    protected function format($eventName, $serializedEvent)
    {
        return json_encode(['event_name' => $eventName, 'event' => $serializedEvent]);
    }
}
