<?php

namespace App\Repository;

use App\Entity\MetricType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MetricType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetricType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetricType[]    findAll()
 * @method MetricType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetricTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetricType::class);
    }

    // /**
    //  * @return MetricType[] Returns an array of MetricType objects
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
    public function findOneBySomeField($value): ?MetricType
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
