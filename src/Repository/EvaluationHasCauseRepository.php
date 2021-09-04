<?php

namespace App\Repository;

use App\Entity\EvaluationHasCause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EvaluationHasCause|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationHasCause|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationHasCause[]    findAll()
 * @method EvaluationHasCause[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationHasCauseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvaluationHasCause::class);
    }

    // /**
    //  * @return EvaluationHasCause[] Returns an array of EvaluationHasCause objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EvaluationHasCause
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
