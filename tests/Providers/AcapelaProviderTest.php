<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Providers\AcapelaProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Utils;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class AcapelaProviderTest extends TestCase
{
    /** @var AcapelaProvider */
    private $provider;

    /** @var ClientInterface|MockInterface */
    private $client;

    protected function setUp(): void
    {
        $this->provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD");

        $this->client = Mockery::mock(ClientInterface::class);
        $this->provider->setClient($this->client);
    }


    protected function tearDown(): void
    {
        Mockery::close();
    }


    public function testTextToSpeech(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn(Utils::streamFor("mp3"));

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "http://vaas.acapela-group.com/Services/FileMaker.mp3?prot_vers=2&cl_login=LOGIN&cl_app=APPLICATION&cl_pwd=PASSWORD&req_voice=rod22k&req_spd=180&req_text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testWithVoice(): void
    {
        $provider = $this->provider->withVoice("Peter");

        # Ensure immutability
        $this->assertSame("peter", $provider->getOptions()["voice"]);
        $this->assertSame("rod", $this->provider->getOptions()["voice"]);

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn(Utils::streamFor("mp3"));

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "http://vaas.acapela-group.com/Services/FileMaker.mp3?prot_vers=2&cl_login=LOGIN&cl_app=APPLICATION&cl_pwd=PASSWORD&req_voice=peter22k&req_spd=180&req_text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithVoiceFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected voice name (ke), names should be at least 3 characters long");
        $this->provider->withVoice("ke");
    }


    public function testWithSpeed(): void
    {
        $provider = $this->provider->withSpeed(260);

        # Ensure immutability
        $this->assertSame(260, $provider->getOptions()["speed"]);
        $this->assertSame(180, $this->provider->getOptions()["speed"]);

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn(Utils::streamFor("mp3"));

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "http://vaas.acapela-group.com/Services/FileMaker.mp3?prot_vers=2&cl_login=LOGIN&cl_app=APPLICATION&cl_pwd=PASSWORD&req_voice=rod22k&req_spd=260&req_text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithSpeedFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid speed (30), must be a number between 60 and 360");
        $this->provider->withSpeed(30);
    }


    public function testGetOptions(): void
    {
        $options = [
            "voice" =>  "rod",
            "speed" =>  180,
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testSendRequestFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Only messages under 300 characters are supported");
        $this->provider->textToSpeech(str_repeat("A", 301));
    }


    public function testConstructorOptions1(): void
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD", "lucy", 190);

        $options = [
            "voice" =>  "lucy",
            "speed" =>  190,
        ];

        $this->assertSame($options, $provider->getOptions());
    }
    public function testConstructorOptions2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected voice name (no), names should be at least 3 characters long");
        new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD", "no");
    }
    public function testConstructorOptions3(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid speed (50), must be a number between 60 and 360");
        new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD", "fred", 50);
    }
}
