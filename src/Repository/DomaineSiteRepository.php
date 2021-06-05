<?php

namespace App\Repository;

use App\Entity\DomaineSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DomaineSite|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomaineSite|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomaineSite[]    findAll()
 * @method DomaineSite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomaineSiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomaineSite::class);
    }

    // /**
    //  * @return DomaineSite[] Returns an array of DomaineSite objects
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
    public function findOneBySomeField($value): ?DomaineSite
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
