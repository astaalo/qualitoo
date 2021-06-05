<?php

namespace App\Repository;

use App\Entity\MethodeControle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MethodeControle|null find($id, $lockMode = null, $lockVersion = null)
 * @method MethodeControle|null findOneBy(array $criteria, array $orderBy = null)
 * @method MethodeControle[]    findAll()
 * @method MethodeControle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MethodeControleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MethodeControle::class);
    }

    // /**
    //  * @return MethodeControle[] Returns an array of MethodeControle objects
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
    public function findOneBySomeField($value): ?MethodeControle
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
