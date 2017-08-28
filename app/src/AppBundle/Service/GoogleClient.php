<?php
/**
 * Created by PhpStorm.
 * User: alexis
 * Date: 26/08/17
 * Time: 18:45
 */

namespace AppBundle\Service;


class GoogleClient
{
    /**
     * @var \Google_Client
     */
    private $client;

    /**
     * GoogleClient constructor.
     * @param string $googleId
     */
    public function __construct(string $googleId)
    {
        $this->client = new \Google_Client(['client_id' => $googleId]);
    }

    /**
     * @return \Google_Client
     */
    public function getClient(): \Google_Client
    {
        return $this->client;
    }
}