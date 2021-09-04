<?php

namespace App\Repository;

use App\Entity\AuditHasRisque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AuditHasRisque|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditHasRisque|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditHasRisque[]    findAll()
 * @method AuditHasRisque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditHasRisqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditHasRisque::class);
    }

    // /**
    //  * @return AuditHasRisque[] Returns an array of AuditHasRisque objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AuditHasRisque
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
