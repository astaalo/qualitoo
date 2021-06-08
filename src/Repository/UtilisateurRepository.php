<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class UtilisateurRepository extends BaseRepository
{
    /**
     * @param \App\Entity\Utilisateur $user
     * @return QueryBuilder
     */
    public function listAllQueryBuilder($user = null) {
        $querBuilder = $this->createQueryBuilder('q')
            ->leftJoin('q.societeOfRiskManager', 'r')
            ->leftJoin('q.societeOfAdministrator', 'd')
            ->leftJoin('q.societeOfAuditor', 'u')
            ->leftJoin('q.structure', 'e')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime']);

        return $this->filterBySociete($querBuilder, 'e')->groupBy('q.id');
    }

    /* (non-PHPdoc)
     * @see \Doctrine\ORM\EntityRepository::findAll()
     */
    public function findAll() {
        $queryBuilder = $this->createQueryBuilder('q')
            ->innerJoin('q.structure', 's');
        return $this->filterBySociete($queryBuilder, 's')->getQuery()->execute();
    }

    public function filter() {

    }

    public function getAllSocietes() {
        $data = new \Doctrine\Common\Collections\ArrayCollection();
        $ids = array();
        $data->add($this->structure->getSociete());
        $ids[] = $this->structure->getSociete()->getId();
        foreach($this->societeOfAdministrator as $societe) {
            if(in_array($societe->getId(), $ids)==false) {
                array_push($ids, $societe->getId());
                $data->add($societe);
            }
        }
        foreach($this->societeOfRiskManager as $societe) {
            if(in_array($societe->getId(), $ids)==false) {
                array_push($ids, $societe->getId());
                $data->add($societe);
            }
        }
        foreach($this->societeOfAuditor as $societe) {
            if(in_array($societe->getId(), $ids)==false) {
                array_push($ids, $societe->getId());
                $data->add($societe);
            }
        }
        return $data;
    }

}
