<?php

namespace AppBundle\Controller;

use AppBundle\Api\Weather\WeatherInfoClimat;
use AppBundle\Api\Transport\GoogleDirection;
use AppBundle\Model\Localisation;
use JMS\Serializer\Exception\LogicException;
use PHPUnit\Runner\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Model\ApiData;
use AppBundle\Resolver\ApiServiceResolver;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\Velov\VelovParc;
use AppBundle\Service\Velov\Velov;
use AppBundle\Utils;

class DefaultController extends Controller
{
    const API_DATA_TYPE = 'main';

    /**
     * @Route("/", name="homepage")
     *
     */
    public function indexAction(Request $request)
    {
        // Obligatoire
        $addressFrom    = $request->request->get("addressFrom");
        $addressTo      = $request->request->get("addressTo");
        // Optionnel
        $latFrom        = $request->request->get("latFrom");
        $lngFrom        = $request->request->get("lngFrom");
        $latTo          = $request->request->get("latTo");
        $lngTo          = $request->request->get("lngTo");
        if(is_null($addressFrom) || is_null($addressTo))
        {
            throw new \LogicException("Wrong address given!"); // FIXME --> Renvoyer un code d'erreur à la place d'une exception
        }

        if (is_null($latFrom) || is_null($latFrom)){
            $locFrom = Utils\LocalisationUtils::getCoordonateByAddress($addressFrom);
        } else {
            $locFrom = new Localisation(floatval($latFrom),floatval($lngFrom) );
        }

        if (is_null($latTo) || is_null($latTo)){
            $locTo = Utils\LocalisationUtils::getCoordonateByAddress($addressTo);
        } else {
            $locTo = new Localisation(floatval($latTo),floatval($lngTo) );
        }

        // Simulation of user input to retrieve related services from his keywords
        $services = $this->get(ApiServiceResolver::class)->resolveByApiKeyWords(['metro', 'meteo', 'slip', 'bike']);

        $apiData = new ApiData();
        $apiData->setType(self::API_DATA_TYPE);

        foreach ($services as $service) {
            if ($service instanceof GoogleDirection) {
                $from = "44 Rue du 24 Mars 1852, 69009 Lyon";
                $to   = "157 Avenue Jean Mermoz, 69008 Lyon";
                $directionWalk  = $this->get(GoogleDirection::class)->getDirection($from, $to, "walking");
                $directionBike  = $this->get(GoogleDirection::class)->getDirection($from, $to, "bicycling");
                $directionDrive = $this->get(GoogleDirection::class)->getDirection($from, $to, "driving");

                $apiData->addData($directionWalk);
                $apiData->addData($directionBike);
                $apiData->addData($directionDrive);
            }

            if ($service instanceof Velov) {
                //Define an array of VelovArret object
                $data = $this->get(Velov::class)->setVelovParc();
                //Return the formated data array
                $nearFrom = VelovParc::getNearStop($data, $locFrom);
                $nearTo = VelovParc::getNearStop($data, $locTo);
                $nearFrom['arret']->setType("transport.velov.nearFrom");
                $nearTo['arret']->setType("transport.velov.nearTo");

                $apiData->addData($nearFrom);
                $apiData->addData($nearTo);

            }

            if ($service instanceof WeatherInfoClimat) {

                //Define an array of WeatherInfoClimat object
                $weatherFrom = $this->get(WeatherInfoClimat::class)->getWeather($locFrom);
                $weatherTo   = $this->get(WeatherInfoClimat::class)->getWeather($locTo);
                $weatherFrom->setType("weatherFrom");
                $weatherFrom->setType("weatherTo");
                $apiData->addData($weatherFrom);
                $apiData->addData($weatherTo);
            }
        }

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($apiData, 'json');

        return new JsonResponse(
            $jsonContent,
            200,
            [
                'Access-Control-Allow-Origin' => '*'
            ],
            true
        );
    }

    /**
     * @Route("/weather1", name="weather1")
     */
    public function Weather1Action(Request $request)
    {
        $apiData = new ApiData();
        $apiData->setType(self::API_DATA_TYPE);
        $data = $this->get(WeatherInfoClimat::class)->getWeather();
        $apiData->addData($data);

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($data, 'json');

        return new JsonResponse($jsonContent, 200, [], true);
    }
}
