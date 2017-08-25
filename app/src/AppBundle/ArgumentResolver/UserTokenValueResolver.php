<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 25/08/17
 * Time: 11:34
 */

namespace AppBundle\ArgumentResolver;

use AppBundle\Model\UserToken;
use AppBundle\Provider\UserTokenProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserTokenValueResolver implements ArgumentValueResolverInterface
{
    private $provider;

    public function __construct(UserTokenProvider $provider)
    {
        $this->provider = $provider;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return UserToken::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->provider->getUserToken();
    }
}