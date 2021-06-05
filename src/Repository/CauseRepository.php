<?php

namespace App\Repository;

use App\Entity\Cause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cause|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cause|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cause[]    findAll()
 * @method Cause[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CauseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cause::class);
    }

    // /**
    //  * @return Cause[] Returns an array of Cause objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cause
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
