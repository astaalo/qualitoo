<?php

namespace App\Repository;

use App\Entity\Maturite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Maturite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Maturite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Maturite[]    findAll()
 * @method Maturite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaturiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Maturite::class);
    }

    // /**
    //  * @return Maturite[] Returns an array of Maturite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Maturite
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
