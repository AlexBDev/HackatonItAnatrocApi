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

class DefaultController extends Controller
{
    const API_DATA_TYPE = 'main';


    /**
     * @Route("/", name="homepage")
     *
     */
    public function indexAction(Request $request)
    {

        $fromLat = $request->request->get("fromLat");
        $fromLng = $request->request->get("fromLng");
        $toLat = $request->request->get("toLat");
        $toLng = $request->request->get("toLng");
        if(!is_numeric($fromLat) || !is_numeric($fromLng) || !is_numeric($toLat) || !is_numeric($toLng))
        {
            throw new \LogicException("Bad coordonate");
        }
        $FromLoc = new Localisation(floatval($fromLat),floatval($fromLng) );
        $toLoc = new Localisation(floatval($toLat),floatval($toLng) );


        // Simulation of user input to retrieve related services from his keywords
        $services = $this->get(ApiServiceResolver::class)->resolveByApiKeyWords(['metro', 'meteo', 'slip', 'bike']);

        $apiData = new ApiData();
        $apiData->setType(self::API_DATA_TYPE);

        foreach ($services as $service) {
            if ($service instanceof GoogleDirection) {
                $data = $this->get(GoogleDirection::class)->getDirection();
                $apiData->addData($data);
            }

            if ($service instanceof Velov) {
                //Define an array of VelovArret object
                $data = $this->get(Velov::class)->setVelovParc();
                //Return the formated data array
                $apiData->addData(VelovParc::getNearStop($data, $fromLoc));
            }

            if ($service instanceof WeatherInfoClimat) {

                //Define an array of WeatherInfoClimat object
                $data = $this->get(WeatherInfoClimat::class)->getWeather($fromLoc);
                $apiData->addData($data);
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
