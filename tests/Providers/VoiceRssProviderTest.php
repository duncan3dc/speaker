<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Providers\VoiceRssProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use Mockery;

class VoiceRssProviderTest extends \PHPUnit_Framework_TestCase
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
        $provider = new VoiceRssProvider("APIKEY");
        $provider->setClient($this->client);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("https://api.voicerss.org/?key=APIKEY&src=Hello&hl=en-gb&r=0&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testTextToSpeechFailure()
    {
        $provider = new VoiceRssProvider("APIKEY");
        $provider->setClient($this->client);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("ERROR: Test Message");

        $this->client->shouldReceive("get")
            ->once()
            ->with("https://api.voicerss.org/?key=APIKEY&src=Hello&hl=en-gb&r=0&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->setExpectedException("duncan3dc\Speaker\Exception", "TextToSpeech ERROR: Test Message");
        $provider->textToSpeech("Hello");
    }


    public function testSetLanguage()
    {
        $provider = new VoiceRssProvider("APIKEY");
        $provider->setClient($this->client);

        $provider->setLanguage("fr");

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("https://api.voicerss.org/?key=APIKEY&src=Hello&hl=fr-fr&r=0&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testSetLanguageFailure()
    {
        $provider = new VoiceRssProvider("APIKEY");

        $this->setExpectedException("InvalidArgumentException", "Unexpected language code (nope), codes should be 2 characters");
        $provider->setLanguage("nope");
    }


    public function testSetSpeed()
    {
        $provider = new VoiceRssProvider("APIKEY");
        $provider->setClient($this->client);

        $provider->setSpeed(-5);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("https://api.voicerss.org/?key=APIKEY&src=Hello&hl=en-gb&r=-5&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testSetSpeedFailure()
    {
        $provider = new VoiceRssProvider("APIKEY");

        $this->setExpectedException("InvalidArgumentException", "Invalid speed (11), must be a number between -10 and 10");
        $provider->setSpeed(11);
    }


    public function testGetOptions()
    {
        $provider = new VoiceRssProvider("APIKEY");

        $options = [
            "language"  =>  "en-gb",
            "speed"     =>  0,
        ];

        $this->assertSame($options, $provider->getOptions());
    }


    public function testConstructorOptions()
    {
        $provider = new VoiceRssProvider("APIKEY", "ab-cd", 10);

        $options = [
            "language"  =>  "ab-cd",
            "speed"     =>  10,
        ];

        $this->assertSame($options, $provider->getOptions());
    }
}
