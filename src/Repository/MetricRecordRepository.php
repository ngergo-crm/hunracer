<?php

namespace App\Repository;

use App\Entity\MetricRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MetricRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetricRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetricRecord[]    findAll()
 * @method MetricRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetricRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetricRecord::class);
    }

    public function getAthleteDatasheetDates($user)
    {
        $q = $this->createQueryBuilder('w');
        $q->select("w.metricCreatedAt")
            ->distinct()
            ->join('w.user', 'u')
            ->andWhere('u.uuid = :athlete')
            ->setParameter('athlete', $user)
            ->orderBy('w.metricCreatedAt', 'desc')
        ;
        return $q->getQuery()->getArrayResult();
    }

    // /**
    //  * @return MetricRecord[] Returns an array of MetricRecord objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MetricRecord
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
