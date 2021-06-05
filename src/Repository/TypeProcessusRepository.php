<?php

namespace App\Repository;

use App\Entity\TypeProcessus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeProcessus|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeProcessus|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeProcessus[]    findAll()
 * @method TypeProcessus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeProcessusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeProcessus::class);
    }

    // /**
    //  * @return TypeProcessus[] Returns an array of TypeProcessus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeProcessus
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
