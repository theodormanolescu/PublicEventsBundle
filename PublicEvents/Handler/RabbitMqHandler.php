<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class RabbitMqHandler extends Handler
{
    /** @var  ProducerInterface */
    private $producer;
    /** @var  string */
    private $routingKey;

    /**
     * RabbitMqProducerHandler constructor.
     * @param ProducerInterface $producer
     * @param $routingKey
     */
    public function __construct(ProducerInterface $producer = null, $routingKey = null)
    {
        $this->producer = $producer;
        $this->routingKey = $routingKey;
    }

    protected function doHandle($message)
    {
        try {
            $this->producer->publish($message, $this->routingKey);
        } catch (\Exception $e) {

        }
    }
}
