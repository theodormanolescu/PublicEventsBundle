<?php

namespace Elefant\PublicEventsBundle\Tests\DependencyInjection;

use Elefant\PublicEventsBundle\DependencyInjection\ElefantPublicEventsExtension;
use Elefant\PublicEventsBundle\ElefantPublicEventsBundle;
use Elefant\PublicEventsBundle\PublicEvents\Filter\ClassFilter;
use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\LoggerHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEventDispatcher;
use Elefant\PublicEventsBundle\PublicEvents\Serializer\PHPSerializer;
use OldSound\RabbitMqBundle\DependencyInjection\OldSoundRabbitMqExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

class ElefantPublicEventsExtensionTest extends TestCase
{

    public function testNoPublicEventDispatcherWhenThereIsNoEventDispatcher()
    {
        $container = $this->createContainer('no_event_dispatcher.yml');
        $this->assertFalse($container->has(PublicEventDispatcher::ID));
    }

    public function testNoPublicEventDispatcherWhenIsDisabled()
    {
        $container = $this->createContainer('disabled.yml');
        $this->assertFalse($container->has(PublicEventDispatcher::ID));
    }

    public function testPublicEventDispatcherDecoratesEventDispatcherWhenEnabled()
    {
        $container = $this->createContainer('enabled.yml');
        //When enabled, PublicEventDispatcher should replace the default EventDispatcher
        /** @var PublicEventDispatcher $eventDispatcher */
        $eventDispatcher = $container->get('event_dispatcher');
        $this->assertInstanceOf(PublicEventDispatcher::class, $eventDispatcher);
    }

    public function testNoLoggerHandlerWhenThereIsNoLogger()
    {
        $container = $this->createContainer('logger_handler_without_logger.yml');

        $this->assertFalse($container->has('elefant.public_events.logger_test_handler_without'));
    }

    public function testThereIsLoggerHandlerWhenThereIsLogger()
    {
        $container = $this->createContainer('logger_handler_with_logger.yml');

        /** @var Definition $loggerHandler */
        $loggerHandler = $container->getDefinition('elefant.public_events.logger_test_handler');
        $this->assertEquals(LoggerHandler::class, $loggerHandler->getClass());
    }

    public function testSerializerAndFiltersAreSet()
    {
        $container = $this->createContainer('logger_handler_with_logger.yml');

        /** @var Definition $loggerHandler */
        $loggerHandler = $container->getDefinition('elefant.public_events.logger_test_handler');

        $this->assertEquals(
            [
                ['setSerializer', [new Definition(PHPSerializer::class)]],
                ['addFilter', [new Definition(NameFilter::class, ['regex1'])]],
                ['addFilter', [new Definition(NameFilter::class, ['regex2'])]],
                ['addFilter', [new Definition(ClassFilter::class, ['ClassName'])]],
            ],
            $loggerHandler->getMethodCalls()
        );
    }

    public function testGuzzleHandler()
    {
        $container = $this->createContainer('guzzle_handler.yml');
        /** @var Definition $guzzleHandler */
        $guzzleHandler = $container->getDefinition('elefant.public_events.guzzle_test_handler');

        $this->assertEquals(
            [
                new Reference('guzzle_client'),
                'test_method',
                '/test_uri',
                ['extra headers']
            ],
            $guzzleHandler->getArguments()
        );
    }

    public function testGuzzleHandlerDefaultConfig()
    {
        $container = $this->createContainer('guzzle_handler_no_config.yml');
        /** @var Definition $guzzleHandler */
        $guzzleHandler = $container->getDefinition('elefant.public_events.guzzle_test_handler');

        $this->assertEquals(
            [
                new Reference('guzzle_client'),
                'get',
                '/',
                []
            ],
            $guzzleHandler->getArguments()
        );
    }

    public function testRabbitMqProducer()
    {
        $container = $this->createContainer('rabbitmq_producer_handler.yml', [new OldSoundRabbitMqExtension()]);
        /** @var Definition $producerHandler */
        $producerHandler = $container->getDefinition('elefant.public_events.producer_test_handler');

        $this->assertEquals(
            [
                new Reference('old_sound_rabbit_mq.test_producer_producer'),
                'test_routing_key',
            ],
            $producerHandler->getArguments()
        );
    }

    public function testRabbitMqProducerWithoutRoutingKey()
    {
        $container = $this->createContainer('rabbitmq_producer_handler_without_routing_key.yml', [new OldSoundRabbitMqExtension()]);
        /** @var Definition $producerHandler */
        $producerHandler = $container->getDefinition('elefant.public_events.producer_test_handler');

        $this->assertEquals(
            [
                new Reference('old_sound_rabbit_mq.test_producer_producer'),
                'producer_test',
            ],
            $producerHandler->getArguments()
        );
    }

    public function testCustomFilter()
    {
        $container = $this->createContainer('custom_filter.yml');

        /** @var Definition $loggerHandler */
        $loggerHandler = $container->getDefinition('elefant.public_events.logger_test_handler');

        $this->assertEquals(
            [
                ['setSerializer', [new Definition(PHPSerializer::class)]],
                ['addFilter', [new Reference('custom_filter')]],
            ],
            $loggerHandler->getMethodCalls()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid filter class \stdClass, a filter must implement Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface
     */
    public function testCustomInvalidFilter()
    {
        $this->createContainer('custom_invalid_filter.yml');
    }

    private function createContainer($file, $extensions = [])
    {
        $container = new ContainerBuilder(new ParameterBag(array('kernel.debug' => false)));

        $container->registerExtension(new ElefantPublicEventsExtension());

        foreach ($extensions as $extension) {
            $container->registerExtension($extension);
        }

        $locator = new FileLocator(__DIR__ . '/Fixtures');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load($file);

        $bundle = new ElefantPublicEventsBundle();
        $bundle->build($container);
        $container->compile();

        return $container;
    }
}
