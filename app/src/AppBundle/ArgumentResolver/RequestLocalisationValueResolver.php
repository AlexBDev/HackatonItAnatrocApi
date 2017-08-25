<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 25/08/17
 * Time: 14:52
 */

namespace AppBundle\ArgumentResolver;


use AppBundle\Model\RequestLocalisation;
use AppBundle\Provider\RequestLocationProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestLocalisationValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var RequestLocationProvider
     */
    private $provider;

    /**
     * RequestLocalisationValueResolver constructor.
     * @param RequestLocationProvider $provider
     */
    public function __construct(RequestLocationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return RequestLocalisation::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->provider->getRequestLocation();
    }
}