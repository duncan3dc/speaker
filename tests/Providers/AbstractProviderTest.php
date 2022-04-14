<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\ProviderException;
use GuzzleHttp\ClientInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class AbstractProviderTest extends TestCase
{
    /** @var ExampleProvider */
    private $provider;

    /** @var ClientInterface|MockInterface */
    private $client;


    protected function setUp(): void
    {
        $this->client = Mockery::mock(ClientInterface::class);
        $this->provider = new ExampleProvider();
        $this->provider->setClient($this->client);
    }


    protected function tearDown(): void
    {
        Mockery::close();
    }


    public function testGetFormat(): void
    {
        $this->assertSame("mp3", $this->provider->getFormat());
    }


    public function testGetOptions(): void
    {
        $this->assertSame([], $this->provider->getOptions());
    }


    public function testGetClient(): void
    {
        $provider = new ExampleProvider();
        $this->assertInstanceOf(ClientInterface::class, $provider->getClient());
    }


    public function testSendRequest(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "http://example.com/?text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testSendRequestFailure(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("500");

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "http://example.com/?text=Hello")
            ->andReturn($response);

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("Failed to call the external text-to-speech service");
        $this->provider->textToSpeech("Hello");
    }
}
