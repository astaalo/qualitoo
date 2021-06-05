<?php

namespace App\Repository;

use App\Entity\NotificationControle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationControle|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationControle|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationControle[]    findAll()
 * @method NotificationControle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationControleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationControle::class);
    }

    // /**
    //  * @return NotificationControle[] Returns an array of NotificationControle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NotificationControle
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
