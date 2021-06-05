<?php

namespace App\Repository;

use App\Entity\EtatAvancement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EtatAvancement|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatAvancement|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatAvancement[]    findAll()
 * @method EtatAvancement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatAvancementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatAvancement::class);
    }

    // /**
    //  * @return EtatAvancement[] Returns an array of EtatAvancement objects
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
    public function findOneBySomeField($value): ?EtatAvancement
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
