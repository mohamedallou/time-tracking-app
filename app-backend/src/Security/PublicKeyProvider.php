<?php

declare(strict_types=1);

namespace App\Security;

class PublicKeyProvider
{
    const PUBLIC_KEY_FILENAME = 'keycloak-public.pem';

    public function __construct(
        private readonly string $projectDir,
    ){
    }

    public function loadKey(): string
    {
        return (string) file_get_contents($this->getStorageDir(). '/'. self::PUBLIC_KEY_FILENAME);
    }

    private function getStorageDir(): string
    {
        return $this->projectDir . '/config/oauth/keys';
    }
}