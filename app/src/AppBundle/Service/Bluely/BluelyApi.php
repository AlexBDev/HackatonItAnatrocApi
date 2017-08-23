<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 22/08/17
 * Time: 17:16
 */

namespace AppBundle\Service\Bluely;

use AppBundle\Model\Localisation;
use AppBundle\Api\ApiKeywordInterface;
use AppBundle\Api\AbstractApi;
use AppBundle\Model;

class BluelyApi extends AbstractApi implements ApiKeywordInterface
{

    static function getFromGrandLyonApi(): array
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request("GET","https://download.data.grandlyon.com/ws/grandlyon/pvo_patrimoine_voirie.pvostationautopartage/all.json");

        $body = $response->getBody();
        $JSONresult = $body->getContents();


        $resultsData = json_decode($JSONresult);


        foreach ($resultsData->features as $recordData)
        {
            $car = new Bluely();

            $car->se($recordData->properties->address);


            $parc[] = $car;
        }

        return $parc;
    }


    /**
     * @return array
     */
    public static function getApiKeywords(): array
    {
        return [
            'voiture',
            'electrique',
        ];
    }
}