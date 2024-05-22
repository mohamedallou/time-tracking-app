<?php

declare(strict_types=1);

namespace App\Dto;

readonly class TimeLogQueryDto
{
    public function __construct(private ?string $from = null, private ?string $to = null)
    {
    }

    public function getFromDate(): ?\DateTimeImmutable
    {
        return $this->from === null ? null : new \DateTimeImmutable($this->from);
    }

    public function getToDate(): ?\DateTimeImmutable
    {
        return $this->to === null ? null : new \DateTimeImmutable($this->to);
    }
}