<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Providers\AcapelaProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class AcapelaProviderTest extends TestCase
{
    private $provider;
    private $client;

    public function setUp()
    {
        $this->provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD");

        $this->client = Mockery::mock(ClientInterface::class);
        $this->provider->setClient($this->client);
    }


    public function tearDown()
    {
        Mockery::close();
    }


    public function testTextToSpeech()
    {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://vaas.acapela-group.com/Services/FileMaker.mp3?prot_vers=2&cl_login=LOGIN&cl_app=APPLICATION&cl_pwd=PASSWORD&req_voice=rod22k&req_spd=180&req_text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testWithVoice()
    {
        $provider = $this->provider->withVoice("Peter");

        # Ensure immutability
        $this->assertSame("peter", $provider->getOptions()["voice"]);
        $this->assertSame("rod", $this->provider->getOptions()["voice"]);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://vaas.acapela-group.com/Services/FileMaker.mp3?prot_vers=2&cl_login=LOGIN&cl_app=APPLICATION&cl_pwd=PASSWORD&req_voice=peter22k&req_spd=180&req_text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithVoiceFailure()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected voice name (ke), names should be at least 3 characters long");
        $this->provider->withVoice("ke");
    }


    public function testWithSpeed()
    {
        $provider = $this->provider->withSpeed(260);

        # Ensure immutability
        $this->assertSame(260, $provider->getOptions()["speed"]);
        $this->assertSame(180, $this->provider->getOptions()["speed"]);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://vaas.acapela-group.com/Services/FileMaker.mp3?prot_vers=2&cl_login=LOGIN&cl_app=APPLICATION&cl_pwd=PASSWORD&req_voice=rod22k&req_spd=260&req_text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithSpeedFailure()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid speed (30), must be a number between 60 and 360");
        $this->provider->withSpeed(30);
    }


    public function testGetOptions()
    {
        $options = [
            "voice" =>  "rod",
            "speed" =>  180,
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testSendRequestFailure()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Only messages under 300 characters are supported");
        $this->provider->textToSpeech(str_repeat("A", 301));
    }


    public function testConstructorOptions1()
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD", "lucy", 190);

        $options = [
            "voice" =>  "lucy",
            "speed" =>  190,
        ];

        $this->assertSame($options, $provider->getOptions());
    }
    public function testConstructorOptions2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected voice name (no), names should be at least 3 characters long");
        new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD", "no");
    }
    public function testConstructorOptions3()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid speed (50), must be a number between 60 and 360");
        new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD", "fred", 50);
    }
}
