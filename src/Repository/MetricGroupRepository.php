<?php

namespace App\Repository;

use App\Entity\MetricGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MetricGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetricGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetricGroup[]    findAll()
 * @method MetricGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetricGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetricGroup::class);
    }

    // /**
    //  * @return MetricGroup[] Returns an array of MetricGroup objects
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
    public function findOneBySomeField($value): ?MetricGroup
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
