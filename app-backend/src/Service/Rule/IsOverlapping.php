<?php

declare(strict_types=1);

namespace App\Service\Rule;

use App\Entity\TimeLog;
use Doctrine\ORM\EntityManagerInterface;

class IsOverlapping
{
    public function __construct(EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(TimeLog $timeLog)
    {
        // TODO: Implement __invoke() method.
    }
}