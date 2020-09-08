<?php

namespace Tests\api;

use App\api\WeatherForecast;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

use PHPUnit\Framework\TestCase;

require 'vendor/autoload.php';


class WeatherForecastTest extends TestCase
{
    protected $client;
    protected $handlerStack;
    protected $mock;
    protected $response;

    protected function setUp(): void
    {
        $this->setUpMock();

        $this->client = new Client([
            'handler' => $this->handlerStack
        ]);
    }

    protected function setUpMock(): void
    {
        $body_min = 'city XYZ country XYZ';

        $this->mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], $body_min),
            new Response(202, ['Content-Length' => 0]),
            new RequestException('Error Communicating with Server', new Request('GET', 'test'))]);

        $this->handlerStack = HandlerStack::create($this->mock);
    }

    public function testRequestHasSucceeded()
    {
        $this->response = $this->client->request('GET', '/');
        $this->assertEquals(200, $this->response->getStatusCode());

    }

    public function testRequestHasBeenReceived()
    {
        $this->response = $this->client->request('GET', '/');
        $this->assertEquals('city XYZ country XYZ', $this->response->getBody());
        $this->assertEquals(202, $this->client->request('GET', '/')->getStatusCode());
    }

    public function testRequestAndResourceHasBeenCreated()
    {
        $this->mock->reset();
        $this->mock->append(new Response(201));

        $this->assertEquals(201, $this->client->request('GET', '/')->getStatusCode());
    }

}