<?php

declare(strict_types=1);

namespace App\Exception;

class TimeLogNotFoundException extends TimeLogDomainException
{
    private const TYPE = 'time_log_not_found';

    public function __construct(string $detail, string $title, int $status = 400, bool $useMessageForUser = true)
    {
        parent::__construct(self::TYPE, $detail, $title, $status, $useMessageForUser);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE;
    }
}