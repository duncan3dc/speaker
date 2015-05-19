<?php

namespace duncan3dc\Speaker\Providers;

/**
 * Convert a string of a text to a spoken word mp3.
 */
interface ProviderInterface
{
    /**
     * Convert the specified text to mp3 audio.
     *
     * @param string $text The text to convert
     *
     * @return string The mp3 audio data
     */
    public function textToSpeech($text);
}
