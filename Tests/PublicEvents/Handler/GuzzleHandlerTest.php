<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Handler\GuzzleHandler;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

class GuzzleHandlerTest extends TestCase
{
    public function testGuzzleHandler()
    {
        $client = $this->getMockBuilder(ClientInterface::class)->getMock();

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Request $request) {
                return
                    $request->getMethod() === 'GET' &&
                    (string)$request->getUri() === '/test_api' &&
                    (string)$request->getBody() === '{"event_name":"test_event","event":"serialized body"}';
            }));

        $guzzleHandler = new GuzzleHandler($client, 'get', '/test_api');
        HandlerMocker::addAllFilterAndSerializer($this, $guzzleHandler);
        $guzzleHandler->handle('test_event', new Event());
    }
}
