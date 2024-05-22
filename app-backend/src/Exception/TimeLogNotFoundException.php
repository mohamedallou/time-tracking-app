<?php

declare(strict_types=1);

namespace App\Exception;

class TimeLogNotFoundException extends TimeLogDomainException
{
    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'TIME_LOG_NOT_FOUND_EXCEPTION';
    }
}