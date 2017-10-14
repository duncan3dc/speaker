<?php

namespace duncan3dc\Speaker\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Exceptions\ProviderException;

/**
 * Convert a string of a text to spoken word audio.
 */
class VoiceRssProvider extends AbstractProvider
{
    /**
     * @var string $language The language to use.
     */
    private $language = "en-gb";

    /**
     * @var int $speed The speech rate.
     */
    private $speed = 0;

    /**
     * Create a new instance.
     *
     * @param string $api Your Voice RSS API key.
     * @param string $language The language to use.
     * @param int $speed The speech rate to use.
     */
    public function __construct(string $apikey, string $language = null, int $speed = null)
    {
        $this->apikey = $apikey;

        if ($language !== null) {
            $this->language = $this->getLanguage($language);
        }

        if ($speed !== null) {
            $this->speed = $this->getSpeed($speed);
        }
    }


    /**
     * Check if the language is valid, and convert it to the required format.
     *
     * @param string $language The language to use
     *
     * @return string
     */
    private function getLanguage(string $language): string
    {
        $language = strtolower(trim($language));

        if (strlen($language) === 2) {
            $language = "{$language}-{$language}";
        }

        if (!preg_match("/^[a-z]{2}-[a-z]{2}$/", $language)) {
            throw new InvalidArgumentException("Unexpected language code ({$language}), codes should be 2 characters, a hyphen, and a further 2 characters");
        }

        return $language;
    }


    /**
     * Set the language to use.
     *
     * @param string $language The language to use (eg 'en')
     *
     * @return self
     */
    public function withLanguage(string $language): self
    {
        $provider = clone $this;

        $provider->language = $this->getLanguage($language);

        return $provider;
    }


    /**
     * Check the speech rate is valid.
     *
     * @param int $speed The speech rate to use
     *
     * @return int
     */
    private function getSpeed(int $speed)
    {
        if ($speed < -10 || $speed > 10) {
            throw new InvalidArgumentException("Invalid speed ({$speed}), must be a number between -10 and 10");
        }

        return $speed;
    }


    /**
     * Set the speech rate to use.
     *
     * @param int $speed The speech rate to use (between -10 and 10)
     *
     * @return $this
     */
    public function withSpeed(int $speed): self
    {
        $provider = clone $this;

        $provider->speed = $this->getSpeed($speed);

        return $provider;
    }


    /**
     * Get the current options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [
            "language"  =>  $this->language,
            "speed"     =>  $this->speed,
        ];
    }


    /**
     * Convert the specified text to audio.
     *
     * @param string $text The text to convert
     *
     * @return string The audio data
     */
    public function textToSpeech(string $text): string
    {
        $result = $this->sendRequest("https://api.voicerss.org/", [
            "key"   =>  $this->apikey,
            "src"   =>  $text,
            "hl"    =>  $this->language,
            "r"     =>  $this->speed,
            "c"     =>  "MP3",
            "f"     =>  "16khz_16bit_stereo",
        ]);

        if (substr($result, 0, 6) === "ERROR:") {
            throw new ProviderException("TextToSpeech {$result}");
        }

        return $result;
    }
}
