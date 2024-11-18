<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Providers\GoogleProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Utils;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class GoogleProviderTest extends TestCase
{
    /** @var GoogleProvider */
    private $provider;

    /** @var ClientInterface|MockInterface */
    private $client;

    protected function setUp(): void
    {
        $this->provider = new GoogleProvider();

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
            ->with("GET", "http://translate.google.com/translate_tts?q=Hello&tl=en&client=duncan3dc-speaker")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testWithLanguage(): void
    {
        $provider = $this->provider->withLanguage("fr");

        # Ensure immutability
        $this->assertSame("fr", $provider->getOptions()["language"]);
        $this->assertSame("en", $this->provider->getOptions()["language"]);

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive("getStatusCode")->once()->andReturn("200");
        $response->shouldReceive("getBody")->once()->andReturn(Utils::streamFor("mp3"));

        $this->client->shouldReceive("request")
            ->once()
            ->with("GET", "http://translate.google.com/translate_tts?q=Hello&tl=fr&client=duncan3dc-speaker")
            ->andReturn($response);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testWithLanguageFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (nope), codes should be 2 characters");
        $this->provider->withLanguage("nope");
    }


    public function testGetOptions(): void
    {
        $options = [
            "language"  =>  "en",
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testSendRequestFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Only messages under 100 characters are supported");
        $this->provider->textToSpeech("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur accumsan laoreet sapien, eget posuere");
    }


    public function testConstructorOptions1(): void
    {
        $provider = new GoogleProvider("de");

        $this->assertSame("de", $provider->getOptions()["language"]);
    }
    public function testConstructorOptions2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (when), codes should be 2 characters");
        new GoogleProvider("when");
    }
}
