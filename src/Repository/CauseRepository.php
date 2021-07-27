<?php

namespace App\Repository;

use App\Entity\Cause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cause|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cause|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cause[]    findAll()
 * @method Cause[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CauseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cause::class);
    }

    /**
     * @param integer $risqueId
     * @return array
     */
    public function getByRisqueId($risqueId) {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.libelle')
            ->innerJoin('c.risqueOfCause', 'roc')
            ->innerJoin('roc.risque', 'r')
            ->where('r.id = :risqueId')
            ->setParameter('risqueId', $risqueId)
            ->getQuery()->getArrayResult();
    }

    /* (non-PHPdoc)
     * @see \Orange\QuickMakingBundle\Repository\EntityRepository::listAllQueryBuilder()
     */
    public function listAllQueryBuilder($criteria = null) {
        $criteria = $criteria ? $criteria : new Cause();
        $queryBuilder = $this->createQueryBuilder('c')
            ->innerJoin('c.risqueHasCause', 'rhc')
            ->innerJoin('rhc.risque', 'r');
        if($criteria->menace) {
            $queryBuilder->andWhere('r.menace = :menace')->setParameter('menace', $criteria->menace);
        }
        if($criteria->getFamille()) {
            $queryBuilder->andWhere('c.famille = :famille')->setParameter('famille', $criteria->getFamille());
        }
        if($criteria->getCartographie()) {
            $queryBuilder->andWhere('r.cartographie IN (:cartographie)')->setParameter('cartographie', array($criteria->getCartographie()->getId()));
        }
        return $queryBuilder;
    }
}
