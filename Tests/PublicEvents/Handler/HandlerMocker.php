<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\FormatterInterface;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\JsonFormatter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\Handler;
use PHPUnit\Framework\TestCase;

class HandlerMocker
{
    public static function addFilterAndJsonFormatter(Handler $handler)
    {
        $handler
            ->addFilter(new NameFilter('/.*/'))
            ->setFormatter(new JsonFormatter());
    }

    public static function getMockFormatter(TestCase $testCase, $willReturn, ...$nextWillReturnValues)
    {
        $mockFormatter = $testCase->getMockBuilder(FormatterInterface::class)->getMock();
        $mockFormatter->expects($testCase->once())
            ->method('format')
            ->willReturn($willReturn, ...$nextWillReturnValues);

        return $mockFormatter;
    }
}