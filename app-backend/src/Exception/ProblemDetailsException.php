<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * ProblemDetailsException
 * @author mohamed.allouche
 * Implements Problem Details for HTTP APIs RFC
 */
abstract class ProblemDetailsException extends \Exception
{
    public function __construct(
        private readonly string $type,
        private readonly string $detail,
        private readonly string $title,
        /**
         * @var int $status
         * The https status
         */
        private readonly int $status = 400,
        private readonly bool $useMessageForUser = true,
    ) {
        parent::__construct($detail, $status);
    }

    /**
     * Can also be used as key for the translation component
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     *
     * @return string
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function canUseMessageForUser(): bool
    {
        return $this->useMessageForUser;
    }
}