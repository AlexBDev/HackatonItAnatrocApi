<?php

namespace AppBundle\Model\Transport;

use AppBundle\Model\ApiData;
use AppBundle\Model\Localisation;

class TransportData extends ApiData
{
    public function setStartAddressName($address)
    {
        $this->data['start_address_name'] = $address;

        return $this;
    }

    public function getStartAddressName()
    {
        return $this->data['start_address_name'];
    }

    public function setEndAddressName($address)
    {
        $this->data['end_address_name'] = $address;

        return $this;
    }

    public function getEndAddressName()
    {
        return $this->data['end_address_name'];
    }

    public function getStartLocation()
    {
        return $this->data['start_location'];
    }

    public function setStartLocation(Localisation $location)
    {
        $this->data['start_location'] = $location;

        return $this;
    }

    public function getEndLocation()
    {
        return $this->data['end_location'];
    }

    public function setEndLocation(Localisation $location)
    {
        $this->data['end_location'] = $location;

        return $this;
    }

    public function getDistance()
    {
        return $this->data['distance'];
    }

    public function setDistance($distance)
    {
        $this->data['distance'] = $distance;

        return $this;
    }

    public function getDuration()
    {
        return $this->data['duration'];
    }

    public function setDuration($duration)
    {
        $this->data['duration'] = $duration;

        return $this;
    }
}