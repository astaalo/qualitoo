<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Utilisateur;

/**
 * ControleRepository
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BaseRepository extends EntityRepository
{
	/* (non-PHPdoc)
	 * @see \Doctrine\ORM\EntityRepository::findAll()
	 */
	public function findAll() {
		return $this->filterBySociete($this->createQueryBuilder('q'))->getQuery()->execute();
	}
	
	public function filterBySocieteOLD(QueryBuilder $queryBuilder, $alias = null) {
		if(!$alias) {
			$aliases = $queryBuilder->getRootAliases();
			$alias = $aliases[0];
		}
		if($this->_user->getSociete()) {
			$queryBuilder->andWhere($alias . '.societe = :societe')->setParameter('societe', $this->_user->getSociete());
		}
		return $queryBuilder;
	}

    public static function filterBySociete(QueryBuilder $queryBuilder, $alias = null, $user = null) {
        if(!$alias) {
            $aliases = $queryBuilder->getRootAliases();
            $alias = $aliases[0];
        }
        if($user->getSociete()) {
            $queryBuilder->andWhere($alias . '.societe = :societe')->setParameter('societe', $user->getSociete());
        }
        return $queryBuilder;
    }

	public function filterByProfile(QueryBuilder $queryBuilder, $alias = null, $role = null) {
		if(!$alias) {
			$aliases = $queryBuilder->getRootAliases();
			$alias = $aliases[0];
		}
		if($role && $role==Utilisateur::ROLE_SUPER_ADMIN && $this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {	
		} elseif($role && $role==Utilisateur::ROLE_ADMIN && $this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			$queryBuilder->andWhere($alias.'.id = :societeId')->setParameter('societeId', $this->_user->getSociete()->getId());
		} elseif($role && $role==Utilisateur::ROLE_RISKMANAGER && $this->_user->hasRole(Utilisateur::ROLE_RISKMANAGER)) {
			$queryBuilder->andWhere($alias.'.id = :societeId')->setParameter('societeId', $this->_user->getSociete()->getId());
		} elseif($role && $role==Utilisateur::ROLE_RESPONSABLE && $this->_user->hasRole(Utilisateur::ROLE_RESPONSABLE)) {
			$queryBuilder->andWhere($alias.'.id = :structureId')->setParameter('structureId', $this->_user->getStructure()->getId());
		} elseif($role && $role==Utilisateur::ROLE_AUDITEUR && $this->_user->hasRole(Utilisateur::ROLE_AUDITEUR)) {
			$queryBuilder->andWhere($alias.'.id = :societeId')->setParameter('societeId', $this->_user->getSociete()->getId());
		} elseif($role && $role==Utilisateur::ROLE_SUPERVISEUR && $this->_user->hasRole(Utilisateur::ROLE_SUPERVISEUR)) {
			$queryBuilder->andWhere($alias.'.id=:userId')->setParameter('userId', $this->_user->getId());
		} elseif($role && $role==Utilisateur::ROLE_PORTEUR && $this->_user->hasRole(Utilisateur::ROLE_PORTEUR)) {
			$queryBuilder->andWhere($alias.'.id=:userId')->setParameter('userId', $this->_user->getId());
		} elseif($role) {
			$queryBuilder->andWhere($alias.'.id = -1');
		}
		return $queryBuilder;
	}
}
