<?php

declare(strict_types=1);

namespace App\Security;

class TokenConfig
{
    public function __construct(
        public readonly string $realm,
        public readonly string $issuer,
        public readonly string $providerUrl,
        public readonly string $publicClientId,
        public readonly string $secretClientId,
        public readonly string $secret,
        public readonly string $audience,
    ) {
    }
}