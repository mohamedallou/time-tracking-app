<?php

declare(strict_types=1);

namespace App\Exception;

class TimeLogOverlappingException extends TimeLogDomainException
{
    private const TYPE = 'time_log_overlapping';
    public function __construct(string $detail,  string $title, bool $useMessageForUser = true)
    {
        parent::__construct(self::TYPE, $detail, $title, useMessageForUser: $useMessageForUser);
    }

    public function getType(): string
    {
        return self::TYPE;
    }
}