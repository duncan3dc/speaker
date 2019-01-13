<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Providers\GoogleProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class GoogleProviderTest extends TestCase
{
    private $provider;
    private $client;

    public function setUp()
    {
        $this->provider = new GoogleProvider();

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
            ->with("http://translate.google.com/translate_tts?q=Hello&tl=en&client=duncan3dc-speaker")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testWithLanguage()
    {
        $provider = $this->provider->withLanguage("fr");

        # Ensure immutability
        $this->assertSame("fr", $provider->getOptions()["language"]);
        $this->assertSame("en", $this->provider->getOptions()["language"]);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn("mp3");

        $this->client->shouldReceive("get")
            ->once()
            ->with("http://translate.google.com/translate_tts?q=Hello&tl=fr&client=duncan3dc-speaker")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithLanguageFailure()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (nope), codes should be 2 characters");
        $this->provider->withLanguage("nope");
    }


    public function testGetOptions()
    {
        $options = [
            "language"  =>  "en",
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testSendRequestFailure()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Only messages under 100 characters are supported");
        $this->provider->textToSpeech("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur accumsan laoreet sapien, eget posuere");
    }


    public function testConstructorOptions1()
    {
        $provider = new GoogleProvider("de");

        $this->assertSame("de", $provider->getOptions()["language"]);
    }
    public function testConstructorOptions2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (when), codes should be 2 characters");
        new GoogleProvider("when");
    }
}
