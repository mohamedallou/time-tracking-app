<?php

declare(strict_types=1);

namespace App\Security\Factory;

use App\Security\AccessTokenHandler;
use App\Security\PublicKeyProvider;
use App\Security\TokenConfig;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\Container;

class AccessTokenHandlerFactory
{
    public function __construct(
        private readonly Container $container,
        private readonly TokenConfig $tokenConfig,
        private readonly PublicKeyProvider $publicKeyProvider,
    ) {
    }

    public function create(): AccessTokenHandler
    {
        $oauthServerUrl = $this->container->getParameter('oauth.server_url');
        $client = new Client(
            [
                'base_uri' => $oauthServerUrl,
                'timeout'  => 2.0,
            ]
        );
        return new AccessTokenHandler(
            $client,
            $this->tokenConfig,
            $this->publicKeyProvider,
        );
    }
}