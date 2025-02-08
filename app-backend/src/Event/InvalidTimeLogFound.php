<?php

declare(strict_types=1);

namespace App\Event;

class InvalidTimeLogFound
{
    public function __construct(private readonly int $timeLogId)
    {
    }

    public function getTimeLogId(): int
    {
        return $this->timeLogId;
    }
}