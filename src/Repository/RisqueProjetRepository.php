<?php

namespace App\Repository;

use App\Entity\RisqueProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RisqueProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method RisqueProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method RisqueProjet[]    findAll()
 * @method RisqueProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueProjet::class);
    }

    public function checkDoublons()
    {
        return $this->createQueryBuilder('rp')
            ->select('r.id')
            ->innerJoin('rp.risque', 'r')
            ->innerJoin('r.menace', 'm')
            ->innerJoin('rp.activite', 'a')
            ->innerJoin('rp.structure', 's')
            ->innerJoin('rp.processus', 'p')
            ->where('m.id = :menace')->setParameter('menace', 5)
            ->andWhere('s.id = :structure')->setParameter('structure', 564)
            ->andWhere('p.id = :processus')->setParameter('processus', 7)
            ->andWhere('a.id = :activite')->setParameter('activite', 15)
            ;
    }
    
    // /**
    //  * @return RisqueProjet[] Returns an array of RisqueProjet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RisqueProjet
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
