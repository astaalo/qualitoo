<?php

namespace App\Repository;

use App\Entity\Controle;
use App\Entity\PlanAction;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method PlanAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanAction[]    findAll()
 * @method PlanAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanActionRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, PlanAction::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    /**
     * @param integer $risque_id
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listByRisqueQueryBuilder($risque_id) {
        return $this->createQueryBuilder('pa')
            ->leftJoin('pa.controle', 'c')
            ->leftJoin('pa.risque', 'r1')
            ->leftJoin('c.risque', 'r2')
            ->where('r1.id = :risque_id')
            ->orWhere('r2.id = :risque_id')
            ->setParameter('risque_id', $risque_id);
    }

    /**
     * @param \App\Entity\Risque $risque
     * @return integer
     */
    public function getLastNumero($risque) {
        $data = $this->createQueryBuilder('r')
            ->select('MAX(r.numero) as number')
            ->innerJoin('r.causeOfRisque', 'c')
            ->where('c.risque = :risque')
            ->setParameter('risque', $risque)
            ->getQuery()->getOneOrNullResult();
        return $data['number'];
    }

    /* (non-PHPdoc)
     * @see \Orange\QuickMakingBundle\Repository\EntityRepository::listAllQueryBuilder()
     */
    public function listAllQueryBuilder($criteria = null) {
        $criteria = $criteria ? $criteria : new \App\Entity\PlanAction();
        $queryBuilder = $this->createQueryBuilder('q')
            ->select('partial q.{ id, libelle, dateFin }, partial cor.{ id }, partial r.{ id }, partial m.{ id, libelle }')
            ->addSelect('partial por.{ id, prenom, nom }, partial st.{ id, libelle }')
            ->innerJoin('q.causeOfRisque', 'cor')
            ->leftJoin('q.statut', 'st')
            ->innerJoin('cor.risque', 'r')
            ->innerJoin('r.menace', 'm')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('r.utilisateur', 'u')
            ->innerJoin('r.societe', 's')
            ->leftJoin('q.porteur', 'por')
            ->leftJoin('q.superviseur', 'sup');

        if($criteria->menace) {
            $queryBuilder->andWhere('r.menace = :menace')->setParameter('menace', $criteria->menace);
        }
        if($criteria->getPorteur()) {
            $queryBuilder->andWhere('q.porteur = :porteur')->setParameter('porteur', $criteria->getPorteur());
        }
        if($criteria->getStatut()) {
            $queryBuilder->andWhere('q.statut = :statut')->setParameter('statut', $criteria->getStatut());
        }
        if($criteria->cartographie) {
            $queryBuilder->andWhere('r.cartographie = :cartographie')->setParameter('cartographie', $criteria->cartographie);
        }
        if($criteria->dateDebutFrom) {
            $queryBuilder->andWhere('q.dateDebut >= :debut')->setParameter('debut', $criteria->dateDebutFrom);
        }
        if($criteria->dateDebutTo) {
            $queryBuilder->andWhere('q.dateDebut <= :fin')->setParameter('fin', $criteria->dateDebutTo);
        }
        if($criteria->dateFinFrom) {
            $queryBuilder->andWhere('q.dateFin >= :debutF')->setParameter('debutF', $criteria->dateFinFrom);
        }
        if($criteria->dateFinTo) {
            $queryBuilder->andWhere('q.dateFin <= :finF')->setParameter('finF', $criteria->dateFinTo);
        }
        if($criteria->projet) {
            $queryBuilder->andWhere('rp.projet = :projet')->setParameter('projet', $criteria->projet);
        }
        if($criteria->site) {
            $queryBuilder->andWhere('re.site = :site OR rs.site = :site')->setParameter('site', $criteria->site);
        }
        if(null != $structure = $criteria->structure) {
            $queryBuilder->andWhere('IDENTITY(rm.structure) IN (:structureIds) OR IDENTITY(rp.structure) IN (:structureIds)')
                ->setParameter('structureIds', $structure->getChildrenIds());
        }
        $this->applyFilterByProfile($queryBuilder);
        return $queryBuilder->groupBy('q.id');
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     */
    public function applyFilterByProfile($queryBuilder) {
        if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
            $queryBuilder = BaseRepository::filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN);
        } elseif($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
            $queryBuilder=BaseRepository::filterByProfile($queryBuilder, 's', Utilisateur::ROLE_ADMIN);
        } elseif($this->_user->hasRole(Utilisateur::ROLE_RISKMANAGER)) {
            $queryBuilder = BaseRepository::filterByProfile($queryBuilder, 's', Utilisateur::ROLE_RISKMANAGER);
        } elseif($this->_user->hasRole(Utilisateur::ROLE_AUDITEUR)) {
            $queryBuilder = BaseRepository::filterByProfile($queryBuilder, 's', Utilisateur::ROLE_AUDITEUR);
        } elseif($this->_user->hasRole(Utilisateur::ROLE_RESPONSABLE)) {
            if((null!=$structure=$this->_user->getStructure()) && $this->_user->getManager()==true) {
                $queryBuilder->orWhere('IDENTITY(rm.structure) IN (:structureIds) OR IDENTITY(rp.structure) IN (:structureIds)')->setParameter('structureIds', $structure->getChildrenIds());
            }
            if($this->_user->getSite()->count()!=0) {
                $queryBuilder->orWhere('IDENTITY(rs.structure) in (:siteIds) or IDENTITY(re.structure) in (:siteIds)')->setParameter('siteIds', $this->_user->getSiteIds());
            }
        } elseif($this->_user->hasRole(Utilisateur::ROLE_PORTEUR)) {
            $queryBuilder->orWhere('IDENTITY(q.porteur)=:userId')->setParameter('userId', $this->_user->getId());
        } elseif($this->_user->hasRole(Utilisateur::ROLE_SUPERVISEUR)) {
            $queryBuilder->orWhere('IDENTITY(q.superviseur)=:userId')->setParameter('userId', $this->_user->getId());
        }  else {
            $queryBuilder->orWhere('q.id = -1');
        }
    }
}
