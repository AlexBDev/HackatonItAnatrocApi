<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 24/08/17
 * Time: 10:04
 */

namespace AppBundle\Security;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class SecurityLoginHandler implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        return new JsonResponse([
            'token' => $user->getApiKey(),
        ]);
    }
}