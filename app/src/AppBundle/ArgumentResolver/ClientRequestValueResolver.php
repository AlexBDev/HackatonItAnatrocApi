<?php

namespace AppBundle\ArgumentResolver;

use AppBundle\Client\ClientRequest;
use AppBundle\Provider\ClientRequestProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ClientRequestValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ClientRequestProvider
     */
    private $provider;

    /**
     * ClientRequestValueResolver constructor.
     * @param ClientRequestProvider $provider
     */
    public function __construct(ClientRequestProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return ClientRequestProvider
     */
    public function getProvider(): ClientRequestProvider
    {
        return $this->provider;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return (ClientRequest::class === $argument->getType());
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->getProvider()->getClientRequest();
    }

}