<?php

declare(strict_types=1);

namespace App\Service\Rule;

use App\Dto\TimeLogStoreDto;
use App\Entity\TimeLog;

class IsWithinShiftDurationLimit
{
    public function __invoke(TimeLog|TimeLogStoreDto $log): bool
    {
        $end = $log->getEnd();
        $startPlus1Day = $log->getStart()->modify('+1 day');
        return $end <= $startPlus1Day;
    }
}