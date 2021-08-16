<?php

namespace App\Repository;

use App\Entity\RisqueMetier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RisqueMetier|null find($id, $lockMode = null, $lockVersion = null)
 * @method RisqueMetier|null findOneBy(array $criteria, array $orderBy = null)
 * @method RisqueMetier[]    findAll()
 * @method RisqueMetier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueMetierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueMetier::class);
    }

    public function checkDoublons($menace_id,$activite_id,$processus_id,$structure_id)
    {
        return $this->createQueryBuilder('rm')
            ->select('count(r.id) as nbRisk, r.id, r.etat')
            ->innerJoin('rm.risque', 'r')
            ->innerJoin('r.menace', 'm')
            ->innerJoin('rm.activite', 'a')
            ->innerJoin('rm.structure', 's')
            ->innerJoin('rm.processus', 'p')
            ->where('r.etat = :valide')->setParameter('valide', 1)
            ->orWhere('r.etat = :a_valider')->setParameter('a_valider', 2)
            ->andWhere('m.id = :menace')->setParameter('menace', $menace_id)
            ->andWhere('s.id = :structure')->setParameter('structure', $structure_id)
            ->andWhere('p.id = :processus')->setParameter('processus', $processus_id)
            ->andWhere('a.id = :activite')->setParameter('activite', $activite_id)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return RisqueMetier[] Returns an array of RisqueMetier objects
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
    public function findOneBySomeField($value): ?RisqueMetier
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
