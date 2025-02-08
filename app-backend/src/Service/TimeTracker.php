<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TimeLog;
use App\Entity\User;
use App\Event\InvalidTimeLogFound;
use App\Exception\TimeLogDomainException;
use App\Exception\TimeLogTooLongException;
use App\Repository\TimeLogRepository;
use App\Service\Rule\IsWithinShiftDurationLimit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class TimeTracker
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly IsWithinShiftDurationLimit $isWithinShiftDurationLimit,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function startTimeTracking(?User $user): void
    {
        $this->messageBus->dispatch(new InvalidTimeLogFound(26));

        $lastTimeLog = $this->findCurrentActiveTimeLogForUser($user);

        if ($lastTimeLog !== null && $lastTimeLog->getEnd() === null && $lastTimeLog->isValid()) {
            throw new TimeLogDomainException(
                'active_tracking_found',
                'An active tracking is still on, it must be stopped before starting again',
                'Active Tracking Found'
            );
        }

        $timeLog = new TimeLog();
        $timeLog->setStart(new \DateTimeImmutable());
        $timeLog->setUser($this->security->getUser());
        $this->entityManager->persist($timeLog);
        $this->entityManager->flush();
    }

    public function stopTimeTracking(?User $user): void
    {
        $lastTimeLog = $this->findCurrentActiveTimeLogForUser($user);
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
            throw new TimeLogTooLongException(
                'The shift is too long',
                'Shift Too Long'
            );
        }

        $this->entityManager->persist($lastTimeLog);
        $this->entityManager->flush();
    }

    public function findCurrentActiveTimeLogForUser(#[CurrentUser] ?User $user): ?TimeLog
    {
        /** @var TimeLogRepository $repo */
        $repo = $this->entityManager->getRepository(TimeLog::class);
        $lastTimeLog = $repo->findLastLogForUser($user);

        if ($lastTimeLog === null || !$lastTimeLog->isValid() || $lastTimeLog->getEnd() !== null) {
            return null;
        }

        return $lastTimeLog;
    }
}