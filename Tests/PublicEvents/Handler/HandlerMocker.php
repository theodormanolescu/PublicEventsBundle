<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\Handler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class HandlerMocker
{
    public static function addAllFilterAndSerializer(TestCase $testCase, Handler $handler)
    {
        $serializer = (new \PHPUnit_Framework_MockObject_MockBuilder($testCase, SerializerInterface::class))->getMock();
        $serializer->expects($testCase->any())
            ->method('serialize')
            ->willReturn('serialized body');

        $handler->addFilter(new NameFilter('/.*/'));
        $handler->setSerializer($serializer);
    }
}