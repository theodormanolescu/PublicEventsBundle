<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use Elefant\PublicEventsBundle\PublicEvents\Handler\GuzzleHandler;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Serializer;

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
                    (string)$request->getBody() === '[]';
            }));

        $guzzleHandler = new GuzzleHandler($client, 'get', '/test_api');
        $guzzleHandler->addFilter(new NameFilter('/.*/'));
        $guzzleHandler->handle(new PublicEvent('test_event', new Event()));
    }
}
