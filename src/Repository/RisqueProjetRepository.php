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

    public function checkDoublons($menace_id,$processus_id,$structure_id,$projet_id)
    {
        return $this->createQueryBuilder('rm')
            ->select('count(r.id) as nbRisk, r.id, r.etat')
            ->innerJoin('rm.risque', 'r')
            ->innerJoin('r.menace', 'm')
            ->innerJoin('rm.projet', 'pr')
            ->innerJoin('rm.structure', 's')
            ->innerJoin('rm.processus', 'p')
            ->where('r.etat >= :etat')->setParameter('etat', 0)
            ->andWhere('m.id = :menace')->setParameter('menace', $menace_id)
            ->andWhere('s.id = :structure')->setParameter('structure', $structure_id)
            ->andWhere('p.id = :processus')->setParameter('processus', $processus_id)
            ->andWhere('pr.id = :projet')->setParameter('projet', $projet_id)
            ->getQuery()
            ->getResult()
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
