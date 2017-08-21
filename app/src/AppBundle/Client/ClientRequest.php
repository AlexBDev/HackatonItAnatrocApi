<?php

namespace AppBundle\Client;


use AppBundle\Model\Localisation;


/**
 * ClientRequest is an object that you can use for you API services, this is class is simple for now.
 *
 * Class ClientRequest
 * @package AppBundle\Client
 */
class ClientRequest
{
    /**
     * @var null|Localisation
     */
    private $localisation = null;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Provide a Localisation object to obtain the current latitude and longitude client
     *
     * @return Localisation|null
     */
    public function getLocalisation(): ?Localisation
    {
        return $this->localisation;
    }

    /**
     * @param Localisation $localisation
     * @return $this
     */
    public function setLocalisation(Localisation $localisation)
    {
        $this->localisation = $localisation;
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return ClientRequest
     */
    public function setParameters(array $parameters): ClientRequest
    {
        $this->parameters = $parameters;
        return $this;
    }
}