<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * UserProvider constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->repository = $em->getRepository(User::class);
    }

    /**
     * @param string $email
     * @return UserInterface
     */
    public function loadUserByUsername($email)
    {
        $user = $this->repository->findOneByEmail($email);

        if ($user === null) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }

    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    /**
     * @param string $token
     * @return string
     */
    public function getUsernameForApiKey(string $token): ?string
    {
        $user = $this->repository->findOneByApiKey($token);

        if ($user === null) {
            return $user->getUsername();
        }

        return $user->getUsername();
    }
}