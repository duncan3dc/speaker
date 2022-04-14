<?php

namespace duncan3dc\Speaker\Test;

use duncan3dc\Speaker\Exceptions\RuntimeException;
use duncan3dc\Speaker\Providers\ProviderInterface;
use duncan3dc\Speaker\TextToSpeech;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

use function error_reporting;
use function file_get_contents;
use function unlink;

class TextToSpeechTest extends TestCase
{
    /** @var ProviderInterface&MockInterface  */
    private $provider;

    /** @var TextToSpeech */
    private $tts;


    protected function setUp(): void
    {
        $this->provider = Mockery::mock(ProviderInterface::class);
        $this->tts = new TextToSpeech("hello", $this->provider);
    }


    protected function tearDown(): void
    {
        Mockery::close();
    }


    public function testGetAudioData(): void
    {
        $this->provider->shouldReceive("textToSpeech")
            ->once()
            ->with("hello")
            ->andReturn("mp3");

        # Call it twice to ensure it only calls the provider method once
        $this->assertSame("mp3", $this->tts->getAudioData());
        $this->assertSame("mp3", $this->tts->getAudioData());
    }


    public function testGenerateFilename(): void
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


    public function testSave(): void
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


    public function testSaveFail(): void
    {
        error_reporting(E_ALL ^ E_WARNING);

        $this->provider->shouldReceive("textToSpeech")
            ->once()
            ->with("hello")
            ->andReturn("test-mp3-data");

        $path = "/no/such/path/test.mp3";

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Unable to save the file ({$path})");
        $this->tts->save($path);
    }


    public function testGetFile(): void
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
