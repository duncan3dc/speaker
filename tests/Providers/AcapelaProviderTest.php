<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Providers\AcapelaProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use Mockery;

class AcapelaProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = Mockery::mock(Client::class);
    }


    public function tearDown()
    {
        Mockery::close();
    }


    public function testTextToSpeech()
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD");
        $provider->setClient($this->client);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://vaas.acapela-group.com/Services/FileMaker.mp3?prot_vers=2&cl_login=LOGIN&cl_app=APPLICATION&cl_pwd=PASSWORD&req_voice=rod22k&req_spd=180&req_text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testSetVoice()
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD");
        $provider->setClient($this->client);

        $provider->setVoice("Peter");

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://vaas.acapela-group.com/Services/FileMaker.mp3?prot_vers=2&cl_login=LOGIN&cl_app=APPLICATION&cl_pwd=PASSWORD&req_voice=peter22k&req_spd=180&req_text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testSetVoiceFailure()
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD");

        $this->setExpectedException("InvalidArgumentException", "Unexpected voice name (ke), names should be at least 3 characters long");
        $provider->setVoice("ke");
    }


    public function testSetSpeed()
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD");
        $provider->setClient($this->client);

        $provider->setSpeed(260);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://vaas.acapela-group.com/Services/FileMaker.mp3?prot_vers=2&cl_login=LOGIN&cl_app=APPLICATION&cl_pwd=PASSWORD&req_voice=rod22k&req_spd=260&req_text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testSetSpeedFailure()
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD");

        $this->setExpectedException("InvalidArgumentException", "Invalid speed (30), must be a number between 60 and 360");
        $provider->setSpeed(30);
    }


    public function testGetOptions()
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD");

        $options = [
            "voice" =>  "rod",
            "speed" =>  180,
        ];

        $this->assertSame($options, $provider->getOptions());
    }


    public function testConstructorOptions()
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD", "lucy", 190);

        $options = [
            "voice" =>  "lucy",
            "speed" =>  190,
        ];

        $this->assertSame($options, $provider->getOptions());
    }


    public function testSendRequestFailure()
    {
        $provider = new AcapelaProvider("LOGIN", "APPLICATION", "PASSWORD");

        $this->setExpectedException("InvalidArgumentException", "Only messages under 300 characters are supported");
        $provider->textToSpeech(str_repeat("A", 301));
    }
}
