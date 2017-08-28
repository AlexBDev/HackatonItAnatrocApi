<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 25/08/17
 * Time: 11:40
 */

namespace AppBundle\Provider;


use AppBundle\Entity\User;
use AppBundle\Model\UserToken;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class UserTokenProvider
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * UserTokenProvider constructor.
     * @param EntityManager $manager
     */
    public function __construct(RequestStack $request, EntityManager $manager)
    {
        $this->manager = $manager;
        $this->request = $request;
    }

    public function getUserToken(): UserToken
    {
        $current = $this->request->getCurrentRequest();
        $token = $current->query->get('token') ?? $current->request->get('token');

        if (empty($token)) {
            throw new \InvalidArgumentException('Any token provided');
        }

        $user = $this->manager->getRepository(User::class)->findOneByApiKey($token);

        return (new UserToken($token))
            ->setUser($user);
    }

    public function getUser($token): User
    {
        return $this->manager->getRepository(User::class)->findOneByApiKey($token);
    }

}