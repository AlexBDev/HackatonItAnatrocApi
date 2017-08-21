<?php

namespace AppBundle\Request;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientREST
{
    /**
     * @var Client
     */
    private $client = null;

    /**
     * @var array
     */
    private $configuration = [];

    /**
     * ClientREST constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        $this->setConfiguration($configuration);
    }

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        if ($this->client === null) {
            $this->createClient();
        }

        return $this->client;
    }

    /**
     * @return ClientREST
     */
    private function createClient(): ClientREST
    {
        $this->client = new Client($this->getConfiguration());

        return $this;
    }

    /**
     * @return array
     */
    private function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($uri, array $options = []): ResponseInterface
    {
        return $this->getClient()->get($uri, $options);
    }
}