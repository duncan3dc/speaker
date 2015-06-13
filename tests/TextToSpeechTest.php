<?php

namespace duncan3dc\Speaker\Test;

use duncan3dc\Speaker\Providers\ProviderInterface;
use duncan3dc\Speaker\TextToSpeech;
use Mockery;

class TextToSpeechTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;
    protected $tts;

    public function setUp()
    {
        $this->provider = Mockery::mock(ProviderInterface::class);
        $this->tts = new TextToSpeech("hello", $this->provider);
    }


    public function tearDown()
    {
        Mockery::close();
    }


    public function testGetAudioData()
    {
        $this->provider->shouldReceive("textToSpeech")
            ->once()
            ->with("hello")
            ->andReturn("mp3");

        # Call it twice to ensure it only calls the provider method once
        $this->assertSame("mp3", $this->tts->getAudioData());
        $this->assertSame("mp3", $this->tts->getAudioData());
    }


    public function testGenerateFilename()
    {
        $this->provider->shouldReceive("getOptions")
            ->once()
            ->andReturn(["language" => "en"]);

        $reflected = new \ReflectionClass($this->tts);
        $method = $reflected->getMethod("generateFilename");
        $method->setAccessible(true);
        $filename = $method->invoke($this->tts);

        $this->assertSame("5ee35ff512372af8cee8ddf79edec5ea.mp3", $filename);
    }


    public function testSave()
    {
        $this->provider->shouldReceive("textToSpeech")
            ->once()
            ->with("hello")
            ->andReturn("test-mp3-data");

        $tmp = "/tmp/test.mp3";

        $this->tts->save($tmp);

        $this->assertSame("test-mp3-data", file_get_contents($tmp));
        unlink($tmp);
    }


    public function testGetFile()
    {
        $this->provider->shouldReceive("getOptions")
            ->twice()
            ->andReturn(["language" => "en"]);

        $this->provider->shouldReceive("textToSpeech")
            ->once()
            ->with("hello")
            ->andReturn("test-mp3-data");

        $tmp = "/tmp/5ee35ff512372af8cee8ddf79edec5ea.mp3";

        $filename = $this->tts->getFile();
        $this->assertSame($tmp, $filename);
        $this->assertSame("test-mp3-data", file_get_contents($filename));

        $filename = $this->tts->getFile();
        $this->assertSame($tmp, $filename);

        unlink($tmp);
    }
}
