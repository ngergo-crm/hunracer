<?php

namespace App\Repository;

use App\Entity\WorkoutDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkoutDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkoutDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkoutDetail[]    findAll()
 * @method WorkoutDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkoutDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkoutDetail::class);
    }

    // /**
    //  * @return WorkoutDetail[] Returns an array of WorkoutDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorkoutDetail
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
