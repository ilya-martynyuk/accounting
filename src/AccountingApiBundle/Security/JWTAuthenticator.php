<?php

namespace AccountingApiBundle\Security;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class JWTAuthenticator
 *
 * @codeCoverageIgnore
 *
 * @package AccountingApiBundle\Security
 */
class JWTAuthenticator implements SimplePreAuthenticatorInterface
{
    protected $jwtSerivice;
    protected $httpUtils;

    public function __construct($jwtSerivice, HttpUtils $httpUtils)
    {
        $this->jwtSerivice = $jwtSerivice;
        $this->httpUtils = $httpUtils;
    }

    public function createToken(Request $request, $providerKey)
    {
        $accessToken = $request->headers->get('X_BEARER_TOKEN');

        if (!$accessToken) {
            throw new AccessDeniedHttpException('X-Bearer-Token should be provided');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $accessToken,
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof AccessTokenUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $jwt = $token->getCredentials();
        $userData = $this->jwtSerivice->getData($jwt);

        if (false === $userData) {
            throw new AccessDeniedHttpException('Invalid or expired X-Bearer-Token');
        }

        $username = $userData->username;

        $user = $userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
            $user,
            $jwt,
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}