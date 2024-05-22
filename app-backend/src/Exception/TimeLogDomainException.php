<?php

declare(strict_types=1);

namespace App\Exception;

class TimeLogDomainException extends \Exception
{
    public function __construct(
        string $message = "",
        private readonly bool $useMessageForUser = true,
    ) {
        parent::__construct($message);
    }

    public function canUseMessageForUser(): bool
    {
        return $this->useMessageForUser;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'TIME_LOG_NOT_FOUND_EXCEPTION';
    }

    public function getTitle(): string
    {
        return $this->getKey(). '.title';
    }

    public function getDetails(): string
    {
        return $this->getKey().'.details';
    }
}