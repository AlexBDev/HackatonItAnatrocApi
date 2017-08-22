<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 22/08/17
 * Time: 15:49
 */

namespace AppBundle\OAuth;


use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;

class UserProvider extends FOSUBUserProvider
{
    public function __construct(UserManagerInterface $userManager, array $properties)
    {
        parent::__construct($userManager, $properties);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        dump($response);die;
    }
}