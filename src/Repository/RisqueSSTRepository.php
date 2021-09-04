<?php

namespace App\Repository;

use App\Entity\RisqueSST;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RisqueSST|null find($id, $lockMode = null, $lockVersion = null)
 * @method RisqueSST|null findOneBy(array $criteria, array $orderBy = null)
 * @method RisqueSST[]    findAll()
 * @method RisqueSST[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueSSTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueSST::class);
    }

    public function checkDoublons($menace_id,$site_id,$domaine_activite_id,$equipement_id,$lieu_id,$manifestation_id)
    {
        return $this->createQueryBuilder('rsst')
            ->select('count(r.id) as nbRisk, r.id, r.etat')
            ->innerJoin('rsst.risque', 'r')
            ->innerJoin('r.menace', 'm')
            ->innerJoin('rsst.site', 'si')
            ->innerJoin('rsst.domaineActivite', 'da')
            ->innerJoin('rsst.equipement', 'e')
            ->innerJoin('rsst.lieu', 'l')
            ->innerJoin('rsst.manifestation', 'ma')
            ->where('r.etat >= :etat')->setParameter('etat', 0)
            ->andWhere('m.id = :menace')->setParameter('menace', $menace_id)
            ->andWhere('si.id = :site')->setParameter('site', $site_id)
            ->andWhere('da.id = :domaine')->setParameter('domaine', $domaine_activite_id)
            ->andWhere('e.id = :equipement')->setParameter('equipement', $equipement_id)
            ->andWhere('l.id = :lieu')->setParameter('lieu', $lieu_id)
            ->andWhere('ma.id = :manifestation')->setParameter('manifestation', $manifestation_id)
            ->getQuery()
            ->getResult()
            ;
    }
}
