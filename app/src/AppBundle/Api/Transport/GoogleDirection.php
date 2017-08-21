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
use GuzzleHttp\Client;

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
     * @var Client
     */
    private $guzzle;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * GoogleDirection constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters['google_direction'];
        $this->guzzle = new Client(['connect_timeout' => 3]);
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
     * @return Client
     */
    private function getGuzzle(): Client
    {
        return $this->guzzle;
    }

    /**
     * @return array
     */
    private function getParameters(): array
    {
        return $this->parameters;
    }

    public function getDirection()
    {
        // @todo Wait for input user feature to pass location
        $parameters = [
            'origin' => 'Disneyland',
            'destination' => 'Universal+Studios+Hollywood4'
        ];

        // @todo Refactor later need to push for the moment
        $parameters['units'] = 'metric';
        $parameters['mode'] = 'driving';
        $parameters['key'] = $this->getParameters()['key'];

        $query = \GuzzleHttp\Psr7\build_query($parameters);
        $url = $this->getParameters()['url'].'json?'.$query;
        $response = $this->getGuzzle()->get($url);
        $content = $response->getBody()->getContents();

        $object = \GuzzleHttp\json_decode($content);

        return $this->transformToTransport($object, $parameters['mode']);
    }

    private function transformToTransport($object, string $transportType)
    {
        $transports = [];
        $transport = new TransportData();
        $type = $this->getType().'.'.$transportType;
        $transport->setType($type);
        
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

