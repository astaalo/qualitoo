<?php

namespace App\Repository;

use App\Entity\PlanAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanAction[]    findAll()
 * @method PlanAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanAction::class);
    }

    // /**
    //  * @return PlanAction[] Returns an array of PlanAction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlanAction
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
