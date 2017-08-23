<?php

namespace AppBundle\Controller;

use AppBundle\Api\Weather\WeatherInfoClimat;
use AppBundle\Api\Transport\GoogleDirection;
use AppBundle\Model\Localisation;
use GuzzleHttp\Exception\RequestException;
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
        try
        {

            $lat = $request->request->get("lat");
            $lng = $request->request->get("lng");
            if(!isset($lat) || !isset($lng))
            {
                throw new \LogicException("Bad coordonate");
            }
            if($lat == "" or $lng == "")
            {
                throw new \LogicException("Bad coordonate");
            }
            if(!is_numeric($lat) or !is_numeric($lng))
            {
                throw new \LogicException("Bad coordonate");
            }

            $loc = new Localisation(floatval($lat),floatval($lng) );


            // Simulation of user input to retrieve related services from his keywords
            $services = $this->get(ApiServiceResolver::class)->resolveByApiKeyWords(['metro', 'meteo', 'slip', 'bike']);

            $apiData = new ApiData();
            $apiData->setType(self::API_DATA_TYPE);

            foreach ($services as $service) {
                if ($service instanceof GoogleDirection) {
                    $data = $this->get(GoogleDirection::class)->getDirection();
                    if(!isset($data) || $data == "")
                    {
                        throw new \LogicException("Api google direction has crashed");
                    }
                    $apiData->addData($data);
                }

                if ($service instanceof Velov) {
                    //Define an array of VelovArret object
                    $data = $this->get(Velov::class)->setVelovParc();

                    if(!isset($data) || $data == "")
                    {
                        throw new \LogicException("Api Velov has crashed");
                    }
                    //Return the formated data array
                    $apiData->addData(VelovParc::getNearStop($data, $loc));
                }

                if ($service instanceof WeatherInfoClimat) {

                    //Define an array of WeatherInfoClimat object
                    $data = $this->get(WeatherInfoClimat::class)->getWeather($loc);

                    if(!isset($data) || $data == "")
                    {
                        throw new \LogicException("Api weather has crashed");
                    }
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
        //Catch application error
            // Check post data
            // Check no return from an api
        catch ( \LogicException $e)
        {
            $errorMessage = explode(" ",$e->getMessage());
            if($errorMessage[0] == "Bad" && $errorMessage[1] == "coordonate")
            {
                $status = 400;
            }
            elseif ($errorMessage[0] == "Api" && $errorMessage[2] == "has" && $errorMessage == "crashed")
            {
                $status = 502;
            }
            else
            {
                $status = '500';
            }
            return new JsonResponse(
                $e->getMessage(),
                $status,
                [
                    'Access-Control-Allow-Origin' => '*'
                ],
                true
            );
        }
        // Catch Guzzle Error
        catch ( RequestException $e)
        {
            return new JsonResponse(
                "An error has occurred trying to process your request an api.",
                502,
                [
                    'Access-Control-Allow-Origin' => '*'
                ],
                true
            );
        }
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



    /**
     * Intercept all 404 error and return a 404 json error
     *
     * @Route("/{slug}", name="notFound", requirements={"slug" = "[[:graph:][:punct:]àéèêîçùµ]*"})
     *
     */
    public function notFoundAction(Request $request, $slug)
    {
        return new JsonResponse(
            "Page not found",
            404,
            [
                'Access-Control-Allow-Origin' => '*'
            ],
            true
        );
    }

}
