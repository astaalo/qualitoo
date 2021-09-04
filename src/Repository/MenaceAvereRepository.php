<?php

namespace App\Repository;

use App\Entity\MenaceAvere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenaceAvere|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenaceAvere|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenaceAvere[]    findAll()
 * @method MenaceAvere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenaceAvereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenaceAvere::class);
    }

    // /**
    //  * @return MenaceAvere[] Returns an array of MenaceAvere objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MenaceAvere
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
