<?php

namespace Elefant\PublicEventsBundle\Tests\DependencyInjection;

use OldSound\RabbitMqBundle\DependencyInjection\OldSoundRabbitMqExtension;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ElefantPublicEventsExtensionRabbitMqTest extends TestCase
{

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage you need RabbitMqBundle to use rabbitmq_producer
     */
    public function testFailsWithoutRabbitMqExtension()
    {
        ContainerFactory::createContainer('rabbitmq/producer_handler_without_rabbitmq_extension.yml');
    }

    public function testProducersConfigIsAppended()
    {
        $container = ContainerFactory::createContainer('rabbitmq/producer_handler.yml', [new OldSoundRabbitMqExtension()]);
        $producerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_producer');

        $this->assertEquals(Producer::class, $producerDefinition->getClass());
        $this->assertEquals([new Reference('old_sound_rabbit_mq.connection.default')], $producerDefinition->getArguments());

        $exchangeOptions = $producerDefinition->getMethodCalls()[0][1][0];
        $this->assertArraySubset(
            ['name' => 'public_events', 'type' => 'direct'],
            $exchangeOptions
        );
    }

    public function testProducersConfigWithCustomExchangeOptions()
    {
        $container = ContainerFactory::createContainer('rabbitmq/producer_handler_custom_exchange_options.yml', [new OldSoundRabbitMqExtension()]);
        $producerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_producer');

        $this->assertEquals(Producer::class, $producerDefinition->getClass());
        $this->assertEquals([new Reference('old_sound_rabbit_mq.connection.default')], $producerDefinition->getArguments());

        $exchangeOptions = $producerDefinition->getMethodCalls()[0][1][0];
        $this->assertArraySubset(
            ['name' => 'exchange_name', 'type' => 'special'],
            $exchangeOptions
        );
    }

    public function testProducersConfigWithCustomConnection()
    {
        $container = ContainerFactory::createContainer('rabbitmq/producer_handler_custom_connection.yml', [new OldSoundRabbitMqExtension()]);
        $producerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_producer');

        $this->assertEquals(Producer::class, $producerDefinition->getClass());
        $this->assertEquals([new Reference('old_sound_rabbit_mq.connection.custom')], $producerDefinition->getArguments());
    }
    public function testConsumersConfigIsAppended()
    {
        $container = ContainerFactory::createContainer('rabbitmq/producer_handler.yml', [new OldSoundRabbitMqExtension()]);
        $consumerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_consumer');
        return ;
    }

    public function testRabbitMqProducer()
    {
        $container = ContainerFactory::createContainer('rabbitmq/producer_handler.yml', [new OldSoundRabbitMqExtension()]);
        /** @var Definition $producerHandler */
        $producerHandler = $container->getDefinition('elefant.public_events.producer_test_handler');

        $this->assertEquals(
            [
                new Reference('old_sound_rabbit_mq.public_events_producer_test_producer'),
                'test_routing_key',
            ],
            $producerHandler->getArguments()
        );
    }

    public function testRabbitMqProducerWithoutRoutingKey()
    {
        $container = ContainerFactory::createContainer('rabbitmq/producer_handler_without_routing_key.yml', [new OldSoundRabbitMqExtension()]);
        /** @var Definition $producerHandler */
        $producerHandler = $container->getDefinition('elefant.public_events.producer_test_handler');

        $this->assertEquals(
            [
                new Reference('old_sound_rabbit_mq.public_events_producer_test_producer'),
                'producer_test',
            ],
            $producerHandler->getArguments()
        );
    }
}
