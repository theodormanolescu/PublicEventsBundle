<?php

namespace Elefant\PublicEventsBundle\Tests\DependencyInjection;

use OldSound\RabbitMqBundle\DependencyInjection\OldSoundRabbitMqExtension;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ElefantPublicEventsExtensionRabbitMqTest extends TestCase
{

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage you need RabbitMqBundle to use rabbitmq
     */
    public function testFailsWithoutRabbitMqExtension()
    {
        ContainerFactory::createContainer('rabbitmq/handler_without_rabbitmq_extension.yml');
    }

    public function testProducersConfigIsAppended()
    {
        $container = ContainerFactory::createContainer('rabbitmq/handler.yml', [new OldSoundRabbitMqExtension()]);
        $producerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_producer');

        $this->assertEquals(Producer::class, $producerDefinition->getClass());
        $this->assertEquals([new Reference('old_sound_rabbit_mq.connection.default')], $producerDefinition->getArguments());

        $exchangeOptions = $producerDefinition->getMethodCalls()[0][1][0];
        $this->assertArraySubset(
            ['name' => 'public_events', 'type' => 'direct'],
            $exchangeOptions
        );
    }

    public function testConfigWithCustomExchangeOptions()
    {
        $container = ContainerFactory::createContainer('rabbitmq/handler_custom_exchange_options.yml', [new OldSoundRabbitMqExtension()]);
        $producerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_producer');

        $this->assertEquals(Producer::class, $producerDefinition->getClass());
        $this->assertEquals([new Reference('old_sound_rabbit_mq.connection.default')], $producerDefinition->getArguments());

        $exchangeOptions = $producerDefinition->getMethodCalls()[0][1][0];
        $this->assertArraySubset(
            ['name' => 'exchange_name', 'type' => 'special'],
            $exchangeOptions
        );
    }

    public function testConfigWithCustomQosOptions()
    {
        $container = ContainerFactory::createContainer('rabbitmq/handler_custom_qos_options.yml', [new OldSoundRabbitMqExtension()]);
        $consumerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_consumer');

        $this->assertEquals(Consumer::class, $consumerDefinition->getClass());
        $this->assertEquals([new Reference('old_sound_rabbit_mq.connection.default')], $consumerDefinition->getArguments());

        $qosOptions = $consumerDefinition->getMethodCalls()[3][1];
        $this->assertArraySubset(
            [0, 1, false],
            $qosOptions
        );
    }

    public function testConfigWithCustomConnection()
    {
        $container = ContainerFactory::createContainer('rabbitmq/handler_custom_connection.yml', [new OldSoundRabbitMqExtension()]);
        $producerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_producer');

        $this->assertEquals(Producer::class, $producerDefinition->getClass());
        $this->assertEquals([new Reference('old_sound_rabbit_mq.connection.custom')], $producerDefinition->getArguments());
    }

    public function testConsumersConfigIsAppended()
    {
        $container = ContainerFactory::createContainer('rabbitmq/handler.yml', [new OldSoundRabbitMqExtension()]);
        $consumerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_consumer');

        $exchangeOptions = $consumerDefinition->getMethodCalls()[0][1][0];
        $this->assertArraySubset(
            ['name' => 'public_events', 'type' => 'direct'],
            $exchangeOptions
        );
        $queueOptions = $consumerDefinition->getMethodCalls()[1][1][0];
        $this->assertArraySubset(
            ['name' => 'public_events.producer_test', 'routing_keys' => ['producer_test']],
            $queueOptions
        );

        $callBack = $consumerDefinition->getMethodCalls()[2][1][0];
        $this->assertEquals(
            [new Reference('event_dispatcher'), 'execute'],
            $callBack
        );

        return;
    }

    public function testRabbitMqWithoutRoutingKey()
    {
        $container = ContainerFactory::createContainer('rabbitmq/handler_without_routing_key.yml', [new OldSoundRabbitMqExtension()]);
        /** @var Definition $producerHandler */
        $producerHandler = $container->getDefinition('elefant.public_events.producer_test_handler');

        $this->assertEquals(
            [
                new Reference('old_sound_rabbit_mq.public_events_producer_test_producer'),
                'public_event',
            ],
            $producerHandler->getArguments()
        );
    }

    public function testConfigWithCustomQueueOptions()
    {
        $container = ContainerFactory::createContainer('rabbitmq/handler_custom_queue_options.yml', [new OldSoundRabbitMqExtension()]);
        $consumerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_consumer');

        $this->assertEquals(Consumer::class, $consumerDefinition->getClass());
        $this->assertEquals([new Reference('old_sound_rabbit_mq.connection.default')], $consumerDefinition->getArguments());

        $queueOptions = $consumerDefinition->getMethodCalls()[1][1][0];
        $this->assertArraySubset(
            ['name' => 'exchange_name', 'routing_keys' => ['test_routing_key']],
            $queueOptions
        );
    }

    public function testCustomInvalidFormatter()
    {
        ContainerFactory::createContainer('custom_invalid_formatter.yml');
    }

    public function testConfigWithCustomIdleTimeoutOptions()
    {
        $container = ContainerFactory::createContainer('rabbitmq/handler_custom_idle_timeout_options.yml', [new OldSoundRabbitMqExtension()]);
        $consumerDefinition = $container->getDefinition('old_sound_rabbit_mq.public_events_producer_test_consumer');

        $this->assertEquals(Consumer::class, $consumerDefinition->getClass());

        $idleTimeoutOption = $consumerDefinition->getMethodCalls()[4][1];
        $this->assertArraySubset(
            [10],
            $idleTimeoutOption
        );

        $idleTimeoutExitCodeOption = $consumerDefinition->getMethodCalls()[5][1];
        $this->assertArraySubset(
            [1],
            $idleTimeoutExitCodeOption
        );
    }
}
