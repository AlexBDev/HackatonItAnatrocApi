<?php

namespace AppBundle\Model\Velov;

use AppBundle\Model\Localisation;

/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 05/07/17
 * Time: 14:14
 */
class VelovArret
{

    private $name;
    private $address;
    private $commune;
    private $localisation ;
    private $bike_stands;
    private $status;
    private $availableStand;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * @param mixed $commune
     */
    public function setCommune($commune)
    {
        $this->commune = $commune;
    }

    /**
     * @return mixed
     */
    public function getBikeStands()
    {
        return $this->bike_stands;
    }

    /**
     * @param mixed $bike_stands
     */
    public function setBikeStands($bike_stands)
    {
        $this->bike_stands = $bike_stands;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param mixed $availableStand
     */
    public function setAvailableStand($availableStand)
    {
        $this->availableStand = $availableStand;
    }

    /*
     * Return how many bike is available.
     */
    public function getAvailableBike()
    {
        return $this->bike_stands - $this->availableStand;
    }


    /*
     *
     */
    public function returnJson(&$data)
    {
        foreach ($this as $key => $value)
        {
            $data[$key] = $value;
        }
    }

    /**
     * @return mixed
     */
    public function getLocalisation(): Localisation
    {
        return $this->localisation;
    }

    /**
     * @param mixed $localisation
     */
    public function setLocalisation(Localisation $localisation)
    {
        $this->localisation = $localisation;
    }



}