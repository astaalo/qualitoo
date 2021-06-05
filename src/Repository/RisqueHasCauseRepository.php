<?php

namespace App\Repository;

use App\Entity\RisqueHasCause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RisqueHasCause|null find($id, $lockMode = null, $lockVersion = null)
 * @method RisqueHasCause|null findOneBy(array $criteria, array $orderBy = null)
 * @method RisqueHasCause[]    findAll()
 * @method RisqueHasCause[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueHasCauseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueHasCause::class);
    }

    // /**
    //  * @return RisqueHasCause[] Returns an array of RisqueHasCause objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RisqueHasCause
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
