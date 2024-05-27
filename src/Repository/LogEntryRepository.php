<?php

namespace App\Repository;

use App\Entity\LogEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogEntry>
 */
class LogEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntry::class);
    }

    public function countLogs(array $filters): int
    {
        $qb = $this->createQueryBuilder('l');

        if (isset($filters['serviceName'])) {
            $qb->andWhere('l.serviceName IN (:serviceNames)')
                ->setParameter('serviceNames', $filters['serviceName']);
        }

        if (isset($filters['statusCode'])) {
            $qb->andWhere('l.statusCode = :statusCode')
                ->setParameter('statusCode', $filters['statusCode']);
        }

        if (isset($filters['logDate']['gte'])) {
            $qb->andWhere('l.logDate >= :startDate')
                ->setParameter('startDate', $filters['logDate']['gte']);
        }

        if (isset($filters['logDate']['lte'])) {
            $qb->andWhere('l.logDate <= :endDate')
                ->setParameter('endDate', $filters['logDate']['lte']);
        }

        return (int) $qb->select('COUNT(l.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
