<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 06/07/17
 * Time: 11:46
 */

namespace AppBundle\Model;


class Localisation
{
    /**
     * @var float
     */
    private $lat;

    /**
     * @var
     */
    private $lng;

    /**
     * Localisation constructor.
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct(float $latitude, float $longitude)
    {
        $this->lat = $latitude;
        $this->lng = $longitude;
    }

    /**
     * @return float
     */
    public function getLng(): float
    {
        return $this->lng;
    }

    /**
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }
}