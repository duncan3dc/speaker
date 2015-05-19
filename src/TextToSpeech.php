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
}
