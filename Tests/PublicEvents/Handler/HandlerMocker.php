<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Formatter\FormatterInterface;
use PHPUnit\Framework\TestCase;

class HandlerMocker
{
    /**
     * @param TestCase $testCase
     * @param $willReturn
     * @param array ...$nextWillReturnValues
     * @return FormatterInterface
     */
    public static function getMockFormatter(TestCase $testCase, $willReturn, ...$nextWillReturnValues)
    {
        $mockFormatter = $testCase->getMockBuilder(FormatterInterface::class)->getMock();
        $mockFormatter->expects($testCase->any())
            ->method('format')
            ->willReturn($willReturn, ...$nextWillReturnValues);

        return $mockFormatter;
    }
}
