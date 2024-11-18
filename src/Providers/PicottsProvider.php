<?php

namespace duncan3dc\Speaker\Providers;

use duncan3dc\Exec\FactoryInterface;
use duncan3dc\Exec\Output\Silent;
use duncan3dc\Exec\Program;
use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Exceptions\ProviderException;

use function exec;
use function explode;
use function file_exists;
use function file_get_contents;
use function md5;
use function preg_match;
use function strlen;
use function strtolower;
use function strtoupper;
use function sys_get_temp_dir;
use function trim;
use function unlink;

/**
 * Convert a string of a text to a spoken word wav.
 */
class PicottsProvider extends AbstractProvider
{
    /**
     * @var string $pico The picotts program.
     */
    private $pico;

    /**
     * @var string $language The language to use.
     */
    private $language = "en-US";


    /**
     * Create a new instance.
     *
     * @param string $language The language to use
     */
    public function __construct(string $language = null)
    {
        $pico = trim((string) exec("which pico2wave"));
        if (!file_exists($pico)) {
            throw new ProviderException("Unable to find picotts program, please install pico2wave before trying again");
        }

        $this->pico = $pico;

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
     * Get the format of this audio.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return "wav";
    }


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
     * @param FactoryInterface $factory
     *
     * @return string The audio data
     */
    public function textToSpeech(string $text, FactoryInterface $factory = null): string
    {
        $filename = sys_get_temp_dir() . \DIRECTORY_SEPARATOR . "speaker_picotts_{$this->language}_" . md5($text) . ".wav";

        if (file_exists($filename)) {
            unlink($filename);
        }

        if ($factory === null) {
            $program = new Program($this->pico, new Silent());
        } else {
            $program = $factory->make($this->pico);
        }

        $result = $program->getResult("--wave={$filename}", "--lang={$this->language}", $text);

        if ($result->getStatus() !== 0) {
            throw new ProviderException($result->getFirstLine());
        }

        if (!file_exists($filename)) {
            throw new ProviderException("TextToSpeech unable to create file: {$filename}");
        }

        $result = file_get_contents($filename);
        if ($result === false) {
            throw new ProviderException("TextToSpeech unable to read file: {$filename}");
        }

        unlink($filename);

        return $result;
    }
}
