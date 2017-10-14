<?php

namespace duncan3dc\Speaker\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;

/**
 * Convert a string of a text to spoken word audio.
 */
class GoogleProvider extends AbstractProvider
{
    /**
     * @var string $language The language to use.
     */
    private $language = "en";

    /**
     * Create a new instance.
     *
     * @param string $language The language to use.
     */
    public function __construct(string $language = null)
    {
        if ($language !== null) {
            $this->language = $this->getLanguage($language);
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
        $language = trim($language);

        if (strlen($language) !== 2) {
            throw new InvalidArgumentException("Unexpected language code ({$language}), codes should be 2 characters");
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
     * Get the current options.
     *
     * @return array
     */
    public function getOptions(): array
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
    public function textToSpeech(string $text): string
    {
        if (strlen($text) > 100) {
            throw new InvalidArgumentException("Only messages under 100 characters are supported");
        }

        return $this->sendRequest("http://translate.google.com/translate_tts", [
            "q"         =>  $text,
            "tl"        =>  $this->language,
            "client"    =>  "duncan3dc-speaker",
        ]);
    }
}
