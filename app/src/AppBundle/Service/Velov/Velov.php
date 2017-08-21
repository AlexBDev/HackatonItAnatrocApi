<?php

namespace AppBundle\Service\Velov;

use AppBundle\Model\Velov\VelovArret;
use AppBundle\Api\ApiKeywordInterface;


class Velov implements ApiKeywordInterface
{
    public function setVelovParc()
    {
        $apiNum = 1;

        if($apiNum == 1)
        {
            return $this->getFromGrandLyonApi();

        }
        else if ($apiNum == 2)
        {
            return $this->getFromOpenDataApi();
        }

        throw new \UnexpectedValueException("Aucune api selectionnée");
    }


    /**
     * @return array
     *
     * request on https://www.data.gouv.fr/fr/datasets/station-velov-disponibilites-temps-reel/
     * licence ouverte, https://download.data.grandlyon.com/files/grandlyon/LicenceOuverte.pdf
     */
    private function getFromGrandLyonApi(): array
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request("GET","https://inspire.data.gouv.fr/api/geogw/services/556c63df330f1fcd48345220/feature-types/ms:jcd_jcdecaux.jcdvelov/download?format=GeoJSON&projection=WGS84");

        $body = $response->getBody();
        $JSONresult = $body->getContents();


        $resultsData = json_decode($JSONresult);


        $parc = array();
        foreach ($resultsData->features as $recordData)
        {
            $arret = new VelovArret();

            $arret->setAddress($recordData->properties->address);
            $arret->setBikeStands($recordData->properties->bike_stands);
            $arret->setLatitude($recordData->properties->lat);
            $arret->setLongitude($recordData->properties->lng);
            $arret->setAddress($recordData->properties->address);
            $arret->setCommune($recordData->properties->commune);
            $arret->setStatus($recordData->properties->status);
            $arret->setName($recordData->properties->name);

            $parc[] = $arret;
        }

        return $parc;
    }

    /**
     * @return array
     *
     * connect on https://public.opendatasoft.com/explore/dataset/station-velov-grand-lyon/information/
     * licence ouverte : https://www.etalab.gouv.fr/wp-content/uploads/2014/05/Licence_Ouverte.pdf
     */
    private function getFromOpenDataApi(): array
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request("GET","https://public.opendatasoft.com/api/records/1.0/search/?dataset=station-Velov-grand-lyon&lang=fr&rows=10&geofilter.distance=45.8520930694%2C4.34738897685%2C1000000000");


        $body = $response->getBody();
        $JSONresult = $body->getContents();


        $resultsData = json_decode($JSONresult);

        $parc = array();
        foreach ($resultsData->records as $recordData)
        {
            $arret = new VelovArret();
            $arret->setAddress($recordData->fields->address);
            $arret->setBikeStands($recordData->fields->bike_stand);
            $arret->setLatitude($recordData->fields->lat);
            $arret->setLongitude($recordData->fields->lng);
            $arret->setAddress($recordData->fields->address);
            $arret->setCommune($recordData->fields->commune);
            $arret->setStatus($recordData->fields->status);
            $arret->setName($recordData->fields->name);

            $parc[] = $arret;

        }
        return $parc;
    }


    /**
     * @return array
     */
    public static function getApiKeywords(): array
    {
        return [
            'velo',
            'Velov',
            'bike',
        ];
    }
}