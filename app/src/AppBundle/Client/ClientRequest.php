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
    private $currentLocalisation = null;

    /**
     * @var string
     */
    private $text = '';

    /**
     * @var null|Localisation|string
     */
    private $from = null;

    /**
     * @var null|Localisation|string
     */
    private $to = null;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * ClientRequest constructor.
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return ClientRequest
     */
    public function setText(string $text): ClientRequest
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Provide a Localisation object to obtain the current latitude and longitude client
     *
     * @return Localisation|null
     */
    public function getCurrentLocalisation(): ?Localisation
    {
        return $this->currentLocalisation;
    }

    /**
     * @param Localisation|null $currentLocalisation
     * @return ClientRequest
     */
    public function setCurrentLocalisation(Localisation $currentLocalisation = null): ClientRequest
    {
        $this->currentLocalisation = $currentLocalisation;

        return $this;
    }

    /**
     * @return Localisation|null|string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param Localisation $from
     * @return ClientRequest
     */
    public function setFrom($from): ClientRequest
    {
        if ($from === null || is_string($from) || $from instanceof Localisation) {
            $this->from = $from;

            return $this;
        }

        throw new \UnexpectedValueException('$from must be a type of "null", "string", AppBundle\Model\Localisation');
    }

    /**
     * @return Localisation|null|string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param Localisation $to
     * @return ClientRequest
     */
    public function setTo($to): ClientRequest
    {
        if ($to === null || is_string($to) || $to instanceof Localisation) {
            $this->to = $to;

            return $this;
        }

        throw new \UnexpectedValueException('$to must be a type of "null", "string", AppBundle\Model\Localisation');
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