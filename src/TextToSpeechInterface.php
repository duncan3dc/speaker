<?php

namespace duncan3dc\Speaker;

use duncan3dc\Speaker\Providers\ProviderInterface;

/**
 * Convert a string of a text to spoken word audio.
 */
interface TextToSpeechInterface
{
    /**
     * Get the audio for this text.
     *
     * @return string The audio data
     */
    public function getAudioData(): string;


    /**
     * Generate the filename to be used for this text.
     *
     * @return string
     */
    public function generateFilename(): string;


    /**
     * Create an audio file on the filesystem.
     *
     * @param string $filename The filename to write to
     *
     * @return $this
     */
    public function save(string $filename): TextToSpeechInterface;


    /**
     * Store the audio file on the filesystem.
     *
     * @param string $path The path to the directory to store the file in
     *
     * @return string The full path and filename
     */
    public function getFile(string $path = null): string;
}
