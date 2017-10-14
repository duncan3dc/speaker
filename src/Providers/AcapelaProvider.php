<?php

namespace duncan3dc\Speaker\Providers;

use duncan3dc\Speaker\Exceptions\InvalidArgumentException;

/**
 * Convert a string of a text to spoken word audio.
 */
class AcapelaProvider extends AbstractProvider
{
    /**
     * @var string $login Your acapela login.
     */
    private $login = "";

    /**
     * @var string $application Your acapela application.
     */
    private $application = "";

    /**
     * @var string $password Your acapela password.
     */
    private $password = "";

    /**
     * @var string $voice The voice to use.
     */
    private $voice = "rod";

    /**
     * @var int $speed The speech rate.
     */
    private $speed = 180;


    /**
     * Create a new instance.
     *
     * @param string $login The username to access the service
     * @param string $application The name of the application
     * @param string $password The password to access the service
     * @param string $voice The voice to use
     * @param int $speed The speech rate
     */
    public function __construct(string $login, string $application, string $password, string $voice = null, int $speed = null)
    {
        $this->login = $login;
        $this->application = $application;
        $this->password = $password;

        if ($voice !== null) {
            $this->voice = $this->getVoice($voice);
        }

        if ($speed !== null) {
            $this->speed = $this->getSpeed($speed);
        }
    }


    /**
     * Check if the voce is valid, and convert it to the required format.
     *
     * @param string $voice The voice to use
     *
     * @return string
     */
    private function getVoice(string $voice): string
    {
        $voice = trim($voice);
        if (strlen($voice) < 3) {
            throw new InvalidArgumentException("Unexpected voice name ({$voice}), names should be at least 3 characters long");
        }

        return strtolower($voice);
    }


    /**
     * Set the voice to use.
     *
     * Visit http://www.acapela-vaas.com/ReleasedDocumentation/voices_list.php for available voices
     *
     * @param string $voice The voice to use (eg 'Graham')
     *
     * @return self
     */
    public function withVoice(string $voice): self
    {
        $provider = clone $this;

        $provider->voice = $this->getVoice($voice);

        return $provider;
    }


    /**
     * Check the speech rate is valid.
     *
     * @param int $speed The speech rate to use
     *
     * @return int
     */
    private function getSpeed(int $speed): int
    {
        if ($speed < 60 || $speed > 360) {
            throw new InvalidArgumentException("Invalid speed ({$speed}), must be a number between 60 and 360");
        }

        return $speed;
    }


    /**
     * Set the speech rate to use.
     *
     * @param int $speed The speech rate to use (between 60 and 360)
     *
     * @return self
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
            "voice" =>  $this->voice,
            "speed" =>  $this->speed,
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
        if (strlen($text) > 300) {
            throw new InvalidArgumentException("Only messages under 300 characters are supported");
        }

        return $this->sendRequest("http://vaas.acapela-group.com/Services/FileMaker.mp3", [
            "prot_vers" =>  2,
            "cl_login"  =>  $this->login,
            "cl_app"    =>  $this->application,
            "cl_pwd"    =>  $this->password,
            "req_voice" =>  "{$this->voice}22k",
            "req_spd"   =>  $this->speed,
            "req_text"  =>  $text,
        ]);
    }
}
