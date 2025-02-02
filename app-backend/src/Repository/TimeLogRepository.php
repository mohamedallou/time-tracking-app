<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TimeLog;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;

class TimeLogRepository extends EntityRepository
{
    public function findLogWithOverlappingInterval(
        DateTimeImmutable $start,
        ?DateTimeImmutable $end = null,
        ?TimeLog $exclude = null,
    ): ?TimeLog
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.id', 'desc')
            ->setMaxResults(1);

        // t.start <= $start <= t.end
        $startCondition = $qb->expr()->between(':start', 't.start', 't.end');
        $qb->andWhere($startCondition);
        $qb->setParameter('start', $start);

        if ($end !== null) {
            // t.start <= $end <= t.end
            $endCondition = $qb->expr()->between(':end', 't.start', 't.end');
            $qb->orWhere($endCondition);
            $qb->setParameter(':end', $end);
        }

        if ($exclude !== null) {
            $qb->andWhere('t.id != :exclude' );
            $qb->setParameter('exclude', $exclude->getId());
        }

        return $qb->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param DateTimeImmutable|null $from
     * @param DateTimeImmutable|null $to
     * @return TimeLog
     */
    public function findLogsFiltered(
        int $limit,
        int $offset,
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): array {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.id', 'desc')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if (isset($from)) {
            $qb->andWhere('t.start >= :fromDate');
            $qb->setParameter('fromDate', $from);
        }

        if (isset($to)) {
            $qb->andWhere('t.end <= :toDate');
            $qb->setParameter('toDate', $to);
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function findLogsCount(
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null
    ): int {
        $qb =  $this->createQueryBuilder('t');
        $qb->select('count(t.id)');
        if (isset($from)) {
            $qb->andWhere('t.start >= :fromDate');
            $qb->setParameter('fromDate', $from);
        }

        if (isset($to)) {
            $qb->andWhere('t.end <= :toDate');
            $qb->setParameter('toDate', $to);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findLastLogForUser(?User $user): ?TimeLog
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.id', 'desc')
            ->setMaxResults(1);

        if ($user !== null) {
            $qb->andWhere('t.user = :user')
                ->setParameter('user', $user);
        }

        return $qb->getQuery()
        ->getOneOrNullResult();
    }
}