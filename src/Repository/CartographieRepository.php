<?php

namespace App\Repository;

use App\Entity\Cartographie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cartographie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cartographie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cartographie[]    findAll()
 * @method Cartographie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartographieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cartographie::class);
    }

    // /**
    //  * @return Cartographie[] Returns an array of Cartographie objects
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
    public function findOneBySomeField($value): ?Cartographie
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
