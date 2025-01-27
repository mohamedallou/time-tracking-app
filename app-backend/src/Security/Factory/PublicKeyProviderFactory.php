<?php

declare(strict_types=1);

namespace App\Security\Factory;

use App\Security\PublicKeyProvider;

class PublicKeyProviderFactory
{
    public function __construct()
    {
    }

    public function create(string $projectDir): PublicKeyProvider
    {
        return new PublicKeyProvider($projectDir,);
    }
}