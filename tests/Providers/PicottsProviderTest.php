<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Exceptions\ProviderException;
use duncan3dc\Speaker\Providers\PicottsProvider;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\ProcessBuilder;

class PicottsProviderTest extends TestCase
{
    private $provider;
    private $binary;

    public function setUp()
    {
        $this->binary = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "pico2wave";

        file_put_contents($this->binary, "bin");

        Handlers::handle("exec", function () {
            return $this->binary;
        });

        $this->provider = new PicottsProvider();
    }


    public function tearDown()
    {
        unlink($this->binary);

        Handlers::clear();
    }


    public function testBinaryInstalled()
    {
        Handlers::handle("exec", function () {
            return "";
        });

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("Unable to find picotts program, please install pico2wave before trying again");
        $provider = new PicottsProvider();
    }


    public function testTextToSpeech()
    {
        $process = Mockery::mock(ProcessBuilder::class);

        # Get the specified filename and write some test data to it
        $process->shouldReceive("add")->with(Mockery::on(function ($option) {
            if (substr($option, 0, 7) === "--wave=") {
                $filename = substr($option, 7);
                file_put_contents($filename, "test-data");
                return true;
            }
            return false;
        }))->andReturn($process);

        $process->shouldReceive("setPrefix")->with($this->binary)->andReturn($process);
        $process->shouldReceive("add")->with("--lang=en-US")->andReturn($process);
        $process->shouldReceive("add")->with("Hello")->andReturn($process);
        $process->shouldReceive("getProcess")->withNoArgs()->andReturn($process);
        $process->shouldReceive("run")->withNoArgs()->andReturn(0);
        $process->shouldReceive("isSuccessful")->withNoArgs()->andReturn(true);

        $result = $this->provider->textToSpeech("Hello", $process);
        $this->assertSame("test-data", $result);
    }


    public function testTextToSpeechUnknownLanguage()
    {
        $process = Mockery::mock(ProcessBuilder::class);

        # Get the specified filename and write some test data to it
        $process->shouldReceive("add")->with(Mockery::on(function ($option) {
            if (substr($option, 0, 7) === "--wave=") {
                $filename = substr($option, 7);
                file_put_contents($filename, "test-data");
                return true;
            }
            return false;
        }))->andReturn($process);

        $process->shouldReceive("setPrefix")->with($this->binary)->andReturn($process);
        $process->shouldReceive("add")->with("--lang=zh-CN")->andReturn($process);
        $process->shouldReceive("add")->with("Hello")->andReturn($process);
        $process->shouldReceive("getProcess")->withNoArgs()->andReturn($process);
        $process->shouldReceive("run")->withNoArgs()->andReturn(1);
        $process->shouldReceive("isSuccessful")->withNoArgs()->andReturn(false);
        $process->shouldReceive("getErrorOutput")->withNoArgs()->andReturn("Unknown language: zh-CN\nextra boring stuff");

        $provider = $this->provider->withLanguage("zh-CN");

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("Unknown language: zh-CN");
        $provider->textToSpeech("Hello", $process);
    }


    public function testTextToSpeechError()
    {
        $process = Mockery::mock(ProcessBuilder::class);

        $process->shouldReceive("setPrefix")->with($this->binary)->andReturn($process);
        $process->shouldReceive("add")->with(Mockery::on(function ($option) {
            return (substr($option, 0, 7) === "--wave=");
        }))->andReturn($process);
        $process->shouldReceive("add")->with("--lang=en-US")->andReturn($process);
        $process->shouldReceive("add")->with("Hello")->andReturn($process);
        $process->shouldReceive("getProcess")->withNoArgs()->andReturn($process);
        $process->shouldReceive("run")->withNoArgs()->andReturn(0);
        $process->shouldReceive("isSuccessful")->withNoArgs()->andReturn(true);

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("TextToSpeech unable to create file: /tmp/speaker_picotts.wav");
        $this->provider->textToSpeech("Hello", $process);
    }


    public function testGetFormat()
    {
        $this->assertSame("wav", $this->provider->getFormat());
    }


    public function testWithLanguage()
    {
        $provider = $this->provider->withLanguage("fr");

        $this->assertSame("fr-FR", $provider->getOptions()["language"]);

        # Ensure immutability
        $this->assertSame("en-US", $this->provider->getOptions()["language"]);
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
            "language"  =>  "en-US",
        ];

        $this->assertSame($options, $this->provider->getOptions());
    }


    public function testConstructorOptions1()
    {
        $provider = new PicottsProvider("fr");

        $this->assertSame("fr-FR", $provider->getOptions()["language"]);
    }
    public function testConstructorOptions2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (nope), codes should be 2 characters, a hyphen, and a further 2 characters");
        new PicottsProvider("nope");
    }
}
