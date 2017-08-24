<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 22/08/17
 * Time: 15:49
 */

namespace AppBundle\OAuth;


use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\FacebookResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GoogleResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;

class UserProvider extends FOSUBUserProvider
{
    /**
     * UserProvider constructor.
     * @param UserManagerInterface $userManager
     * @param array $properties
     */
    public function __construct(UserManagerInterface $userManager, array $properties)
    {
        parent::__construct($userManager, $properties);
    }

    /**
     * @param UserResponseInterface $response
     * @return UserInterface|null
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response): ?UserInterface
    {
        return $this->loadUser($response);
    }


    /**
     * @param UserResponseInterface $response
     * @return UserInterface|null
     */
    private function loadUser(UserResponseInterface $response): ?UserInterface
    {
        $resource = $response->getResourceOwner();
        switch (get_class($resource)) {
            case GoogleResourceOwner::class:
                return $this->loadUserForGoogleOAuth($response);
            case FacebookResourceOwner::class:
                return $this->loadUserForFacebookOAuth($response);
            default;
                return null;
        }
    }

    /**
     * @todo refactor
     * @param UserResponseInterface $userResponse
     * @return UserInterface|null
     */
    private function loadUserForGoogleOAuth(UserResponseInterface $userResponse): UserInterface
    {
        $data = $userResponse->getResponse();
        $user = $this->userManager->findUserByEmail($data['email']);

        if ($user === null) {
            $user = (new User())
                ->setUsername($data['email'])
                ->setEmail($data['email'])
                ->setGoogleId($data['id'])
                ->setPlainPassword(md5(uniqid())) // Generate random password
                ->setApiKey(md5(uniqid())) // api
            ;

            $this->userManager->updateUser($user);
        }

        return $user;
    }

    /**
     * @todo refactor
     * @param UserResponseInterface $userResponse
     * @return UserInterface|null
     */
    private function loadUserForFacebookOAuth(UserResponseInterface $userResponse): UserInterface
    {
        $data = $userResponse->getResponse();

        $user = $this->userManager->findUserByEmail($data['email']);

        if ($user === null) {
            $user = (new User())
                ->setUsername($data['email'])
                ->setEmail($data['email'])
                ->setFacebookId($data['id'])
                ->setPlainPassword(md5(uniqid())) // Generate random password
                ->setApiKey(md5(uniqid())) // api
            ;

            $this->userManager->updateUser($user);
        }

        return $user;
    }
}