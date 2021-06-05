<?php

namespace App\Repository;

use App\Entity\TableauBord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TableauBord|null find($id, $lockMode = null, $lockVersion = null)
 * @method TableauBord|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableauBord[]    findAll()
 * @method TableauBord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableauBordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableauBord::class);
    }

    // /**
    //  * @return TableauBord[] Returns an array of TableauBord objects
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
    public function findOneBySomeField($value): ?TableauBord
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
