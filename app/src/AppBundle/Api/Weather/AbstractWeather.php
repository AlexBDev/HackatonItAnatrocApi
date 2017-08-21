<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 04/07/17
 * Time: 14:27
 */

namespace AppBundle\Api\Weather;


use AppBundle\Api\AbstractApi;
use AppBundle\Model\Weather\WeatherData;
use GuzzleHttp\Client;

abstract class AbstractWeather extends AbstractApi
{
    // Variable à soustraire à des degrés kelvin pour en faire des degrés celsius
    const KELVIN_TO_CELSIUS = 273.15;

    // Le temps retourné est "soleil"
    const TYPE_SUN = 0;

    // Le temps retourné est "nuage"
    const TYPE_CLOUD = 1;

    // Le temps retourné est "pluie"
    const TYPE_RAIN = 2;

    // Le temps retourné est "orage"
    const TYPE_STORM = 3;

    // Le temps retourné est "neige"
    const TYPE_SNOW = 4;

    /**
     * @var string
     */
    const API_DATA_TYPE = 'weather';

    /**
     * @var string
     */
    protected $type = self::API_DATA_TYPE;

    /**
     * @var Client
     */
    private $guzzle;

    /**
     * SubwayTCL constructor.
     */
    public function __construct()
    {
        $this->guzzle = new Client();
    }

    /**
     * @return array
     */
    public static function getApiKeywords(): array
    {
        return [
            'temps',
            'meteo',
            'pluie',
            'soleil',
        ];
    }

    /**
     * @return Client
     */
    public function getGuzzle(): Client
    {
        return $this->guzzle;
    }
}

