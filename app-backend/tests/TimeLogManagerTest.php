<?php

declare(strict_types=1);

namespace App\Tests;

use App\Dto\TimeLogStoreDto;
use App\Entity\TimeLog;
use App\Service\TimeLogManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TimeLogManagerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testCreateTimeLog(): void
    {
        $subject = new TimeLogManager($this->entityManager);
        $dto = new TimeLogStoreDto(
            new \DateTimeImmutable('2020-01-01 08:00'),
            new \DateTimeImmutable('2020-01-01 18:00')
        );

        $this->entityManager
            ->expects(static::any())
            ->method('persist')
            ->with(new Callback(function (TimeLog $timeLog) use ($dto) {
                static::assertEquals($timeLog->getStart(), $dto->start);
                static::assertEquals($timeLog->getEnd(), $dto->end);
                return true;
            }));
        $timeLog = $subject->createTimeLog($dto);
        static::assertEquals($timeLog->getStart(), $dto->start);
        static::assertEquals($timeLog->getEnd(), $dto->end);

    }
}