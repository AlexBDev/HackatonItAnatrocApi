<?php
/**
 * Created by PhpStorm.
 * User: alexis
 * Date: 25/08/17
 * Time: 22:51
 */

namespace AppBundle\Response;


use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{
    /**
     * @param $data
     * @param array $headers
     * @param int $status
     * @param bool $json
     * @return JsonResponse
     */
    public static function response(
        $data,
        array $headers  = [],
        int $status = 200,
        $json = false
    ): JsonResponse {
        if (!$json) {
            $serializer = SerializerBuilder::create()->build();
            $data = $serializer->serialize($data, 'json');
        }

        return new JsonResponse($data, $status, $headers, true);
    }
}