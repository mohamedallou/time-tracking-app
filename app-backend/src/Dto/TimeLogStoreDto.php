<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

readonly class TimeLogStoreDto
{
    public function __construct(
        #[Assert\LessThan(propertyPath: 'end', groups: ['edit', 'create'])]
        #[Assert\LessThanOrEqual(value: 'now', groups: ['edit', 'create'])]
        public \DateTimeImmutable $start,
        #[Assert\NotNull(groups: ['edit'])]
        public ?\DateTimeImmutable $end = null,
    ) {
    }

    #[Assert\Callback(groups: ['edit', 'create'])]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        if ($this->end !== null && (($this->end->getTimestamp() - $this->start->getTimestamp())/ 3600) >= 24) {
            $context->buildViolation('The difference between start and end is more than 24 hours')
                ->atPath('end')
                ->addViolation();
        }
    }
}