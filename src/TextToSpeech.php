<?php

namespace duncan3dc\Speaker;

use duncan3dc\Speaker\Providers\ProviderInterface;

/**
 * Convert a string of a text to a spoken word mp3.
 */
class TextToSpeech
{
    /**
     * @var string $text The text to convert.
     */
    protected $text;

    /**
     * @var ProviderInterface $provider The provider instance to handle text conversion.
     */
    protected $provider;

    /**
     * @var string $data The mp3 audio.
     */
    protected $data;

    /**
     * Create a new instance.
     *
     * @param string $text The text to convert
     * @param Directory $directory The directory to store the mp3 in.
     */
    public function __construct($text, ProviderInterface $provider)
    {
        $this->text = $text;
        $this->provider = $provider;
    }


    /**
     * Get the mp3 audio for this text.
     *
     * @return string The mp3 audio data
     */
    public function getAudioData()
    {
        if ($this->data === null) {
            $this->data = $this->provider->textToSpeech($this->text);
        }

        return $this->data;
    }


    /**
     * Generate the filename to be used for this text.
     *
     * @return string
     */
    protected function generateFilename()
    {
        $options = $this->provider->getOptions();

        $options["text"] = $this->text;

        $data = serialize($options);

        return md5($data) . ".mp3";
    }


    /**
     * Create an mp3 file on the filesystem.
     *
     * @param string $filename The filename to write to
     *
     * @return static
     */
    public function save($filename)
    {
        $result = file_put_contents($filename, $this->getAudioData());

        if ($result === false) {
            throw new Exception("Unable to save the file ({$filename})");
        }

        return $this;
    }


    /**
     * Store the mp3 file on the filesystem.
     *
     * This function uses caching so if the file already exists
     * a call to the text-to-speech service is not made.
     *
     * @param string $path The path to the directory to store the file in
     *
     * @return string The full path and filename
     */
    public function getFile($path = null)
    {
        if ($path === null) {
            $path = sys_get_temp_dir();
        }

        $filename = $path . "/" . $this->generateFilename();

        if (!is_file($filename)) {
            $this->save($filename);
        }

        return $filename;
    }
}
