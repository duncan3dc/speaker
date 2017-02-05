<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Exception;
use duncan3dc\Speaker\Providers\PicottsProvider;

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
        $provider = new PicottsProvider;

        Handlers::handle("exec", function ($command, &$output = [], $return = 0) {
            preg_match("/\-\-wave='([a-z_\/\.]+)'/", $command, $matches);
            $filename = $matches[1];
            file_put_contents($filename, "test");
        });

        $this->assertSame("test", $provider->textToSpeech("Hello"));
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
