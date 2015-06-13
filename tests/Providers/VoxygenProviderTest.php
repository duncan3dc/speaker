<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Providers\VoxygenProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use Mockery;

class VoxygenProviderTest extends \PHPUnit_Framework_TestCase
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
        $provider = new VoxygenProvider;
        $provider->setClient($this->client);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://www.voxygen.fr/sites/all/modules/voxygen_voices/assets/proxy/index.php?method=redirect&voice=Bronwen&text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testSetVoice()
    {
        $provider = new VoxygenProvider;
        $provider->setClient($this->client);

        $provider->setVoice("Jenny");

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://www.voxygen.fr/sites/all/modules/voxygen_voices/assets/proxy/index.php?method=redirect&voice=Jenny&text=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testSetVoiceFailure()
    {
        $provider = new VoxygenProvider;

        $this->setExpectedException("InvalidArgumentException", "Unexpected voice name (k), names should be at least 2 characters long");
        $provider->setVoice("k");
    }


    public function testGetOptions()
    {
        $provider = new VoxygenProvider;

        $options = [
            "voice"     =>  "Bronwen",
        ];

        $this->assertSame($options, $provider->getOptions());
    }


    public function testConstructorOptions()
    {
        $provider = new VoxygenProvider("Aaron");

        $options = [
            "voice"     =>  "Aaron",
        ];

        $this->assertSame($options, $provider->getOptions());
    }
}
