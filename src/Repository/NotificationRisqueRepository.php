<?php

namespace App\Repository;

use App\Entity\NotificationRisque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationRisque|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationRisque|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationRisque[]    findAll()
 * @method NotificationRisque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRisqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationRisque::class);
    }

    // /**
    //  * @return NotificationRisque[] Returns an array of NotificationRisque objects
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
    public function findOneBySomeField($value): ?NotificationRisque
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
