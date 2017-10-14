<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Providers\ResponsiveVoiceProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class ResponsiveVoiceProviderTest extends TestCase
{
    private $provider;
    private $client;

    public function setUp()
    {
        $this->provider = new ResponsiveVoiceProvider;

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
            ->with("https://code.responsivevoice.org/getvoice.php?tl=en-GB&t=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testWithLanguage()
    {
        $provider = $this->provider->withLanguage("ru");

        $this->assertSame("ru-RU", $provider->getOptions()["language"]);

        # Ensure immutability
        $this->assertSame("en-GB", $this->provider->getOptions()["language"]);
    }


    public function testWithLanguageFailure()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (k), codes should be 2 characters, a hyphen, and a further 2 characters");
        $this->provider->withLanguage("k");
    }


    public function testGetOptions()
    {
        $options = [
            "language"  =>  "en-GB",
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testConstructorOptions1()
    {
        $provider = new ResponsiveVoiceProvider("de-de");

        $this->assertSame("de-DE", $provider->getOptions()["language"]);
    }
    public function testConstructorOptions2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (where), codes should be 2 characters, a hyphen, and a further 2 characters");
        new ResponsiveVoiceProvider("where");
    }
}
