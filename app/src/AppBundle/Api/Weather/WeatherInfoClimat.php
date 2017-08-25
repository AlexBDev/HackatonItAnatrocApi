<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 04/07/17
 * Time: 14:27
 */

namespace AppBundle\Api\Weather;

use AppBundle\Model\Localisation;
use AppBundle\Model\Weather\WeatherData;

class WeatherInfoClimat extends AbstractWeather
{
    public function getWeather(Localisation $loc)
    {
        $response = $this->getGuzzle()->get('http://www.infoclimat.fr/public-api/gfs/json?_ll='.$loc->getLat().','.$loc->getLng().'&_auth=UUsCFQ5wV3VVeFdgBnBWf1U9ADULfQIlBnoFZgtuAH1UP1AxUjIHYVM9WyYDLAUzBShXNAA7CTkFbgV9Xy1UNVE7Am4OZVcwVTpXMgYpVn1VewBhCysCJQZmBWULeABiVDFQPVIvB2RTOVs%2BAy0FMwU0VzIAIAkuBWcFZl87VD5RMgJuDmpXPVU5VzEGKVZ9VWMAaQsxAmsGYQVhCzIAMlQ%2FUGJSMgc0U21bMAMtBTMFMlczADsJNwVjBWJfO1QoUS0CHw4eVyhVeld3BmNWJFV7ADULagJu&_c=027a5f6d83c9c484d1f0ba18810b275f');
        $json = \GuzzleHttp\json_decode($response->getBody()->getContents());

        $weather = $this->getWeatherByDate($json);

        return $this->transformToWeatherData($weather);
    }

    private function transformToWeatherData($json)
    {
        $weather = new WeatherData();

        $weather->setType($this->getType());
        $weather->setTemperature($json->temperature->sol - self::KELVIN_TO_CELSIUS);
        $weather->setWindForce($json->vent_moyen->{'10m'});
        //$weather->setWindDirection($json->vent_direction->{'10m'});
        $weather->setWeather($this->getWeatherByParams($json->risque_neige,$json->pluie,floatval($json->pluie_convective),$json->nebulosite->totale,$json->vent_moyen));

        return $weather;
    }

    /**
     * @return array
     */
    public function getDate($result)
    {
        $test = true;
        $date = date('Y-m-d H:m:i'); //date actuelle

        foreach ($result as $key => $value) {
            if (strtotime($date) > strtotime($key)){
                $dateavant = $key;
            } else {
                if ($test == true) {
                    $dateapres = $key;
                    $test = false;
                }
            }
        }

        $datetrue = $this->getBetterDate($dateavant,$dateapres,$date);

        return $datetrue;
    }

    /**
     * @return array
     */
    public function getWeatherByDate($data)
    {
        $datetrue = $this->getDate($data);

        return $data->$datetrue;
    }

    /**
     * @return \DateTime|null
     */
    public function getBetterDate($dateBefore, $dateAfter, $date)
    {
        $valeur1 = strtotime($date) - strtotime($dateBefore);
        $valeur2 = strtotime($dateAfter) - strtotime($date);

        if ($valeur1 < $valeur2) {
            $datetrue = $dateBefore;
        } elseif ($valeur1 > $valeur2) {
            $datetrue = $dateAfter;
        }

        return $datetrue;
    }

    /**
     * @return integer
     */
    public function getWeatherByParams($snow, $rain, $convectivRain, $nebulosity, $wind)
    {
        $weather = self::TYPE_SUN;
        if ($snow == 'oui') {
            $weather = self::TYPE_SNOW;
        } elseif ($convectivRain > 0.0 && $wind > 25) {
            $weather = self::TYPE_STORM;
        } elseif ($rain > 0) {
            $weather = self::TYPE_RAIN;
        } elseif ($nebulosity > 50) {
            $weather = self::TYPE_CLOUD;
        }

        return $weather;
    }
}