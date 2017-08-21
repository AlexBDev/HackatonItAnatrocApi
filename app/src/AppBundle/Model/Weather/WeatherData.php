<?php

namespace AppBundle\Model\Weather;

use AppBundle\Model\ApiData;


class WeatherData extends ApiData
{
    /*
     *
     */
    public static function getWeatherIndex(): array
    {
        return  ['soleil', 'nuage', 'pluie', 'orage', 'neige'];
    }

    /**
     * @return integer
     */
    public function getWeather(): string
    {
        return $this->data['weather'];
    }

    /*
     * @param int $index
     * @return WeatherData
     */
    public function setWeather($index)
    {
        $this->data['weather'] = self::getWeatherIndex()[$index];
        return $this;
    }

    /**
     * @return string
     */
    public function getTemperature(): string
    {
        return $this->data['temperature'];
    }

    /*
     * @param string $temperature
     * @return WeatherData
     */
    public function setTemperature($temperature)
    {
        $this->data['temperature'] = $temperature;
        return $this;
    }

    /**
     * @return string
     */
    public function getWindForce(): string
    {
        return $this->data['wind_force'];
    }

    /*
     * @param string $windForce
     * @return WeatherData
     */
    public function setWindForce($windForce)
    {
        $this->data['wind_force'] = $windForce;
        return $this;
    }

    /**
     * @return string
     */
    public function getWindDirection(): string
    {
        return $this->data['wind_direction'];
    }

    /*
     * @param string $windDirection
     * @return WeatherData
     */
    public function setWindDirection($windDirection)
    {
        $this->data['wind_direction'] = $windDirection;
        return $this;
    }
}