<?php

namespace App\Repository;

use App\Entity\RisqueSST;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RisqueSST|null find($id, $lockMode = null, $lockVersion = null)
 * @method RisqueSST|null findOneBy(array $criteria, array $orderBy = null)
 * @method RisqueSST[]    findAll()
 * @method RisqueSST[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueSSTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueSST::class);
    }

    // /**
    //  * @return RisqueSST[] Returns an array of RisqueSST objects
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
    public function findOneBySomeField($value): ?RisqueSST
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
