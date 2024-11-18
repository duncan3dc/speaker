<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Providers\ResponsiveVoiceProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Utils;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponsiveVoiceProviderTest extends TestCase
{
    /** @var ResponsiveVoiceProvider */
    private $provider;

    /** @var ClientInterface|MockInterface */
    private $client;

    protected function setUp(): void
    {
        $this->provider = new ResponsiveVoiceProvider();

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
            ->with("GET", "https://code.responsivevoice.org/getvoice.php?tl=en-GB&t=Hello")
            ->andReturn($response);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testWithLanguage(): void
    {
        $provider = $this->provider->withLanguage("ru");

        $this->assertSame("ru-RU", $provider->getOptions()["language"]);

        # Ensure immutability
        $this->assertSame("en-GB", $this->provider->getOptions()["language"]);
    }


    public function testWithLanguageFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (k), codes should be 2 characters, a hyphen, and a further 2 characters");
        $this->provider->withLanguage("k");
    }


    public function testGetOptions(): void
    {
        $options = [
            "language"  =>  "en-GB",
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testConstructorOptions1(): void
    {
        $provider = new ResponsiveVoiceProvider("de-de");

        $this->assertSame("de-DE", $provider->getOptions()["language"]);
    }
    public function testConstructorOptions2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (where), codes should be 2 characters, a hyphen, and a further 2 characters");
        new ResponsiveVoiceProvider("where");
    }
}
