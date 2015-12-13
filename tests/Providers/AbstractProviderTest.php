<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use Mockery;

class AbstractProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $client;
    protected $provider;

    public function setUp()
    {
        $this->client = Mockery::mock(Client::class);
        $this->provider = new ExampleProvider;
        $this->provider->setClient($this->client);
    }


    public function tearDown()
    {
        Mockery::close();
    }


    public function testGetFormat()
    {
        $this->assertSame("mp3", $this->provider->getFormat());
    }


    public function testGetOptions()
    {
        $this->assertSame([], $this->provider->getOptions());
    }


    public function testGetClient()
    {
        $provider = new ExampleProvider;
        $this->assertInstanceOf(Client::class, $provider->getClient());
    }


    public function testSendRequest()
    {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://example.com/?text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testSendRequestFailure()
    {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("500");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://example.com/?text=Hello")
            ->andReturn($response);

        $this->setExpectedException(Exception::class, "Failed to call the external text-to-speech service");
        $this->provider->textToSpeech("Hello");
    }
}
