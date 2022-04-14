<?php

namespace duncan3dc\Speaker\Providers;

use duncan3dc\Speaker\Exceptions\ProviderException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

use function http_build_query;

/**
 * Convert a string of a text to spoken word audio.
 */
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @var ClientInterface $client A guzzle instance for http requests.
     */
    private $client;

    /**
     * Get the guzzle client instance to use.
     *
     * @param ClientInterface $client
     *
     * @return ProviderInterface
     */
    public function setClient(ClientInterface $client): ProviderInterface
    {
        $this->client = $client;

        return $this;
    }


    /**
     * Get the guzzle client.
     *
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
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
        return [];
    }


    /**
     * Send a http request.
     *
     * @param string $hostname The hostname to send the request to
     * @param string[] $params The parameters of the request
     *
     * @return string The response body
     */
    protected function sendRequest(string $hostname, array $params): string
    {
        $url = $hostname . "?" . http_build_query($params);

        $response = $this->getClient()->request("GET", $url);

        if ($response->getStatusCode() != "200") {
            throw new ProviderException("Failed to call the external text-to-speech service");
        }

        return $response->getBody();
    }
}
