<?php

namespace App\Repository;

use App\Entity\TypeTrace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeTrace|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeTrace|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeTrace[]    findAll()
 * @method TypeTrace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeTraceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeTrace::class);
    }

    // /**
    //  * @return TypeTrace[] Returns an array of TypeTrace objects
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
    public function findOneBySomeField($value): ?TypeTrace
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
