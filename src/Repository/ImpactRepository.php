<?php

namespace App\Repository;

use App\Entity\Impact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Impact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Impact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Impact[]    findAll()
 * @method Impact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImpactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Impact::class);
    }

    public function listAllQueryBuilder($criteria = null) {
        $criteria = $criteria ? $criteria : new Impact();
        $queryBuilder = $this->createQueryBuilder('i')
            ->innerJoin('i.risqueOfImpact', 'roi')
            ->innerJoin('roi.risque', 'r');
        if($criteria->menace) {
            $queryBuilder->andWhere('r.menace = :menace')
                ->setParameter('menace', $criteria->menace);
        }
        if($criteria->domaine) {
            $queryBuilder->innerJoin('i.critere', 'c')
                ->andWhere('c.domaine = :domaine')
                ->setParameter('domaine', $criteria->domaine);
        }
        if($criteria->cartographie) {
            $queryBuilder
                ->andWhere('r.cartographie IN (:cartographie)')
                ->setParameter('cartographie', array($criteria->cartographie->getId()));
        }
        return $this->filterBySociete($queryBuilder, 'r');
    }

    public function getNextId() {
        $data = $this->createQueryBuilder('r')
            ->select('MAX(r.id) as maxi')
            ->getQuery ()
            ->getArrayResult ();
        return(int) $data [0] ['maxi'] + 1;
    }
}
