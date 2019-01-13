<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Mock\CoreFunction;
use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Exceptions\ProviderException;
use duncan3dc\Speaker\Providers\PicottsProvider;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\ProcessBuilder;
use function file_put_contents;
use function is_file;
use function is_string;
use function substr;
use function sys_get_temp_dir;
use function unlink;

class PicottsProviderTest extends TestCase
{
    /** @var string|null */
    private $binary;


    public function tearDown()
    {
        if (is_string($this->binary) && is_file($this->binary)) {
            unlink($this->binary);
        }

        CoreFunction::close();
    }


    private function setupBinary(): void
    {
        $this->binary = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "pico2wave";

        file_put_contents($this->binary, "bin");

        CoreFunction::mock("exec")->once()->with("which pico2wave")->andReturn($this->binary);
    }


    private function getProvider(): PicottsProvider
    {
        $this->setupBinary();

        return new PicottsProvider();
    }


    public function testBinaryInstalled()
    {
        CoreFunction::mock("exec")->once()->with("which pico2wave")->andReturn("");

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("Unable to find picotts program, please install pico2wave before trying again");
        new PicottsProvider();
    }


    public function testTextToSpeech()
    {
        $provider = $this->getProvider();

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

        $result = $provider->textToSpeech("Hello", $process);
        $this->assertSame("test-data", $result);
    }


    public function testTextToSpeechUnknownLanguage()
    {
        $provider = $this->getProvider();

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

        $provider = $provider->withLanguage("zh-CN");

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("Unknown language: zh-CN");
        $provider->textToSpeech("Hello", $process);
    }


    public function testTextToSpeechError()
    {
        $provider = $this->getProvider();

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
        $provider->textToSpeech("Hello", $process);
    }


    public function testGetFormat()
    {
        $provider = $this->getProvider();
        $this->assertSame("wav", $provider->getFormat());
    }


    public function testWithLanguage()
    {
        $english = $this->getProvider();
        $french = $english->withLanguage("fr");

        $this->assertSame("fr-FR", $french->getOptions()["language"]);

        # Ensure immutability
        $this->assertSame("en-US", $english->getOptions()["language"]);
    }


    public function testWithLanguageFailure()
    {
        $provider = $this->getProvider();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (k), codes should be 2 characters, a hyphen, and a further 2 characters");
        $provider->withLanguage("k");
    }


    public function testGetOptions()
    {
        $provider = $this->getProvider();

        $options = [
            "language"  =>  "en-US",
        ];

        $this->assertSame($options, $provider->getOptions());
    }


    public function testConstructorOptions1()
    {
        $this->setupBinary();

        $provider = new PicottsProvider("fr");

        $this->assertSame("fr-FR", $provider->getOptions()["language"]);
    }
    public function testConstructorOptions2()
    {
        $this->setupBinary();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (nope), codes should be 2 characters, a hyphen, and a further 2 characters");
        new PicottsProvider("nope");
    }
}
