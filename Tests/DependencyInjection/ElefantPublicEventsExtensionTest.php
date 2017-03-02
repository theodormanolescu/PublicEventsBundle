<?php

namespace Elefant\PublicEventsBundle\Tests\DependencyInjection;

use Elefant\PublicEventsBundle\PublicEvents\Filter\ClassFilter;
use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\MetadataFormatter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\LoggerHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEventDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ElefantPublicEventsExtensionTest extends TestCase
{

    public function testNoPublicEventDispatcherWhenThereIsNoEventDispatcher()
    {
        $container = ContainerFactory::createContainer();
        $this->assertFalse($container->has(PublicEventDispatcher::ID));
    }

    public function testNoPublicEventDispatcherWhenIsDisabled()
    {
        $container = ContainerFactory::createContainer('disabled.yml');
        $this->assertFalse($container->has(PublicEventDispatcher::ID));
    }

    public function testPublicEventDispatcherDecoratesEventDispatcherWhenEnabled()
    {
        $container = ContainerFactory::createContainer('enabled.yml');
        //When enabled, PublicEventDispatcher should replace the default EventDispatcher
        /** @var PublicEventDispatcher $eventDispatcher */
        $eventDispatcher = $container->get('event_dispatcher');
        $this->assertInstanceOf(PublicEventDispatcher::class, $eventDispatcher);
    }

    public function testPublicEventDispatcherEnablesEventTracing()
    {
        $container = ContainerFactory::createContainer('enable_tracing.yml');
        $eventDispatcherDefinition = $container->getDefinition('event_dispatcher');

        $this->assertEquals(true, $eventDispatcherDefinition->getArguments()[1]);
    }

    public function testNoLoggerHandlerWhenThereIsNoLogger()
    {
        $container = ContainerFactory::createContainer('logger_handler_without_logger.yml');

        $this->assertFalse($container->has('elefant.public_events.logger_test_handler_without'));
    }

    public function testThereIsLoggerHandlerWhenThereIsLogger()
    {
        $container = ContainerFactory::createContainer('logger_handler_with_logger.yml');

        /** @var Definition $loggerHandler */
        $loggerHandler = $container->getDefinition('elefant.public_events.logger_test_handler');
        $this->assertEquals(LoggerHandler::class, $loggerHandler->getClass());
    }

    public function testFormatterAndFiltersAreSet()
    {
        $container = ContainerFactory::createContainer('logger_handler_with_logger.yml');

        /** @var Definition $loggerHandler */
        $loggerHandler = $container->getDefinition('elefant.public_events.logger_test_handler');

        $this->assertEquals(
            [
                ['addFormatter', [new Definition(MetadataFormatter::class)]],
                ['addFilter', [new Definition(NameFilter::class, ['regex1'])]],
                ['addFilter', [new Definition(NameFilter::class, ['regex2'])]],
                ['addFilter', [new Definition(ClassFilter::class, ['ClassName'])]],
                ['setLogger', [new Reference('logger')]],
            ],
            $loggerHandler->getMethodCalls()
        );
    }

    public function testGuzzleHandler()
    {
        $container = ContainerFactory::createContainer('guzzle_handler.yml');
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
        $container = ContainerFactory::createContainer('guzzle_handler_no_config.yml');
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

    public function testCustomFilter()
    {
        $container = ContainerFactory::createContainer('custom_filter.yml');

        /** @var Definition $loggerHandler */
        $loggerHandler = $container->getDefinition('elefant.public_events.logger_test_handler');

        $this->assertEquals(
            [
                ['addFilter', [new Reference('custom_filter')]],
                ['setLogger', [new Reference('logger')]],
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
        ContainerFactory::createContainer('custom_invalid_filter.yml');
    }

    public function testCustomFormatter()
    {
        $container = ContainerFactory::createContainer('custom_formatter.yml');

        /** @var Definition $loggerHandler */
        $loggerHandler = $container->getDefinition('elefant.public_events.logger_test_handler');

        $this->assertEquals(
            [
                ['addFormatter', [new Reference('custom_formatter')]],
                ['addFilter', [new Definition(NameFilter::class, ['/.*/'])]],
                ['setLogger', [new Reference('logger')]],
            ],
            $loggerHandler->getMethodCalls()
        );
    }

    public function testMultipleFormatters()
    {
        $container = ContainerFactory::createContainer('multiple_formatters.yml');

        /** @var Definition $loggerHandler */
        $loggerHandler = $container->getDefinition('elefant.public_events.logger_test_handler');

        $this->assertEquals(
            [
                ['addFormatter', [new Reference('custom_formatter')]],
                ['addFormatter', [new Reference('custom_formatter')]],
                ['addFormatter', [new Definition(MetadataFormatter::class)]],
                ['addFilter', [new Definition(NameFilter::class, ['/.*/'])]],
                ['setLogger', [new Reference('logger')]],
            ],
            $loggerHandler->getMethodCalls()
        );
    }
}
