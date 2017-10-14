<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Exceptions\ProviderException;
use duncan3dc\Speaker\Providers\VoiceRssProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class VoiceRssProviderTest extends TestCase
{
    private $provider;
    private $client;

    public function setUp()
    {
        $this->provider = new VoiceRssProvider("APIKEY");

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
            ->with("https://api.voicerss.org/?key=APIKEY&src=Hello&hl=en-gb&r=0&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testTextToSpeechFailure()
    {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("ERROR: Test Message");

        $this->client->shouldReceive("get")
            ->once()
            ->with("https://api.voicerss.org/?key=APIKEY&src=Hello&hl=en-gb&r=0&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("TextToSpeech ERROR: Test Message");
        $this->provider->textToSpeech("Hello");
    }


    public function testWithLanguage()
    {
        $provider = $this->provider->withLanguage("fr");

        # Ensure immutability
        $this->assertSame("fr-fr", $provider->getOptions()["language"]);
        $this->assertSame("en-gb", $this->provider->getOptions()["language"]);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("https://api.voicerss.org/?key=APIKEY&src=Hello&hl=fr-fr&r=0&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithLanguageFailure()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (nope), codes should be 2 characters");
        $this->provider->withLanguage("nope");
    }


    public function testWithSpeed()
    {
        $provider = $this->provider->withSpeed(-5);

        # Ensure immutability
        $this->assertSame(-5, $provider->getOptions()["speed"]);
        $this->assertSame(0, $this->provider->getOptions()["speed"]);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("https://api.voicerss.org/?key=APIKEY&src=Hello&hl=en-gb&r=-5&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithSpeedFailure()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid speed (11), must be a number between -10 and 10");
        $this->provider->withSpeed(11);
    }


    public function testGetOptions()
    {
        $options = [
            "language"  =>  "en-gb",
            "speed"     =>  0,
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testConstructorOptions1()
    {
        $provider = new VoiceRssProvider("APIKEY", "ab-cd", 10);

        $options = [
            "language"  =>  "ab-cd",
            "speed"     =>  10,
        ];

        $this->assertSame($options, $provider->getOptions());
    }
    public function testConstructorOptions2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (who?), codes should be 2 characters");
        new VoiceRssProvider("APIKEY", "who?");
    }
    public function testConstructorOptions3()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid speed (999), must be a number between -10 and 10");
        new VoiceRssProvider("APIKEY", "en", 999);
    }
}
