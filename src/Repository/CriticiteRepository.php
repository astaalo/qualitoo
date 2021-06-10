<?php

namespace App\Repository;

use App\Entity\Criticite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CriticiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Criticite::class);
    }

    /**
     * get criticite by probabilite and gravite
     * @param integer $probabilite
     * @param integer $gravite
     * @return Criticite
     */
    public function findByProbabiliteAndGravite($probabilite, $gravite) {
        if(!$probabilite || !$gravite) {
            return null;
        }
        return $this->createQueryBuilder('c')
            ->where('c.vmin <= :valeur AND c.vmax >= :valeur')
            ->setParameter('valeur', $probabilite*$gravite)
            ->getQuery()->getOneOrNullResult();
    }
}
