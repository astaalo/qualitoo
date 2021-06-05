<?php

namespace App\Repository;

use App\Entity\DomaineActivite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DomaineActivite|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomaineActivite|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomaineActivite[]    findAll()
 * @method DomaineActivite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomaineActiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomaineActivite::class);
    }

    // /**
    //  * @return DomaineActivite[] Returns an array of DomaineActivite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DomaineActivite
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
