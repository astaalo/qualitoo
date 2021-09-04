<?php

namespace App\Repository;

use App\Entity\RisqueHasImpact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RisqueHasImpact|null find($id, $lockMode = null, $lockVersion = null)
 * @method RisqueHasImpact|null findOneBy(array $criteria, array $orderBy = null)
 * @method RisqueHasImpact[]    findAll()
 * @method RisqueHasImpact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueHasImpactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueHasImpact::class);
    }

    public function listAllQueryBuilder($criteria = null) {
        $criteria = $criteria ? $criteria : new RisqueHasImpact();
        $queryBuilder = $this->createQueryBuilder('rhi')
            ->innerJoin('rhi.risque', 'r')
            ->innerJoin('rhi.impact', 'i')
        ;
        if($criteria->getId()) {
            if ($criteria->getRisque()) {
                if ($criteria->getRisque()->getMenace()) {
                    $queryBuilder->andWhere('r.menace = :menace')->setParameter('menace', $criteria->getRisque()->getMenace());
                }
                if ($criteria->getRisque()->getCartographie()) {
                    $queryBuilder->andWhere('r.cartographie IN (:cartographie)')->setParameter('cartographie', array($criteria->getRisque()->getCartographie()->getId()));
                }
            }
            if ($criteria->getImpact()) {
                if ($criteria->getImpact()->getCritere()) {
                    $queryBuilder->andWhere('i.critere = :critere')->setParameter('critere', $criteria->getImpact()->getCritere());
                }
            }
        }
        return $queryBuilder;
    }
}
