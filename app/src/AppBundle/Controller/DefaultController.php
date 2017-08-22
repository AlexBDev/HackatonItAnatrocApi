<?php

namespace AppBundle\Controller;

use AppBundle\Api\Weather\WeatherInfoClimat;
use AppBundle\Api\Transport\GoogleDirection;
use AppBundle\Model\Localisation;
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
     */
    public function indexAction(Request $request)
    {
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
                $nbVeloToSee = 15;
                //Return the formated data array
                $apiData->addData(VelovParc::returnFirstsInArray($nbVeloToSee, $data));
            }

            if ($service instanceof WeatherInfoClimat) {
                $lat = 45.77987;
                $lng = 4.88471;

                //Define an array of WeatherInfoClimat object
                $data = $this->get(WeatherInfoClimat::class)->getWeather($lat,$lng);
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

    public static function getCoordonateByAddress($address)
    {
        // url encode the address
        $address = urlencode($address);

        // google map geocode api url
        $url = "http://maps.google.com/maps/api/geocode/json?address={$address}";

        // get the json response
        $resp_json = file_get_contents($url);

        // decode the json
        $resp = json_decode($resp_json, true);

        // response status will be 'OK', if able to geocode given address
        if($resp['status']=='OK'){

            // get the important data
            $lati = $resp['results'][0]['geometry']['location']['lat'];
            $longi = $resp['results'][0]['geometry']['location']['lng'];
            $formatted_address = $resp['results'][0]['formatted_address'];

            // verify if data is complete
            if($lati && $longi && $formatted_address){

                $loc = new Localisation($lati,$longi);

                // put the data in the array
                $data_arr = array();

                array_push(
                    $data_arr,
                    $loc,
                    $formatted_address
                );

                return $data_arr;

            }else{
                return false;
            }

        }else{
            return false;
        }
    }
}
