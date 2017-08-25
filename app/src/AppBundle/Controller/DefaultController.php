<?php

namespace AppBundle\Controller;

use AppBundle\Api\Weather\WeatherInfoClimat;
use AppBundle\Api\Transport\GoogleDirection;
use AppBundle\Entity\Favorite;
use AppBundle\Entity\User;
use AppBundle\Model\Localisation;
use AppBundle\Model\ApiData;
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
                $directionWalk  = $this->get(GoogleDirection::class)->getDirection($addressFrom, $addressTo, "walking");
                $directionBike  = $this->get(GoogleDirection::class)->getDirection($addressFrom, $addressTo, "bicycling");
                $directionDrive = $this->get(GoogleDirection::class)->getDirection($addressFrom, $addressTo, "driving");

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

    /**
     * @Method("POST")
     * @Route("/favorite/add", name="favorite_add")
     */
    public function favoritePostAction(Request $request)
    {
        $data = (new ApiData())
            ->setType('favorite');

        $token = $request->request->get('token');
        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository(User::class)
            ->findByApiKey($token);

        if (empty($user)) {
            $data->setErrors(['Unable to found user from apiKey']);
        } else {
            $address = $request->request->get('address');
            $description = $request->request->get('description');
            $favorite = (new Favorite())
                ->setUser(current($user))
                ->setAddress($address)
                ->setDescription($description);

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
    public function favoriteGetAction(Request $request)
    {
        $data = (new ApiData())
            ->setType('favorite');

        $token = $request->query->get('token');
        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository(User::class)
            ->findByApiKey($token);

        if (empty($user)) {
            $data->setErrors(['Unable to found user from apiKey']);
        } else {
            $favorites = $doctrine->getRepository(Favorite::class)
                ->findByUser($user);

            $data->addData($favorites);
        }

        return $this->jsonResponse($data);
    }
}
