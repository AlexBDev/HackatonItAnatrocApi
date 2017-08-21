<?php

namespace AppBundle\Provider;


use AppBundle\Client\ClientRequest;
use AppBundle\Model\Localisation;
use AppBundle\Parser\TextParser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ClientRequestProvider
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ClientRequest
     */
    private $clientRequest;

    /**
     * ClientRequestProvider constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->clientRequest = $this->createClientRequest();
    }

    /**
     * @return mixed
     */
    private function getRequestStack(): RequestStack
    {
        return $this->requestStack;
    }

    /**
     * @return ClientRequest
     */
    public function getClientRequest(): ClientRequest
    {
        return $this->clientRequest;
    }


    /**
     * @return ClientRequest
     */
    private function createClientRequest()
    {
        $request = $this->getRequestStack()->getCurrentRequest();
        $options = self::resolve($request->query->all());

        return (new ClientRequest($options['client_text']))
            ->setCurrentLocalisation($options['current_localisation'])
            ->setFrom($options['destination_from'])
            ->setTo($options['destination_to']);
    }

    /**
     * @param array $options
     * @return array
     */
    private static function resolve(array $options): array
    {
        // @todo Use now for development need to be remove
        $options['client_text'] = 'Je veux aller de Chemin La Broutette à Bellcourt1';
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'current_localisation' => null,
            'client_text' => '',
            'destination_from' => null,
            'destination_to' => null,
            'parameters' => [],
        ]);

        $resolver
            ->setNormalizer('destination_from', function (Options $options, $value) {
                $destination = TextParser::recognizeDestination($options['client_text']);

                return $destination['from'];
            })
            ->setNormalizer('destination_to', function (Options $options, $value) {
                $destination = TextParser::recognizeDestination($options['client_text']);

                return $destination['to'];
            })
        ;

        $resolver
            ->setAllowedTypes('current_localisation', ['null', Localisation::class])
            ->setAllowedTypes('destination_from', ['null', 'string', Localisation::class])
            ->setAllowedTypes('destination_to', ['null', 'string', Localisation::class])
            ->setAllowedTypes('client_text', 'string')
            ->setAllowedTypes('parameters', 'array');

        return $resolver->resolve($options);
    }
}