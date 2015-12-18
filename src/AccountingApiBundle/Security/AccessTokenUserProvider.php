<?php

namespace AccountingApiBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Class AccessTokenUserProvider
 *
 * @codeCoverageIgnore
 *
 * @package AccountingApiBundle\Security
 */
class AccessTokenUserProvider implements UserProviderInterface
{
    protected $conteiner;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function loadUserByUsername($username)
    {
        $userRepository = $this
            ->container
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AccountingApiBundle:User');

        return $userRepository
            ->findOneByUsername($username);
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }
}
