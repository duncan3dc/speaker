<?php

namespace duncan3dc\Speaker\Test;

use duncan3dc\Speaker\TextToSpeech;
use Mockery;
use Regatta\Hive\Files\Filesystem;

class TextToSpeechTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;
    protected $tts;

    public function setUp()
    {
        $this->provider = Mockery::mock("duncan3dc\\Speaker\\Providers\\ProviderInterface");
    }


    public function tearDown()
    {
        Mockery::close();
    }


    public function testGetAudioData()
    {
        $tts = new TextToSpeech("hello", $this->provider);

        $this->provider->shouldReceive("textToSpeech")
            ->once()
            ->with("hello")
            ->andReturn("mp3");

        # Call it twice to ensure it only calls the provider method once
        $this->assertSame("mp3", $tts->getAudioData());
        $this->assertSame("mp3", $tts->getAudioData());
    }
}
