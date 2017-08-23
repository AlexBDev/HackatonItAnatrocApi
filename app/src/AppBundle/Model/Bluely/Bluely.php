<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 23/08/17
 * Time: 09:10
 */

class Bluely
{
    private $name;
    private $localisation;
    private $nbSpot;
    private $address;
    private $idStation;
    private $dateCreation;
    private $municipality;
    private $status;

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
    public function getLocalisation()
    {
        return $this->localisation;
    }

    /**
     * @param mixed $localisation
     */
    public function setLocalisation($localisation)
    {
        $this->localisation = $localisation;
    }

    /**
     * @return mixed
     */
    public function getNbSpot()
    {
        return $this->nbSpot;
    }

    /**
     * @param mixed $nbSpot
     */
    public function setNbSpot($nbSpot)
    {
        $this->nbSpot = $nbSpot;
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
    public function getIdStation()
    {
        return $this->idStation;
    }

    /**
     * @param mixed $idStation
     */
    public function setIdStation($idStation)
    {
        $this->idStation = $idStation;
    }

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param mixed $dateCreation
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * @return mixed
     */
    public function getMunicipality()
    {
        return $this->municipality;
    }

    /**
     * @param mixed $municipality
     */
    public function setMunicipality($municipality)
    {
        $this->municipality = $municipality;
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




}