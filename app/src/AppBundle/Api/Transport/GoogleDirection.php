<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 04/07/17
 * Time: 14:27
 */

namespace AppBundle\Api\Transport;


use AppBundle\Api\AbstractApi;
use AppBundle\Api\ApiKeywordInterface;
use AppBundle\Model\Localisation;
use AppBundle\Model\Transport\TransportData;
use AppBundle\Request\ClientREST;

/**
 * Provide direction for driving (for now), with distance, duration, start & end location
 *
 * Class GoogleDirection
 * @package AppBundle\Api\Transport
 */
class GoogleDirection extends AbstractApi implements ApiKeywordInterface
{
    /**
     * @var string
     */
    const API_DATA_TYPE = 'transport.google_direction';

    /**
     * @var string
     */
    protected $type = self::API_DATA_TYPE;

    /**
     * @var ClientREST
     */
    private $clientRest;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * GoogleDirection constructor.
     * @param ClientREST $client
     * @param array $parameters
     */
    public function __construct(ClientREST $client, array $parameters)
    {
        $this->parameters = $parameters['google_direction'];
        $this->clientRest = $client;
    }

    /**
     * @return array
     */
    public static function getApiKeywords(): array
    {
        return [
            'transport',
            'direction',
            'voiture',
            'metro',
        ];
    }

    /**
     * @return ClientREST
     */
    private function getClient(): ClientREST
    {
        return $this->clientRest;
    }

    /**
     * @return array
     */
    private function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param $from null|string|Localisation
     * @param $to null|string|Localisation
     * @return array
     */
    public function getDirection($from, $to)
    {
        // @todo Wait for input user feature to pass location
        $parameters = [
            'origin' => $from,
            'destination' => $to
        ];

        // @todo Refactor later need to push for the moment
        $parameters['units'] = 'metric';
        $parameters['mode'] = 'driving';
        $parameters['key'] = $this->getParameters()['key'];

        $query = \GuzzleHttp\Psr7\build_query($parameters);
        $url = $this->getParameters()['url'].'json?'.$query;

        try {
            $response = $this->getClient()->get($url);
            $content = $response->getBody()->getContents();
            $object = \GuzzleHttp\json_decode($content);
        } catch (\Exception $e) {
            $object = null;
        }


        return $this->transformToTransport($object, $parameters['mode']);
    }

    private function transformToTransport($object, string $transportType): array
    {
        $transports = [];
        $transport = new TransportData();
        $type = $this->getType().'.'.$transportType;
        $transport->setType($type);

        if ($object === null || property_exists($object, 'error_message') && !empty($object->error_message)) {
            $transport->addError('Impossible de récupérer les données de Google Direction.');
            $transports[] = $transport;

            return $transports;
        }

        
        foreach ($object->routes as $record) {
            $legs = $record->legs[0];
            $transport->setDistance($legs->distance->text)
                ->setDuration($legs->duration->text)
                ->setStartAddressName($legs->start_address)
                ->setEndAddressName($legs->end_address)
                ->setStartLocation(new Localisation($legs->start_location->lat, $legs->start_location->lng))
                ->setEndLocation(new Localisation($legs->end_location->lat, $legs->end_location->lng));


            $transports[] = $transport;
        }

        return $transports;
    }
}

