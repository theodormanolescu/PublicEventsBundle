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

    protected function doHandle($formattedEvent)
    {
        $message = json_encode($formattedEvent);
        try {
            $this->producer->publish($message, $this->routingKey);
        } catch (\Exception $exception) {
            if ($this->logger) {
                $this->logger->error(
                    'PublicEventsBundle_RabbitMqHandler error publishing',
                    ['exception' => $exception->getMessage()]
                );
            }
        }
    }
}
