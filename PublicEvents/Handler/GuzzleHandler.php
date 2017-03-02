<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

class GuzzleHandler extends Handler
{
    /** @var  ClientInterface */
    private $client;
    /** @var  string */
    private $method;
    /** @var  string */
    private $uri;
    /** @var  array */
    private $headers;

    /**
     * @param ClientInterface $client
     * @param string $method
     * @param string $uri
     * @param array $headers
     */
    public function __construct(ClientInterface $client, $method = 'get', $uri = '/', array $headers = [])
    {
        $this->client = $client;
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
    }


    protected function doHandle($data)
    {
        $body = json_encode($data);
        $request = new Request($this->method, $this->uri, $this->headers, $body);
        $this->client->send($request);
    }
}
