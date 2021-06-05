<?php

namespace App\Repository;

use App\Entity\CritereChargement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CritereChargement|null find($id, $lockMode = null, $lockVersion = null)
 * @method CritereChargement|null findOneBy(array $criteria, array $orderBy = null)
 * @method CritereChargement[]    findAll()
 * @method CritereChargement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CritereChargementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CritereChargement::class);
    }

    // /**
    //  * @return CritereChargement[] Returns an array of CritereChargement objects
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
    public function findOneBySomeField($value): ?CritereChargement
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
