<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Exec\FactoryInterface;
use duncan3dc\Exec\ProgramInterface;
use duncan3dc\Exec\ResultInterface;
use duncan3dc\Mock\CoreFunction;
use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Exceptions\ProviderException;
use duncan3dc\Speaker\Providers\PicottsProvider;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

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

    /** @var FactoryInterface&MockInterface */
    private $factory;

    /** @var ResultInterface&MockInterface */
    private $result;


    public function setUp(): void
    {
        $this->factory = Mockery::mock(FactoryInterface::class);
        $this->result = Mockery::mock(ResultInterface::class);
    }


    public function tearDown(): void
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


    /**
     * @return ProgramInterface&MockInterface
     */
    private function getProgram(): ProgramInterface
    {
        $program = Mockery::mock(ProgramInterface::class);

        $this->factory->shouldReceive("make")->once()->with($this->binary)->andReturn($program);

        return $program;
    }


    public function testBinaryInstalled(): void
    {
        CoreFunction::mock("exec")->once()->with("which pico2wave")->andReturn("");

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("Unable to find picotts program, please install pico2wave before trying again");
        new PicottsProvider();
    }


    public function testTextToSpeech(): void
    {
        $provider = $this->getProvider();
        $program = $this->getProgram();

        # Get the specified filename and write some test data to it
        $program->shouldReceive("getResult")->with(Mockery::on(function ($option) {
            if (substr($option, 0, 7) === "--wave=") {
                $filename = substr($option, 7);
                file_put_contents($filename, "test-data");
                return true;
            }
            return false;
        }), "--lang=en-US", "Hello")->andReturn($this->result);

        $this->result->shouldReceive("getStatus")->once()->with()->andReturn(0);

        $result = $provider->textToSpeech("Hello", $this->factory);
        $this->assertSame("test-data", $result);
    }


    public function testTextToSpeechUnknownLanguage(): void
    {
        $provider = $this->getProvider();
        $program = $this->getProgram();

        $program->shouldReceive("getResult")->with(Mockery::type("string"), "--lang=zh-CN", "Hello")->andReturn($this->result);

        $this->result->shouldReceive("getStatus")->once()->with()->andReturn(1);
        $this->result->shouldReceive("getFirstLine")->once()->with()->andReturn("Unknown language: zh-CN");

        $provider = $provider->withLanguage("zh-CN");

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("Unknown language: zh-CN");
        $provider->textToSpeech("Hello", $this->factory);
    }


    public function testTextToSpeechError(): void
    {
        $provider = $this->getProvider();
        $program = $this->getProgram();

        $program->shouldReceive("getResult")->with(Mockery::type("string"), "--lang=en-US", "Hello")->andReturn($this->result);

        $this->result->shouldReceive("getStatus")->once()->with()->andReturn(0);

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage("TextToSpeech unable to create file: /tmp/speaker_picotts.wav");
        $provider->textToSpeech("Hello", $this->factory);
    }


    public function testGetFormat(): void
    {
        $provider = $this->getProvider();
        $this->assertSame("wav", $provider->getFormat());
    }


    public function testWithLanguage(): void
    {
        $english = $this->getProvider();
        $french = $english->withLanguage("fr");

        $this->assertSame("fr-FR", $french->getOptions()["language"]);

        # Ensure immutability
        $this->assertSame("en-US", $english->getOptions()["language"]);
    }


    public function testWithLanguageFailure(): void
    {
        $provider = $this->getProvider();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (k), codes should be 2 characters, a hyphen, and a further 2 characters");
        $provider->withLanguage("k");
    }


    public function testGetOptions(): void
    {
        $provider = $this->getProvider();

        $options = [
            "language"  =>  "en-US",
        ];

        $this->assertSame($options, $provider->getOptions());
    }


    public function testConstructorOptions1(): void
    {
        $this->setupBinary();

        $provider = new PicottsProvider("fr");

        $this->assertSame("fr-FR", $provider->getOptions()["language"]);
    }
    public function testConstructorOptions2(): void
    {
        $this->setupBinary();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected language code (nope), codes should be 2 characters, a hyphen, and a further 2 characters");
        new PicottsProvider("nope");
    }
}
