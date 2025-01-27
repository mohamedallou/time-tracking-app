<?php

declare(strict_types=1);

namespace App\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Client;
use Jumbojett\OpenIDConnectClient;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    private const TOKEN_CERT_ENDPOINT = '/realms/%s/protocol/openid-connect/certs';
    private const ALLOWED_AUDIENCES = [
        'account'
    ];

    public function __construct(
        private readonly Client $authClient,
        private readonly TokenConfig $tokenConfig,
        private readonly PublicKeyProvider $publicKeyProvider,
    ){
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $jwt = JWT::decode($accessToken, new Key($this->publicKeyProvider->loadKey(), 'RS256'));

        /*
         * The value of aud in the ID token is equal to one of your app's client IDs. This check is necessary to
         * prevent ID tokens issued to a malicious app being used to access data about the same user on your app's
         * backend server.
         */
        if ($jwt->aud !== $this->tokenConfig->audience) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        if ($jwt->azp !== $this->tokenConfig->publicClientId) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        if ($jwt->iss !== $this->tokenConfig->issuer) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        if ($jwt->email_verified !== true) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        var_dump($jwt);die;
        return new UserBadge($accessToken->email);
    }

    public function getPublicKeys(): mixed
    {
        return $this->publicKeyProvider->loadKey();
    }
}