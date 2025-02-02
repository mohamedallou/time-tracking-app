<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TimeLog;
use App\Event\InvalidTimeLogFound;
use App\Exception\TimeLogDomainException;
use App\Repository\TimeLogRepository;
use App\Service\Rule\IsWithinShiftDurationLimit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;

class TimeTracker
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly IsWithinShiftDurationLimit $isWithinShiftDurationLimit,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function startTimeTracking(): void
    {
        $lastTimeLog = $this->findCurrentActiveTimeLog();

        if ($lastTimeLog !== null && $lastTimeLog->getEnd() === null && $lastTimeLog->isValid()) {
            throw new TimeLogDomainException(
                'active_tracking_found',
                'An active tracking is still on, it must be stopped before starting again',
                'Active Tracking Found'
            );
        }

        $timeLog = new TimeLog();
        $timeLog->setStart(new \DateTimeImmutable());
        $this->entityManager->persist($timeLog);
        $this->entityManager->flush();
    }

    public function stopTimeTracking(): void
    {
        $lastTimeLog = $this->findCurrentActiveTimeLog();
        if ($lastTimeLog === null || $lastTimeLog->getEnd() !== null) {
            throw new TimeLogDomainException(
                'active_tracking_not_found',
                'An active tracking must exist to be stopped',
                'Active Tracking Not Found'
            );
        }

        // We can't have a shift longer than the specified period
        //TODO: cron that closes open shifts
        $end = new \DateTimeImmutable();
        $lastTimeLog->setEnd($end);
        if (!($this->isWithinShiftDurationLimit)($lastTimeLog)) {
            // Set the log to invalid, so that the manager can look into it
            $lastTimeLog->setValid(false);
            $this->messageBus->dispatch(new InvalidTimeLogFound($lastTimeLog->getId()));
            $lastTimeLog->setEnd(null);
        }

        $this->entityManager->persist($lastTimeLog);
        $this->entityManager->flush();
    }

    public function findCurrentActiveTimeLog(): ?TimeLog
    {
        /** @var TimeLogRepository $repo */
        $repo = $this->entityManager->getRepository(TimeLog::class);
        $lastTimeLog = $repo->findLastLogForUser($this->security->getUser());

        if ($lastTimeLog === null || !$lastTimeLog->isValid() || $lastTimeLog->getEnd() !== null) {
            return null;
        }

        return $lastTimeLog;
    }
}