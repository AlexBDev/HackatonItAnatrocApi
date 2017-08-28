<?php

namespace AppBundle\Controller;

use AppBundle\Api\Weather\WeatherInfoClimat;
use AppBundle\Api\Transport\GoogleDirection;
use AppBundle\Collector\ApiExceptionCollector;
use AppBundle\Entity\Favorite;
use AppBundle\Entity\User;
use AppBundle\Handler\ApiExceptionHandler;
use AppBundle\Handler\ApiHandlerExecption;
use AppBundle\Model\Localisation;
use AppBundle\Model\RequestLocalisation;
use AppBundle\Model\Weather\WeatherData;
use AppBundle\Provider\RequestLocationProvider;
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
    public function indexAction(Request $request)
    {
        $apiData = new ApiData();
        $apiData->setType(self::API_DATA_TYPE);

        $exceptionHandler = $this->get(ApiExceptionHandler::class);
        $localisation = $this->get(RequestLocationProvider::class)->getRequestLocation();
        $positionFrom = $localisation->getPositionFrom();
        $positionTo = $localisation->getPositionTo();
        $container = $this->container;

        $services = $this->get(ApiServiceResolver::class)->resolveByApiKeyWords(['metro', 'meteo', 'slip', 'bike']);
        foreach ($services as $service) {
            if ($service instanceof GoogleDirection) {
                $directions = $exceptionHandler->handle(function () use ($container, $localisation) {
                    return $this->get(GoogleDirection::class)->getDirections(
                        $localisation,
                        GoogleDirection::getTransportModes()
                    );
                });

                foreach ($directions as $direction) {
                    $apiData->addData($direction);
                }
            }

            if ($service instanceof Velov) {
                //Define an array of VelovArret object
                $data = $this->get(Velov::class)->setVelovParc();
                //Return the formated data array
                $nearFrom = $exceptionHandler->handle(function () use ($data, $positionFrom) {

                    return VelovParc::getNearStop($data, $positionFrom);
                });

                $nearTo = $exceptionHandler->handle(function () use ($data, $positionTo) {
                    return VelovParc::getNearStop($data, $positionTo);
                });

                if (is_array($nearFrom)) {
                    $apiData->addData([
                        "type" => "transport.velov.nearFrom",
                        "data" => $nearFrom,
                    ]);
                }

                if (is_array($nearTo)) {
                    $apiData->addData([
                        "type" => "transport.velov.nearTo",
                        "data" => $nearTo,
                    ]);
                }
            }

            if ($service instanceof WeatherInfoClimat) {
                $weatherInfo = $this->get(WeatherInfoClimat::class);

                $weatherFrom = $exceptionHandler->handle(function () use ($weatherInfo, $positionFrom) {
                    return $weatherInfo->getWeather($positionFrom);
                });

                $weatherTo = $exceptionHandler->handle(function () use ($weatherInfo, $positionTo) {
                    return $weatherInfo->getWeather($positionTo);
                });

                if ($weatherFrom instanceof WeatherData) {
                    $weatherFrom->setType("weatherFrom");
                    $apiData->addData($weatherFrom);
                }

                if ($weatherTo instanceof WeatherData) {
                    $weatherTo->setType("weatherTo");
                    $apiData->addData($weatherTo);
                }
            }
        }

        return $this->response(
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
     * @Route("/user/favorite", name="favorite_add")
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

        return $this->response($data);
    }

    /**
     * @Method("GET")
     * @Route("/user/favorites", name="favorite_list")
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

        return $this->response($data);
    }
}
