<?php

namespace duncan3dc\Speaker\Test\Providers;

use Aws\Polly\PollyClient;
use Aws\Result;
use duncan3dc\Speaker\Providers\AmazonPollyProvider;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class AmazonPollyProviderTest extends TestCase
{
    /**
     * @var AmazonPollyProvider $provider The provider to test.
     */
    private $provider;

    /**
     * @var PollyClient|MockInterface $client The amazon polly client.
     */
    private $client;


    public function setUp()
    {
        $this->client = Mockery::mock(PollyClient::class);
        $this->provider = new AmazonPollyProvider($this->client);
    }


    public function tearDown()
    {
        Mockery::close();
    }


    public function testTextToSpeech()
    {
        $result = Mockery::mock(Result::class);
        $result->shouldReceive("get")->with("AudioStream")->once()->andReturn("mp3");

        $this->client->shouldReceive("synthesizeSpeech")
            ->once()
            ->with(["OutputFormat" => "mp3", "Text" => "Hello", "VoiceId" => "Emma"])
            ->andReturn($result);

        $this->assertSame("mp3", $this->provider->textToSpeech("Hello"));
    }


    public function testWithVoice()
    {
        $provider = $this->provider->withVoice("Brian");

        # Ensure immutability
        $this->assertSame("Brian", $provider->getOptions()["voice"]);
        $this->assertSame("Emma", $this->provider->getOptions()["voice"]);

        $result = Mockery::mock(Result::class);
        $result->shouldReceive("get")->with("AudioStream")->once()->andReturn("mp3");

        $this->client->shouldReceive("synthesizeSpeech")
            ->once()
            ->with(["OutputFormat" => "mp3", "Text" => "Hello", "VoiceId" => "Brian"])
            ->andReturn($result);

        $this->assertSame("mp3", $provider->textToSpeech("Hello"));
    }


    public function testGetOptions()
    {
        $options = [
            "voice" =>  "Emma",
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testConstructorOptions1()
    {
        $provider = new AmazonPollyProvider($this->client, "Fred");

        $this->assertSame("Fred", $provider->getOptions()["voice"]);
    }
}
