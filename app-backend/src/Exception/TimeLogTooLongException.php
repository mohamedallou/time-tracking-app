<?php

declare(strict_types=1);

namespace App\Exception;

class TimeLogTooLongException extends TimeLogDomainException
{
    private const TYPE = 'time_log_too_long';
    public function __construct(string $detail,  string $title, bool $useMessageForUser = true)
    {
        parent::__construct(self::TYPE, $detail, $title, useMessageForUser: $useMessageForUser);
    }

    public function getType(): string
    {
        return self::TYPE;
    }
}