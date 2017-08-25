<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 24/08/17
 * Time: 17:04
 */

namespace AppBundle\Controller;

use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends Controller
{
    public function jsonResponse($data, array $headers  = [], int $status = 200, $json = false)
    {
        if (!$json) {
            $serializer = SerializerBuilder::create()->build();
            $jsonContent = $serializer->serialize($data, 'json');
        }

        return new JsonResponse($jsonContent, $status, $headers, true);
    }
}