<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TimeLogStoreDto;
use App\Entity\TimeLog;
use App\Exception\TimeLogNotFoundException;
use App\Exception\TimeLogOverlappingException;
use App\Repository\TimeLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Writer;

readonly class TimeLogManager
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    public function createTimeLog(TimeLogStoreDto $dto): TimeLog
    {
        //We cannot create overlapping timelogs
        /** @var TimeLogRepository $repo */
        $repo = $this->manager->getRepository(TimeLog::class);
        $overlappingLog = $repo->findLogWithOverlappingInterval($dto->start, $dto->end);
        if ($overlappingLog !== null) {
            throw new TimeLogOverlappingException(
                detail: 'Overlapping log found',
                title: 'Overlapping time log'
            );
        }

        $timeLog = new TimeLog();
        $timeLog->setStart($dto->start);
        $timeLog->setEnd($dto->end);
        $this->manager->persist($timeLog);
        $this->manager->flush();

        return $timeLog;
    }

    public function updateTimeLog(TimeLogStoreDto $dto, int $logId): TimeLog
    {
        //We cannot create overlapping timelogs
        /** @var TimeLogRepository $repo */
        $repo = $this->manager->getRepository(TimeLog::class);
        $currLog = $repo->find($logId);

        if ($currLog === null) {
            throw new TimeLogNotFoundException('Requested log was not found', 'Log not found');
        }

        $overlappingLog = $repo->findLogWithOverlappingInterval($dto->start, $dto->end, $currLog);

        if ($overlappingLog !== null) {
            throw new TimeLogOverlappingException(
                'Overlapping time log found',
                'Overlapping log found'
            );
        }

        $timeLog = $currLog;
        $timeLog->setStart($dto->start);
        $timeLog->setEnd($dto->end);
        $this->manager->persist($timeLog);
        $this->manager->flush();

        return $timeLog;
    }

    public function deleteTimeLog(int $id): void
    {
        /** @var TimeLogRepository $repo */
        $repo = $this->manager->getRepository(TimeLog::class);
        $currLog = $repo->find($id);

        if ($currLog === null) {
            throw new TimeLogNotFoundException('Log not found', 'Log not found');
        }

        $this->manager->remove($currLog);
        $this->manager->flush();
    }

    public function findOneTimeLog(int $id): TimeLog
    {
        /** @var TimeLogRepository $repo */
        $repo = $this->manager->getRepository(TimeLog::class);

        $currLog = $repo->find($id);

        if ($currLog === null) {
            throw new TimeLogNotFoundException('Log not found', 'Log not found');
        }

        return $currLog;
    }

    /**
     * Find Logs in a given time interval
     * @param int $pageSize
     * @param int<1,max> $page
     * @param \DateTimeImmutable|null $from
     * @param \DateTimeImmutable|null $to
     * @return TimeLog[]
     */
    public function findLogs(
        int $pageSize = 100,
        int $page = 1,
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): array {
        $limit = $pageSize;
        $offset = $limit*($page-1);

        /** @var TimeLogRepository $repo */
        $repo = $this->manager->getRepository(TimeLog::class);
        return $repo->findLogsFiltered($limit, $offset, $from, $to);
    }

    /**
     * Finds the work statistics by year, month, week and day
     * @param int $pageSize
     * @param int $page
     * @param \DateTimeImmutable|null $from
     * @param \DateTimeImmutable|null $to
     * @return array[]
     * @throws \Exception
     */
    public function findLogStatistics(
        int $pageSize = 100,
        int $page = 1,
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): array {
        $years = [];
        /** @var TimeLog[] $res */
        $logs = $this->findLogs($pageSize, $page, $from, $to);
        foreach ($logs as $log) {
            // First we get the year of the current log
            $year = $log->getStart()->format('Y');

            if (!isset($years[$year])) {
                $years[$year] =  [
                    'weeks' => [],
                    'days' => [],
                    'months' => [],
                ];
            }

            $days = $years[$year]['days'];
            $months = $years[$year]['months'];
            $weeks = $years[$year]['weeks'];

            $week = 'week-' .  $log->getStart()->format('W');
            $day = $dayStart = $log->getStart()->format('Y-m-d');
            $month = $log->getStart()->format('M');

            $dayEnd = $log->getEnd()->format('Y-m-d');

            // This is the end of the interval or of the day if the interval is spread in 2 days.
            $dayEndRef = $log->getEnd();

            // Check if the given time interval is included in only one day or not.
            // If not then we need to split the interval in 2, and add the resulting periods to the corresponding days
            $endAndStartSameDay = $dayEnd === $dayStart;
            if (!$endAndStartSameDay) {
                // We set a new interval end for the first part of the interval (first day)
                $dayEndRef = new \DateTimeImmutable($day . ' 23:59');

                // We set a new interval start for the second part of the interval
                $customDayStartRef = new \DateTimeImmutable($dayEnd . ' 00:00');

                // We find the date corresponding to the second part of the interval (the next day)
                // We add up the calculated worked hours to their matching days
                // weeks and months (possibly containing hours from other logs too).
                $newWeekKey = $customDayStartRef->format('W');
                $newDayKey = $customDayStartRef->format('Y-m-d');
                $newMonthKey = $customDayStartRef->format('m');

                $newDiffInSeconds = $log->getEnd()->getTimestamp() - $customDayStartRef->getTimestamp();

                $weeks[$newWeekKey] = ($weeks[$newWeekKey] ?? 0) + $newDiffInSeconds / 3600;
                $days[$newDayKey] = ($days[$newDayKey] ?? 0) + $newDiffInSeconds / 3600;
                $months[$newMonthKey] = ($months[$newMonthKey] ?? 0) + $newDiffInSeconds / 3600;
            }

            $diffInSeconds = $dayEndRef->getTimestamp() - $log->getStart()->getTimestamp();

            // We add up the calculated worked hours to their matching days
            // weeks and months (possibly containing hours from other logs too).
            $weeks[$week] = ($weeks[$week] ?? 0) + ($diffInSeconds / 3600);
            $days[$day] = ($days[$dayStart] ?? 0) + ($diffInSeconds / 3600);
            $months[$month] = ($months[$month] ?? 0) + ($diffInSeconds / 3600);

            // We group the intervals per year, and each year has the worked hours, grouped per days, or weeks, or months
            $years[$year]['weeks'] = $weeks;
            $years[$year]['days'] = $days;
            $years[$year]['months'] = $months;
        }

        return [
            'years' => $years
        ];
    }

    public function findLogsCount(
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null
    ): int {
        /** @var TimeLogRepository $repo */
        $repo = $this->manager->getRepository(TimeLog::class);
        return $repo->findLogsCount($from, $to);
    }

    public function exportTimeLogsByInterval(
        int $pageSize = 100,
        int $page = 1,
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): string
    {
        $timeLogs = $this->findLogs($pageSize, $page, $from, $to);

        $header = ['Id', 'Start', 'End'];

        $writer = Writer::createFromString();
        $writer->setDelimiter(';');
        $writer->insertOne($header);
        $writer->setEscape('');

        foreach ($timeLogs as $timeLog) {
            $writer->insertOne([
                $timeLog->getId(),
                $timeLog->getStart()->format('Y.m.d H:i'),
                $timeLog->getEnd()->format('Y.m.d H:i'),
            ]);
        }

        return $writer->toString();
    }
}