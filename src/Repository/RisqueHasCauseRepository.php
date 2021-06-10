<?php

namespace App\Repository;

use App\Entity\RisqueHasCause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RisqueHasCauseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RisqueHasCause::class);
    }

    public function findAll() {
        return $this->createQueryBuilder('rhc')->getQuery()->getResult();
    }



    public function listAllQueryBuilder($criteria = null) {
        $criteria = $criteria ? $criteria : new \App\Entity\RisqueHasCause();
        $queryBuilder = $this->createQueryBuilder('rhc')
            ->innerJoin('rhc.risque', 'r')
            ->innerJoin('rhc.cause', 'c')
        ;
        if($criteria->getRisque()){
            if($criteria->getRisque()->getMenace()) {
                $queryBuilder->andWhere('r.menace = :menace')->setParameter('menace', $criteria->getRisque()->getMenace());
            }
            if($criteria->getRisque()->getCartographie()) {
                $queryBuilder->andWhere('r.cartographie IN (:cartographie)')->setParameter('cartographie', array($criteria->getRisque()->getCartographie()->getId()));
            }
        }
        if($criteria->getCause()){
            if($criteria->getCause()->getFamille()) {
                $queryBuilder->andWhere('c.famille = :famille')->setParameter('famille', $criteria->getCause()->getFamille());
            }
        }
        return $queryBuilder;
    }
}
