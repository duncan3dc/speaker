<?php

namespace duncan3dc\Speaker\Providers;

use duncan3dc\Speaker\Exception;
use duncan3dc\Speaker\Providers\AbstractProvider;

/**
 * Convert a string of a text to a spoken word wav.
 */
class PicottsProvider extends AbstractProvider
{
    /**
     * @var string $pico The picotts program.
     */
    protected $pico;

    /**
     * @var string $language The language to use.
     */
    protected $language = "en-US";


    /**
     * Create a new instance.
     *
     * @param string $language The language to use
     */
    public function __construct($language = null)
    {
        $pico = trim(exec("which pico2wave"));
        if (!file_exists($pico)) {
            throw new Exception("Unable to find picotts program, please install pico2wave before trying again");
        }

        $this->pico = $pico;

        if ($language !== null) {
            $this->setLanguage($language);
        }
    }


    /**
     * Set the language to use.
     *
     * @param string $language The language to use (eg 'en')
     *
     * @return static
     */
    public function setLanguage($language)
    {
        if (strlen($language) === 2) {
			$language = strtolower(trim($language));
            $language = "{$language}-" . strtoupper($language);
        }

        if (!preg_match("/^[a-z]{2}-[A-Z]{2}$/", $language)) {
            throw new \InvalidArgumentException("Unexpected language code ({$language}), codes should be 2 characters, a hyphen, and a further 2 characters");
        }

        $this->language = $language;

        return $this;
    }


    /**
     * Get the format of this audio.
     *
     * @return string
     */
    public function getFormat()
    {
        return "wav";
    }


    /**
     * Get the current options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            "language"  =>  $this->language,
        ];
    }


    /**
     * Convert the specified text to audio.
     *
     * @param string $text The text to convert
     *
     * @return string The audio data
     */
    public function textToSpeech($text)
    {
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "speaker_picotts.wav";

        $cmd = escapeshellcmd($this->pico);
        $cmd .= " --wave=" . escapeshellarg($filename);
        $cmd .= " --lang=" . escapeshellarg($this->language);
        $cmd .= " " . escapeshellarg($text);

        exec($cmd, $output, $return);

        if ($return > 0) {
            throw new Exception("TextToSpeech " . implode("\n", $output));
        }

        if (!file_exists($filename)) {
            throw new Exception("TextToSpeech unable to create file: {$filename}");
        }

        $result = file_get_contents($filename);
        unlink($filename);

        return $result;
    }
}
