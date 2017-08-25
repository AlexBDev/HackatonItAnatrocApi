<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 25/08/17
 * Time: 14:46
 */

namespace AppBundle\Model;


class RequestLocalisation
{
    /**
     * @var Localisation
     */
    private $positionFrom;

    /**
     * @var Localisation
     */
    private $positionTo;

    /**
     * @var string
     */
    private $addressFrom;

    /**
     * @var string
     */
    private $addressTo;

    /**
     * @return Localisation
     */
    public function getPositionFrom(): Localisation
    {
        return $this->positionFrom;
    }

    /**
     * @param Localisation $positionFrom
     * @return RequestLocalisation
     */
    public function setPositionFrom(Localisation $positionFrom): RequestLocalisation
    {
        $this->positionFrom = $positionFrom;
        return $this;
    }

    /**
     * @return Localisation
     */
    public function getPositionTo(): Localisation
    {
        return $this->positionTo;
    }

    /**
     * @param Localisation $positionTo
     * @return RequestLocalisation
     */
    public function setPositionTo(Localisation $positionTo): RequestLocalisation
    {
        $this->positionTo = $positionTo;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddressFrom(): string
    {
        return $this->addressFrom;
    }

    /**
     * @param string $addressFrom
     * @return RequestLocalisation
     */
    public function setAddressFrom(string $addressFrom): RequestLocalisation
    {
        $this->addressFrom = $addressFrom;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddressTo(): string
    {
        return $this->addressTo;
    }

    /**
     * @param string $addressTo
     * @return RequestLocalisation
     */
    public function setAddressTo(string $addressTo): RequestLocalisation
    {
        $this->addressTo = $addressTo;
        return $this;
    }
}