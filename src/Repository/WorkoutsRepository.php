<?php

namespace App\Repository;

use App\Entity\Workouts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Workouts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workouts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Workouts[]    findAll()
 * @method Workouts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkoutsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workouts::class);
    }

    public function getWorkoutDays($user, array $timeRange, $onlyWorkoutDays = true)
    {
        if(isset($timeRange['before'])) {
            $before = $timeRange['before'];
        } else if (isset($timeRange['start'])) {
            $before = $timeRange['start'];
        }
        if(isset($timeRange['after'])) {
            $after = $timeRange['after'];
        }else if (isset($timeRange['end'])) {
            $after = $timeRange['end'];
        }

        $q = $this->createQueryBuilder('w');
        if($onlyWorkoutDays) {
            $q->select('w.workoutDay')->distinct();
        }
        $q->andWhere('w.user = :user')
            ->andWhere('w.workoutDay BETWEEN :after AND :before')
            ->setParameter('user', $user)
            ->setParameter('after', $after)
            ->setParameter('before', $before)
            ->orderBy('w.workoutDay', 'ASC');
        return $q->getQuery()->getResult();
    }

    public function getLastInsertedWorkoutTime($user)
    {
        return $this->createQueryBuilder('w')
            ->select('max(w.updatedAt)')->distinct()
            ->andWhere('w.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
