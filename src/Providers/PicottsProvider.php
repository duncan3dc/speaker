<?php

namespace duncan3dc\Speaker\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;
use duncan3dc\Speaker\Exceptions\ProviderException;
use duncan3dc\Speaker\Providers\AbstractProvider;
use Symfony\Component\Process\ProcessBuilder;

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
        $pico = trim(exec("which pico2wave"));
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
    public function textToSpeech(string $text, ProcessBuilder $builder = null): string
    {
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "speaker_picotts.wav";

        if (file_exists($filename)) {
            unlink($filename);
        }

        if ($builder === null) {
            $builder = new ProcessBuilder;
        }

        $process = $builder
            ->setPrefix($this->pico)
            ->add("--wave={$filename}")
            ->add("--lang={$this->language}")
            ->add($text);

        $process = $builder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            $output = $process->getErrorOutput();
            throw new ProviderException(explode("\n", $output)[0]);
        }

        if (!file_exists($filename)) {
            throw new ProviderException("TextToSpeech unable to create file: {$filename}");
        }

        $result = file_get_contents($filename);
        unlink($filename);

        return $result;
    }
}
