<?php

namespace App\Repository;

use App\Entity\Menace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Menace|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menace|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menace[]    findAll()
 * @method Menace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menace::class);
    }

    // /**
    //  * @return Menace[] Returns an array of Menace objects
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
    public function findOneBySomeField($value): ?Menace
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
