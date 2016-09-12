<?php

namespace duncan3dc\Speaker\Test;

use duncan3dc\Speaker\Exception;
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

        $this->provider->shouldReceive("getFormat")
            ->once()
            ->andReturn("wav");

        $filename = $this->tts->generateFilename();

        $this->assertSame("5ee35ff512372af8cee8ddf79edec5ea.wav", $filename);
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


    public function testSaveFail()
    {
        error_reporting(E_ALL ^ E_WARNING);

        $this->provider->shouldReceive("textToSpeech")
            ->once()
            ->with("hello")
            ->andReturn("test-mp3-data");

        $path = "/no/such/path/test.mp3";

        $this->setExpectedException(Exception::class, "Unable to save the file ({$path})");
        $this->tts->save($path);
    }


    public function testGetFile()
    {
        $this->provider->shouldReceive("getFormat")
            ->twice()
            ->andReturn("mp3");

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
