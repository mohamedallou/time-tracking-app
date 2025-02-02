<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Entity\User;
use App\Security\Token\AccessTokenHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenExtractorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * AuthenticationSuccessHandler
 * @author mohamed.allouche
 *
 * This will sync the roles extracted from the access token if set with the local user roles.
 * This way we can allow the Oauth provider to control the roles, and thus the authoriztion,
 * and not only the authentication.
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private readonly AccessTokenHandler $accessTokenHandler,
        private readonly EntityManagerInterface $entityManager,
        #[Autowire(service: 'security.authenticator.access_token.chain_extractor.api')]
        private readonly AccessTokenExtractorInterface $accessTokenExtractor,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $decodedToken = $this->accessTokenHandler->decodeToken($this->accessTokenExtractor->extractAccessToken($request));
        $user = $token->getUser();

        if (!method_exists($user, 'setRoles')) {
            return null;
        }
        // Sync db user roles with roles from access token
        if ($decodedToken->realm_access?->roles) {
            $user->setRoles(
                $decodedToken->realm_access->roles
            );
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return null;
    }
}