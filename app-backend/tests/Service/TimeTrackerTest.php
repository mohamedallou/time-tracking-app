<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\TimeLog;
use App\Entity\User;
use App\Event\InvalidTimeLogFound;
use App\Exception\TimeLogDomainException;
use App\Exception\TimeLogTooLongException;
use App\Repository\TimeLogRepository;
use App\Service\Rule\IsWithinShiftDurationLimit;
use App\Service\TimeTracker;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;

class TimeTrackerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private Security&MockObject $security;
    private IsWithinShiftDurationLimit&MockObject $isWithinShiftDurationLimit;
    private MessageBusInterface&MockObject $messageBus;
    private TimeLogRepository&MockObject $timeLogRepository;
    private TimeTracker $timeTracker;
    private User $user;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->isWithinShiftDurationLimit = $this->createMock(IsWithinShiftDurationLimit::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->timeLogRepository = $this->createMock(TimeLogRepository::class);
        $this->entityManager
            ->method('getRepository')
            ->willReturn($this->timeLogRepository);

        $this->timeTracker = new TimeTracker(
            $this->entityManager,
            $this->security,
            $this->isWithinShiftDurationLimit,
            $this->messageBus
        );

        $this->user = new User();
    }

    public function testStartTimeTracking(): void
    {
        $this->security
            ->method('getUser')
            ->willReturn($this->user);

        $this->timeLogRepository
            ->method('findLastLogForUser')
            ->willReturn(null);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(TimeLog::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->timeTracker->startTimeTracking($this->user);
    }

    public function testStopTimeTracking(): void
    {
        $timeLog = new TimeLog();
        $timeLog->setStart(new \DateTimeImmutable());

        $this->timeLogRepository
            ->method('findLastLogForUser')
            ->willReturn($timeLog);

        $this->isWithinShiftDurationLimit
            ->method('__invoke')
            ->willReturn(true);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($timeLog);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->timeTracker->stopTimeTracking($this->user);
    }

    public function testStopTimeTrackingThrowsExceptionWhenNoActiveLog(): void
    {
        $this->timeLogRepository
            ->method('findLastLogForUser')
            ->willReturn(null);

        $this->expectException(TimeLogDomainException::class);

        $this->timeTracker->stopTimeTracking($this->user);
    }

    public function testFindCurrentActiveTimeLogForUser(): void
    {
        $timeLog = new TimeLog();
        $timeLog->setStart(new \DateTimeImmutable());

        $this->timeLogRepository
            ->method('findLastLogForUser')
            ->willReturn($timeLog);

        $result = $this->timeTracker->findCurrentActiveTimeLogForUser($this->user);

        $this->assertSame($timeLog, $result);
    }
}