<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Exceptions\ProviderException;
use duncan3dc\Speaker\Providers\VoiceRssProvider;
use GuzzleHttp\ClientInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class VoiceRssProviderTest extends TestCase
{
    /** @var VoiceRssProvider */
    private $provider;

    /** @var ClientInterface|MockInterface */
    private $client;

    protected function setUp(): void
    {
        $this->provider = new VoiceRssProvider("APIKEY");

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
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "https://api.voicerss.org/?key=APIKEY&src=Hello&hl=en-gb&r=0&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testTextToSpeechFailure(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("ERROR: Test Message");

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "https://api.voicerss.org/?key=APIKEY&src=Hello&hl=en-gb&r=0&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("TextToSpeech ERROR: Test Message");
        $this->provider->textToSpeech("Hello");
    }


    public function testWithLanguage(): void
    {
        $provider = $this->provider->withLanguage("fr");

        # Ensure immutability
        $this->assertSame("fr-fr", $provider->getOptions()["language"]);
        $this->assertSame("en-gb", $this->provider->getOptions()["language"]);

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "https://api.voicerss.org/?key=APIKEY&src=Hello&hl=fr-fr&r=0&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithLanguageFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (nope), codes should be 2 characters");
        $this->provider->withLanguage("nope");
    }


    public function testWithSpeed(): void
    {
        $provider = $this->provider->withSpeed(-5);

        # Ensure immutability
        $this->assertSame(-5, $provider->getOptions()["speed"]);
        $this->assertSame(0, $this->provider->getOptions()["speed"]);

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "https://api.voicerss.org/?key=APIKEY&src=Hello&hl=en-gb&r=-5&c=MP3&f=16khz_16bit_stereo")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithSpeedFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid speed (11), must be a number between -10 and 10");
        $this->provider->withSpeed(11);
    }


    public function testGetOptions(): void
    {
        $options = [
            "language"  =>  "en-gb",
            "speed"     =>  0,
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testConstructorOptions1(): void
    {
        $provider = new VoiceRssProvider("APIKEY", "ab-cd", 10);

        $options = [
            "language"  =>  "ab-cd",
            "speed"     =>  10,
        ];

        $this->assertSame($options, $provider->getOptions());
    }
    public function testConstructorOptions2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (who?), codes should be 2 characters");
        new VoiceRssProvider("APIKEY", "who?");
    }
    public function testConstructorOptions3(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid speed (999), must be a number between -10 and 10");
        new VoiceRssProvider("APIKEY", "en", 999);
    }
}
