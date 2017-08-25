<?php

namespace AppBundle\Controller;

use AppBundle\Api\Weather\WeatherInfoClimat;
use AppBundle\Api\Transport\GoogleDirection;
use AppBundle\Entity\Favorite;
use AppBundle\Entity\User;
use AppBundle\Model\Localisation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Model\ApiData;
use AppBundle\Model\UserToken;
use AppBundle\Resolver\ApiServiceResolver;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
            $msg = array("message" => "Params addressFrom and addressTo must be set");
            return new JsonResponse(
                json_encode($msg),
                500,
                [],
                true
            );
        }

        $locFrom = $locTo = null;
        // Si la latitude et la longitude ne sont pas passées en param on les récupère
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
                $directionWalk  = $this->get(GoogleDirection::class)->getDirection($addressFrom, $addressTo, "walking");
                $directionBike  = $this->get(GoogleDirection::class)->getDirection($addressFrom, $addressTo, "bicycling");
                $directionDrive = $this->get(GoogleDirection::class)->getDirection($addressFrom, $addressTo, "driving");

                if (is_null($directionWalk) && is_null($directionBike) && is_null($directionDrive)){
                    $msg = array("message" => "No response from the nav API");
                    return new JsonResponse(
                        json_encode($msg),
                        404,
                        [],
                        true
                    );
                }

                $apiData->addData($directionWalk);
                $apiData->addData($directionBike);
                $apiData->addData($directionDrive);
            }

            if ($service instanceof Velov) {
                //Define an array of VelovArret object
                $data = $this->get(Velov::class)->setVelovParc();
                //Return the formated data array
                $nearFrom = VelovParc::getNearStop($data, $locFrom);
                $nearTo   = VelovParc::getNearStop($data, $locTo);

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
                $weatherFrom = $this->get(WeatherInfoClimat::class)->getWeather($locFrom);
                $weatherTo   = $this->get(WeatherInfoClimat::class)->getWeather($locTo);
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
