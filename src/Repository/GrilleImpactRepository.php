<?php

namespace App\Repository;

use App\Entity\GrilleImpact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GrilleImpact|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrilleImpact|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrilleImpact[]    findAll()
 * @method GrilleImpact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrilleImpactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrilleImpact::class);
    }

    // /**
    //  * @return GrilleImpact[] Returns an array of GrilleImpact objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GrilleImpact
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
