<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * ProblemDetailsException
 * @author mohamed.allouche
 * Implements Problem Details for HTTP APIs RFC
 */
class ProblemDetailsException
{
    public function __construct(
        private readonly string $type,
        private readonly string $detail,
        private readonly string $title,
        private readonly string $status,
    ) {
    }

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

    public function getStatus(): string
    {
        return $this->status;
    }
}