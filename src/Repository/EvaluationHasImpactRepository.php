<?php

namespace App\Repository;

use App\Entity\EvaluationHasImpact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EvaluationHasImpact|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationHasImpact|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationHasImpact[]    findAll()
 * @method EvaluationHasImpact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationHasImpactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvaluationHasImpact::class);
    }

    // /**
    //  * @return EvaluationHasImpact[] Returns an array of EvaluationHasImpact objects
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
    public function findOneBySomeField($value): ?EvaluationHasImpact
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
