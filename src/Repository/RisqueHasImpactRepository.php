<?php

namespace App\Repository;

use App\Entity\RisqueHasImpact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RisqueHasImpact|null find($id, $lockMode = null, $lockVersion = null)
 * @method RisqueHasImpact|null findOneBy(array $criteria, array $orderBy = null)
 * @method RisqueHasImpact[]    findAll()
 * @method RisqueHasImpact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueHasImpactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueHasImpact::class);
    }

    // /**
    //  * @return RisqueHasImpact[] Returns an array of RisqueHasImpact objects
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
    public function findOneBySomeField($value): ?RisqueHasImpact
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
