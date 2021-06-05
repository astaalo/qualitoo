<?php

namespace App\Repository;

use App\Entity\RisqueMetier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RisqueMetier|null find($id, $lockMode = null, $lockVersion = null)
 * @method RisqueMetier|null findOneBy(array $criteria, array $orderBy = null)
 * @method RisqueMetier[]    findAll()
 * @method RisqueMetier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueMetierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueMetier::class);
    }

    // /**
    //  * @return RisqueMetier[] Returns an array of RisqueMetier objects
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
    public function findOneBySomeField($value): ?RisqueMetier
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
