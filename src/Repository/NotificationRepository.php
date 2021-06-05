<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
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
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param \App\Entity\Utilisateur
     * @return integer
     */
    public function getCount(Utilisateur $user) {
        $data = $this->createQueryBuilder('n')
            ->select('COUNT(n.id) as _count')
            ->innerJoin('n.receivers', 'r')
            ->where('r.id = :user')
            ->andWhere('n.read = :isRead')
            ->setParameters(array(
                'user' => $user->getId(),
                'isRead' => false
            ))
            ->getQuery()->getOneOrNullResult();
        return $data['_count'];
    }

    /**
     * @param \App\Entity\Utilisateur
     * @return Collection
     */
    public function getUnreadNotifications(Utilisateur $user, $limit = 5) {
        $data = $this->createQueryBuilder('n')
            ->innerJoin('n.receivers', 'r')
            ->where('r.id = :user')
            ->andWhere('n.read = :isRead')
            ->setMaxResults($limit)
            ->orderBy('n.dateModification', 'DESC')
            ->setParameters(array(
                'user' => $user->getId(),
                'isRead' => false
            ))
            ->getQuery()->getResult();
        return $data;
    }

    /**
     * @return QueryBuilder
     */
    public function getUnreadNotif() {
        $data = $this->createQueryBuilder('n')
            ->innerJoin('n.receivers', 'r')
            ->where('r.id = :user')
            ->andWhere('n.read = :isRead')
            ->orderBy('n.dateModification', 'DESC')
            ->setParameters(array(
                'user' => $this->_user->getId(),
                'isRead' => false
            ));
        return $data;
    }
}
