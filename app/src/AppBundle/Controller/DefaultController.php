<?php

namespace AppBundle\Controller;

use AppBundle\Api\Weather\WeatherInfoClimat;
use AppBundle\Api\Transport\GoogleDirection;
use AppBundle\Entity\Favorite;
use AppBundle\Entity\User;
use AppBundle\Model\Localisation;
use AppBundle\Model\RequestLocalisation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Model\ApiData;
use AppBundle\Model\UserToken;
use AppBundle\Resolver\ApiServiceResolver;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\Velov\VelovParc;
use AppBundle\Service\Velov\Velov;
use AppBundle\Utils;

class DefaultController extends BaseController
{
    const API_DATA_TYPE = 'main';

    /**
     * @Route("/", name="homepage")
     *
     */
    public function indexAction(Request $request, RequestLocalisation $localisation)
    {
        // Simulation of user input to retrieve related services from his keywords
        $services = $this->get(ApiServiceResolver::class)->resolveByApiKeyWords(['metro', 'meteo', 'slip', 'bike']);

        $apiData = new ApiData();
        $apiData->setType(self::API_DATA_TYPE);

        $positionFrom = $localisation->getPositionFrom();
        $positionTo = $localisation->getPositionTo();

        foreach ($services as $service) {
            if ($service instanceof GoogleDirection) {
                $directions = $this->get(GoogleDirection::class)->getDirections($localisation, ["walking", "bicycling", "driving"]);

                if (empty($directions)){
                    $msg = array("message" => "No response from the nav API");
                    return new JsonResponse(
                        json_encode($msg),
                        404,
                        [],
                        true
                    );
                }

                foreach ($directions as $direction) {
                    $apiData->addData($direction);
                }
            }

            if ($service instanceof Velov) {
                //Define an array of VelovArret object
                $data = $this->get(Velov::class)->setVelovParc();
                //Return the formated data array
                $nearFrom = VelovParc::getNearStop($data, $positionFrom);
                $nearTo   = VelovParc::getNearStop($data, $positionTo);

                if (is_null($nearFrom) && is_null($nearTo)){
                    $msg = array("message" => "No response from the bicycles API");
                    return new JsonResponse(
                        json_encode($msg),
                        404,
                        [],
                        true
                    );
                }

                $nearFromDatas = array(
                    "type" => "transport.velov.nearFrom",
                    "data" => array()
                );
                $nearToDatas   = array(
                    "type" => "transport.velov.nearTo",
                    "data" => array()
                );
                array_push($nearFromDatas['data'], $nearFrom);
                array_push($nearToDatas['data'], $nearTo);

                $apiData->addData($nearFromDatas);
                $apiData->addData($nearToDatas);

            }

            if ($service instanceof WeatherInfoClimat) {

                //Define an array of WeatherInfoClimat object
                $weatherFrom = $this->get(WeatherInfoClimat::class)->getWeather($positionFrom);
                $weatherTo   = $this->get(WeatherInfoClimat::class)->getWeather($positionTo);
                $weatherFrom->setType("weatherFrom");
                $weatherTo->setType("weatherTo");

                if (is_null($weatherFrom) && is_null($weatherFrom)){
                    $msg = array("message" => "No response from the weather API");
                    return new JsonResponse(
                        json_encode($msg),
                        404,
                        [],
                        true
                    );
                }

                $apiData->addData($weatherFrom);
                $apiData->addData($weatherTo);
            }
        }

        return $this->jsonResponse(
            $apiData,
            [
                'Access-Control-Allow-Origin' => '*'
            ],
            200
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

    /**
     * @Method("POST")
     * @Route("/favorite/add", name="favorite_add")
     */
    public function favoritePostAction(Request $request, UserToken $userToken)
    {
        $user = $userToken->getUser();
        $data = (new ApiData())
            ->setType('favorite');

        if (empty($user)) {
            $data->setErrors(['Unable to found user from apiKey']);
        } else {
            $address = $request->request->get('address');
            $description = $request->request->get('description');
            $favorite = (new Favorite())
                ->setUser($user)
                ->setAddress($address)
                ->setDescription($description);

            $doctrine = $this->getDoctrine();
            $manager = $doctrine->getManager();
            $manager->persist($favorite);

            try {
                $manager->flush();
            } catch (\Exception $e) {
                $data->setErrors(['Unable to persist favorite']);
            }

            $favorites = $doctrine->getRepository(Favorite::class)
                ->findByUser($user);

            $data->addData($favorites);
        }

        return $this->jsonResponse($data);
    }

    /**
     * @Method("GET")
     * @Route("/favorites", name="favorites_list")
     */
    public function favoriteGetAction(UserToken $userToken)
    {
        $user = $userToken->getUser();
        $data = (new ApiData())
            ->setType('favorite');

        if (empty($user)) {
            $data->setErrors(['Unable to found user from apiKey']);
        } else {
            $favorites = $this->getDoctrine()->getRepository(Favorite::class)
                ->findByUser($user);

            $data->addData($favorites);
        }

        return $this->jsonResponse($data);
    }
}
