<?php

namespace App\Repository;

use App\Entity\RisqueProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RisqueProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method RisqueProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method RisqueProjet[]    findAll()
 * @method RisqueProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueProjet::class);
    }

    // /**
    //  * @return RisqueProjet[] Returns an array of RisqueProjet objects
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
    public function findOneBySomeField($value): ?RisqueProjet
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