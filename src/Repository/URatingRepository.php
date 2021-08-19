<?php

namespace App\Repository;

use App\Entity\URating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method URating|null find($id, $lockMode = null, $lockVersion = null)
 * @method URating|null findOneBy(array $criteria, array $orderBy = null)
 * @method URating[]    findAll()
 * @method URating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class URatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, URating::class);
    }

    // /**
    //  * @return URating[] Returns an array of URating objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?URating
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
