<?php

namespace duncan3dc\Speaker\Providers;

/**
 * Convert a string of a text to a spoken word mp3.
 */
interface ProviderInterface
{
    /**
     * Get the current options.
     *
     * This is used in caching to determine if we have sent a request
     * with these options before and can use the previous result.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Convert the specified text to mp3 audio.
     *
     * @param string $text The text to convert
     *
     * @return string The mp3 audio data
     */
    public function textToSpeech($text);
}
