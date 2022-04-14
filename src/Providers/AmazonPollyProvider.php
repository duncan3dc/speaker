<?php

namespace duncan3dc\Speaker\Providers;

use Aws\Polly\PollyClient;
use duncan3dc\Speaker\Exceptions\ProviderException;

/**
 * Convert a string of a text to spoken word audio.
 */
class AmazonPollyProvider implements ProviderInterface
{
    /**
     * @var PollyClient client The client to interact with.
     */
    private $client;

    /**
     * @var string $voice The voice to use.
     */
    private $voice = "Emma";


    /**
     * Create a new instance.
     *
     * @param PollyClient $client The client to interact with
     * @param string $voice The voice to use
     */
    public function __construct(PollyClient $client, string $voice = null)
    {
        $this->client = $client;
        if ($voice !== null) {
            $this->voice = $voice;
        }
    }


    /**
     * Set the voice to use.
     *
     * @param string $voice The voice to use.
     *
     * @return $this
     */
    public function withVoice(string $voice): self
    {
        $provider = clone $this;

        $provider->voice = $voice;

        return $provider;
    }


    /**
     * Get the format of this audio.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return "mp3";
    }


    public function getOptions(): array
    {
        return [
            "voice" =>  $this->voice,
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
        try {
            $result = $this->client->synthesizeSpeech([
                "OutputFormat"  => $this->getFormat(),
                "Text"          => $text,
                "VoiceId"       => $this->voice,
            ]);
        } catch (\Throwable $e) {
            throw new ProviderException("Failed to call the external text-to-speech service", $e->getCode(), $e);
        }

        return (string) $result->get("AudioStream");
    }
}
