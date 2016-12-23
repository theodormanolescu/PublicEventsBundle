<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqConsumer implements ConsumerInterface
{
    /** @var  callable */
    private $callback;

    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        return call_user_func($this->callback, $msg->getBody());
    }
}