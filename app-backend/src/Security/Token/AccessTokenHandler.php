<?php

declare(strict_types=1);

namespace App\Security\Token;

use App\Entity\User;
use App\Security\PublicKeyProvider;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * AccessTokenHandler
 * @author mohamed.allouche
 * OidcTokenHandler does not support RS256 and no custom claim checks
 */
class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly TokenConfig $tokenConfig,
        private readonly PublicKeyProvider $publicKeyProvider,
    ){
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $jwt = $this->decodeToken($accessToken);

        /*
         * The value of aud in the ID token is equal to one of your app's client IDs. This check is necessary to
         * prevent ID tokens issued to a malicious app being used to access data about the same user on your app's
         * backend server.
         */
        $audience = is_array($jwt->aud) ? $jwt->aud : [$jwt->aud];
        if (!in_array($this->tokenConfig->audience, $audience, true)) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        // Authorized party check, make sure that our application is the one authorized
        if ($jwt->azp !== $this->tokenConfig->publicClientId) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        if ($jwt->iss !== $this->tokenConfig->issuer) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        if ($jwt->email_verified !== true) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($jwt->email);
    }

    /**
     * @param string $accessToken
     * @return \stdClass
     */
    public function decodeToken(string $accessToken): \stdClass
    {
        return JWT::decode($accessToken, new Key($this->publicKeyProvider->loadKey(), 'RS256'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

    }
}