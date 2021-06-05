<?php

namespace App\Repository;

use App\Entity\TypeColonne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeColonne|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeColonne|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeColonne[]    findAll()
 * @method TypeColonne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeColonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeColonne::class);
    }

    // /**
    //  * @return TypeColonne[] Returns an array of TypeColonne objects
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
    public function findOneBySomeField($value): ?TypeColonne
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
