<?php

namespace App\Repository;

use App\Entity\TypeGrille;

/**
 * @method TypeGrille|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeGrille|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeGrille[]    findAll()
 * @method TypeGrille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeGrilleRepository extends EntityRepository
{
    /**
     * @param integer $id
     * @return array
     */
    public function findByTypeEvaluationId($id) {
        return $this->createQueryBuilder('tg')
            ->innerJoin('tg.typeEvaluation', 'te')
            ->where('te.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getResult();
    }

    /**
     * @param integer $id
     * @return \App\\Entity\TypeGrille
     */
    public function getTypeGrilleForImpact($id) {
        $data = $this->createQueryBuilder('tg')
            ->innerJoin('tg.typeEvaluation', 'te')
            ->innerJoin('tg.profilRisque', 'pr')
            ->innerJoin('pr.risque', 'r')
            ->where('te.id = :typeEvaluationId')
            ->andWhere('r.id = :risqueId')
            ->andWhere('tg.etat = :etat')
            ->setParameters(array('typeEvaluationId' => $this->_ids['type_evaluation']['impact'], 'risqueId' => $id, 'etat' => true))
            ->getQuery()->getResult();
        return $data ? $data[0] : null;
    }

}
