<?php

namespace duncan3dc\Speaker\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;

/**
 * Convert a string of a text to spoken word audio.
 */
class ResponsiveVoiceProvider extends AbstractProvider
{
    /**
     * @var string $language The language to use.
     */
    private $language = "en-GB";

    /**
     * Create a new instance.
     *
     * @param string $language The language to use
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

        if (strlen($language) === 2) {
            $language = "{$language}-{$language}";
        }

        if (!preg_match("/^[a-z]{2}-[a-z]{2}$/i", $language)) {
            throw new InvalidArgumentException("Unexpected language code ({$language}), codes should be 2 characters, a hyphen, and a further 2 characters");
        }

        list($main, $sub) = explode("-", $language);
        $language = strtolower($main) . "-" . strtoupper($sub);

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
        return $this->sendRequest("https://code.responsivevoice.org/getvoice.php", [
            "tl"        =>  $this->language,
            "t"         =>  $text,
        ]);
    }
}
