<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class AbstractProviderTest extends TestCase
{
    private $client;
    private $provider;

    public function setUp()
    {
        $this->client = Mockery::mock(ClientInterface::class);
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
        $this->assertInstanceOf(ClientInterface::class, $provider->getClient());
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

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to call the external text-to-speech service");
        $this->provider->textToSpeech("Hello");
    }
}
