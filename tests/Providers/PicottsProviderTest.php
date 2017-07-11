<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exception;
use duncan3dc\Speaker\Providers\PicottsProvider;
use Mockery;
use Symfony\Component\Process\ProcessBuilder;

class PicottsProviderTest extends \PHPUnit_Framework_TestCase
{
    private $binary;

    public function setUp()
    {
        $this->binary = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "pico2wave";

        file_put_contents($this->binary, "bin");

        Handlers::handle("exec", function () {
            return $this->binary;
        });
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

        $this->setExpectedException(Exception::class, "Unable to find picotts program, please install pico2wave before trying again");
        $provider = new PicottsProvider;
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

        $provider = new PicottsProvider;
        $result = $provider->textToSpeech("Hello", $process);
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

        $provider = new PicottsProvider;
        $provider->setLanguage("zh-CN");

        $this->setExpectedException(Exception::class, "Unknown language: zh-CN");
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

        $provider = new PicottsProvider;

        $this->setExpectedException(Exception::class, "TextToSpeech unable to create file: /tmp/speaker_picotts.wav");
        $provider->textToSpeech("Hello", $process);
    }


    public function testGetFormat()
    {
        $provider = new PicottsProvider;

        $this->assertSame("wav", $provider->getFormat());
    }


    public function testSetLanguage()
    {
        $provider = new PicottsProvider;

        $provider->setLanguage("fr");

        $options = [
            "language"  =>  "fr-FR",
        ];

        $this->assertSame($options, $provider->getOptions());
    }


    public function testSetLanguageFailure()
    {
        $provider = new PicottsProvider;

        $this->setExpectedException("InvalidArgumentException", "Unexpected language code (k), codes should be 2 characters, a hyphen, and a further 2 characters");
        $provider->setLanguage("k");
    }


    public function testGetOptions()
    {
        $provider = new PicottsProvider;

        $options = [
            "language"  =>  "en-US",
        ];

        $this->assertSame($options, $provider->getOptions());
    }


    public function testConstructorOptions()
    {
        $provider = new PicottsProvider("FR-fr");

        $options = [
            "language"  =>  "fr-FR",
        ];

        $this->assertSame($options, $provider->getOptions());
    }
}
