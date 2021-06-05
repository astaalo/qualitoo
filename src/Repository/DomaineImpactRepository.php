<?php

namespace App\Repository;

use App\Entity\DomaineImpact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DomaineImpact|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomaineImpact|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomaineImpact[]    findAll()
 * @method DomaineImpact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomaineImpactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomaineImpact::class);
    }

    // /**
    //  * @return DomaineImpact[] Returns an array of DomaineImpact objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DomaineImpact
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
