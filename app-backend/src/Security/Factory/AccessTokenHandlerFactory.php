<?php

declare(strict_types=1);

namespace App\Security\Factory;

use App\Security\PublicKeyProvider;
use App\Security\Token\AccessTokenHandler;
use App\Security\Token\TokenConfig;

class AccessTokenHandlerFactory
{
    public function __construct(
        private readonly TokenConfig $tokenConfig,
        private readonly PublicKeyProvider $publicKeyProvider,
    ) {
    }

    public function create(): AccessTokenHandler
    {
        return new AccessTokenHandler(
            $this->tokenConfig,
            $this->publicKeyProvider,
        );
    }
}