<?php

namespace App\Repository;

use App\Controller\BaseController;
use App\Entity\Activite;
use App\Entity\Risque;
use App\Entity\RisqueEnvironnemental;
use App\Entity\RisqueMetier;
use App\Entity\RisqueProjet;
use App\Entity\RisqueSST;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Risque|null find($id, $lockMode = null, $lockVersion = null)
 * @method Risque|null findOneBy(array $criteria, array $orderBy = null)
 * @method Risque[]    findAll()
 * @method Risque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisqueRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Risque::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    /**
     * @param Risque $criteria
     * @return QueryBuilder
     */
    public function getAllRisquesByUser($criteria=null) {
        $criteria = $criteria ? $criteria : new Risque();
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('r.menace', 'm')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('rm.structure', 'stM')
            ->leftJoin('rp.structure', 'stP')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->innerJoin('r.cartographie', 'cg')
            ->innerJoin('r.societe', 's')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->orderby('r.dateSaisie', 'DESC');
        $this->applyFilterByProfile($queryBuilder, $criteria);
        return $queryBuilder;
    }

    /**
     * Pour un risque donnée renvoie les risques en communs
     * @param Risque $risque
     */
    public function getDoublons($risque) {
        $queryBuilder= $this->createQueryBuilder('r')
            ->innerJoin('r.menace', 'm')
            ->andWhere('r.menace=:menace')->setParameter('menace',$risque->getMenace());
        if($risque->getRisqueMetier()) {
            $queryBuilder->innerJoin('r.risqueMetier', 'rm')
                ->leftJoin('rm.structure', 's')
                ->leftJoin('rm.processus', 'p')
                ->leftJoin('rm.activite', 'a')
                ->andWhere('p=:processus')->setParameter('processus', $risque->getRisqueMetier()->getProcessus())
                ->andWhere('s=:structure')->setParameter('structure', $risque->getRisqueMetier()->getStructure())
                ->andWhere('a=:activite')->setParameter('activite', $risque->getRisqueMetier()->getActivite());
        }
        return $queryBuilder;
    }

    /**
     * @param Risque $entity
     */
    public function isFirstEntity($entity){
        $queryBuilder= $this->createQueryBuilder('r')
            ->innerJoin('r.menace', 'm')
            ->andWhere('r.menace=:menace')->setParameter('menace',$entity->getMenace())
            ->andWhere('r.first=:first')->setParameter('first', true)
            ->andWhere('r.etat = :etat')
            ->setParameter('etat', $this->_states['risque']['valide']);
        if($entity->getRisqueMetier()) {
            $queryBuilder->innerJoin('r.risqueMetier', 'rm')
                ->leftJoin('rm.structure', 's')
                ->leftJoin('rm.processus', 'p')
                ->leftJoin('rm.activite', 'a')
                ->andWhere('p=:processus')->setParameter('processus', $entity->getRisqueMetier()->getProcessus())
                ->andWhere('s=:structure')->setParameter('structure', $entity->getRisqueMetier()->getStructure())
                ->andWhere('a=:activite')->setParameter('activite', $entity->getRisqueMetier()->getActivite());
        }elseif($entity->getRisqueProjet()) {
            $queryBuilder->innerJoin('r.risqueProjet', 'rp')
                ->leftJoin('rp.structure', 's')
                ->leftJoin('rp.projet', 'p')
                ->andWhere('s=:structure')->setParameter('structure', $entity->getRisqueProjet()->getStructure())
                ->andWhere('p=:projet')->setParameter('projet', $entity->getRisqueProjet()->getProjet());
        }elseif($entity->getRisqueEnvironnemental()) {
            $queryBuilder->innerJoin('r.risqueEnvironnemental', 're')
                ->leftJoin('re.site', 's')
                ->andWhere('s=:site')->setParameter('site', $entity->getRisqueEnvironnemental()->getSite());
            if($entity->getRisqueEnvironnemental()->getEquipement())
                $queryBuilder->leftJoin('re.equipement', 'e')
                    ->andWhere('e=:equipement')->setParameter('equipement', $entity->getRisqueEnvironnemental()->getEquipement());
        }elseif($entity->getRisqueSST()){
            $queryBuilder->innerJoin('r.risqueSST', 'rs')
                ->leftJoin('rs.site', 's')
                ->andWhere('s=:site')->setParameter('site', $entity->getRisqueSST()->getSite());
            if($entity->getRisqueSST()->getEquipement())
                $queryBuilder->leftJoin('rs.equipement', 'e')
                    ->andWhere('e=:equipement')->setParameter('equipement', $entity->getRisqueSST()->getEquipement());
        }
        return $queryBuilder->orderBy('r.id','DESC')->setMaxResults(1);
    }

    /* (non-PHPdoc)
     * @see \Doctrine\ORM\EntityRepository::findAll()
     */
    /*public function findAll() {
        // TODO: Auto-generated method stub
        $queryBuilder = $this->createQueryBuilder('r')
            ->innerJoin('r.activite', 'a')
            ->innerJoin('a.processus', 'p')
            ->innerJoin('p.structure', 's');
        return $this->filterBySociete($queryBuilder, 's')->getQuery()->execute();
    }*/

    /**
     * @param Risque $criteria
     * @return QueryBuilder
     */
    public function filterBuilder($queryBuilder, $criteria) {
        $criteria = $criteria ? $criteria : new Risque();
        $queryBuilder->innerJoin('r.causeOfRisque', 'cor')
            ->leftJoin('cor.cause', 'cs')
            ->leftJoin('cor.planAction','pas')
            ->leftJoin('cor.controle','ctrl');
        if(empty($criteria->motCle)==false) {
            $query  = 'm.libelle LIKE :motCle OR cs.libelle LIKE :motCle OR ';
// 			$query .= 'dirM.code LIKE :motCle OR dirP.code LIKE :motCle OR ';
// 			$query .= 'stM.code LIKE :motCle OR stP.code LIKE :motCle OR ';
// 			$query .= 'a.libelle LIKE :motCle OR p.libelle LIKE :motCle OR ';
// 			$query .= 'sitE.libelle LIKE :motCle OR sitS.libelle LIKE :motCle OR ';
            $query .= 'pas.libelle LIKE :motCle OR ';
            $query .= 'pm.libelle LIKE :motCle OR pp.libelle LIKE :motCle OR ';
            $query .= 'ctrl.description LIKE :motCle OR pas.description LIKE :motCle';
            $queryBuilder->andWhere($query)->setParameter('motCle', '%'.$criteria->motCle.'%');
        }
        if($criteria->getMenace()) {
            $queryBuilder->andWhere('r.menace = :menace')->setParameter('menace', $criteria->getMenace());
        }
        if($criteria->getCartographie()) {
            $queryBuilder->andWhere('cg.id = :cartographieId')->setParameter('cartographieId', $criteria->getCartographie()->getId());
        }
        $data = $criteria->getRisqueData();
        if($data->getId()==null) {
        } elseif($criteria->isPhysical()) {
            if($data->getSite()) {
                $queryBuilder->andWhere('rs.site = :site OR re.site = :site')->setParameter('site', $data->getSite());
            }
            if($data->getDomaineActivite()) {
                $queryBuilder->andWhere('rs.domaineActivite = :site OR re.domaineActivite = :site')->setParameter('domaineActivite', $data->getDomaineActivite());
            }
            if($data->getEquipement()) {
                $queryBuilder->andWhere('rs.equipement = :equipement OR re.equipement = :equipement')->setParameter('equipement', $data->getEquipement());
            }
        } else {
            if($data->getDirection() || $data->getStructure()) {
                $structure = $data->getStructure() ? $data->getStructure() : ($data->getDirection() ? $data->getDirection() : null);
                $queryBuilder->andWhere('IDENTITY(rm.structure) IN (:structureIds) OR IDENTITY(rp.structure) IN (:structureIds)')
                    ->setParameter('structureIds', $structure->getChildrenIds());
            }
            if(null!=$processus = $data->getProcessus()) {
                $queryBuilder->andWhere('pm.id IN (:processusIds) OR pp.id IN (:processusIds)')->setParameter('processusIds', $processus->getChildrenIds());
            }
            if($criteria->getCartographie()->getId()==Risque::$carto['metier'] && $data->getActivite()) {
                $queryBuilder->andWhere('rm.activite = :activite')->setParameter('activite', $data->getActivite());
            } elseif($criteria->getCartographie()->getId()==Risque::$carto['projet'] && $data->getProjet()) {
                $queryBuilder->andWhere('rp.projet = :projet')->setParameter('projet', $data->getProjet());
            }
        }
        if($criteria->dateEvaluation) {
            if($criteria->dateEvaluation['dateDebut']) {
                $queryBuilder->andWhere('e.dateEvaluation >= :dateDebutEvaluation')->setParameter('dateDebutEvaluation', $criteria->dateEvaluation['dateDebut']);
            }
            if($criteria->dateEvaluation['dateFin']) {
                $queryBuilder->andWhere('e.dateEvaluation <= :dateFinEvaluation')->setParameter('dateFinEvaluation', $criteria->dateEvaluation['dateFin']);
            }
        }
        if($criteria->cause) {
            $queryBuilder->andWhere('cor.cause = :cause')->setParameter('cause', $criteria->cause);
        }
        if($criteria->getCriticite() && $criteria->getCriticite()->count()) {
            $queryBuilder->andWhere('r.criticite IN (:criticite)')->setParameter('criticite', $criteria->getCriticite());
        }
        if($criteria->getProbabilite() && count($criteria->getProbabilite())) {
            $queryBuilder->andWhere('r.probabilite IN (:probabilite)')->setParameter('probabilite', array_values($criteria->getProbabilite()));
        }
        if($criteria->getGravite() && count($criteria->getGravite())) {
            $queryBuilder->andWhere('r.gravite IN (:gravite)')->setParameter('gravite', array_values($criteria->getGravite()));
        }
        if(empty($criteria->hasPlanAction)==false) {
            if($criteria->hasPlanAction==true) {
                $queryBuilder->andWhere('pas.id is not null');
                if($criteria->statutPlanAction) {
                    $queryBuilder->andWhere('pas.statut = :statutPA')->setParameter('statutPA', $criteria->statutPlanAction);
                }
            } elseif($criteria->hasPlanAction==false) {
                $queryBuilder->andWhere('pas.id is null');
            }
        }
        if(empty($criteria->hasControle)==false) {
            if($criteria->hasControle==true) {
                $queryBuilder->andWhere('ctrl.id is not null');
            } elseif($criteria->hasControle==false) {
                $queryBuilder->andWhere('ctrl.id is null');
            }
        }
        return $queryBuilder;
    }

    /**
     * @param Risque $criteria
     * @return QueryBuilder
     */
    public function listValidQueryBuilder($criteria=null) {
        $criteria = $criteria ? $criteria : new Risque();
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->select('partial r.{id, code, probabilite, gravite}, partial m.{ id, libelle }, partial c.{ id, niveau }, partial cg.{ id }')
            ->addSelect('partial rs.{ id, site }, partial re.{ id, site }, partial a.{ id, libelle },
			 			partial rm.{ id, structure }, partial rp.{ id, structure }, partial pm.{ id, libelle }, partial pp.{ id, libelle }')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('rm.processus', 'pm')
            ->leftJoin('rp.processus', 'pp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('r.menace', 'm')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('r.cartographie', 'cg')
            ->leftJoin('r.societe', 's')
            ->orderby('r.dateSaisie', 'DESC');
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide']);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        return $queryBuilder->addGroupBy('r.id');
    }

    /**
     * @param Risque $criteria
     * @return QueryBuilder
     */
    public function listAverableQueryBuilder($criteria) {
        return $this->listValidQueryBuilder($criteria)
            ->andWhere('r.avered=:avered')->setParameter('avered', false);
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param Risque $criteria
     */
    public function applyFilterByProfile($queryBuilder, $criteria)
    {
        if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
        } elseif($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
            $queryBuilder = BaseRepository::filterByProfile($queryBuilder, 's', Utilisateur::ROLE_ADMIN);
        } elseif( $this->_user->hasRole(Utilisateur::ROLE_RISKMANAGER)) {
            $queryBuilder = BaseRepository::filterByProfile($queryBuilder, 's', Utilisateur::ROLE_RISKMANAGER);
        } elseif($this->_user->hasRole(Utilisateur::ROLE_AUDITEUR)) {
            $queryBuilder = BaseRepository::filterByProfile($queryBuilder, 's', Utilisateur::ROLE_AUDITEUR);
        } elseif( $this->_user->hasRole(Utilisateur::ROLE_RESPONSABLE) && $criteria->getCartographie() && $criteria->isPhysical()==false ) {
            if((null!=$structure=$this->_user->getStructure()) && $this->_user->isManager()) {
                $queryBuilder->andWhere('IDENTITY(rm.structure) IN (:structureIds) OR IDENTITY(rp.structure) IN (:structureIds) ')
                    ->setParameter('structureIds', $structure->getChildrenIds());
            }
        } elseif($this->_user->hasRole(Utilisateur::ROLE_CHEFPROJET)) {
            $queryBuilder->andWhere('IDENTITY(rp.projet) IN (:projetIds)')
                ->setParameter('projetIds', $this->_user->getProjetIds());
        } elseif($this->_user->hasRole(Utilisateur::ROLE_PORTEUR) || $this->_user->hasRole(Utilisateur::ROLE_SUPERVISEUR)) {
            if($this->_user->hasStructureOfConsulteur()) {
                $consultions=$this->_user->getStructureOfConsulteur();
                $structure=$this->_user->getStructure();
                if( null != $structure && null != $consultions) {
                    $IDs = array();
                    foreach ($consultions as $st) { $IDs = array_merge($IDs, $st->getChildrenIds()); }
                    $queryBuilder->andWhere(' (IDENTITY(rm.structure) IN (:s) OR IDENTITY(rp.structure) IN (:s) ) OR 
											  (IDENTITY(rm.structure) IN (:IDs) OR IDENTITY(rp.structure) IN (:IDs) ) OR 
											  (rs.id IS NOT NULL OR re.id IS NOT NULL) ')
                        ->setParameter('s', $structure->getChildrenIds())
                        ->setParameter('IDs', $IDs);
                }
            } else {
                if( null != $structure=$this->_user->getStructure()) {
                    $queryBuilder->andWhere('(IDENTITY(rm.structure) IN (:s) OR IDENTITY(rp.structure) IN (:s) ) OR 
											 (rs.id IS NOT NULL OR re.id IS NOT NULL)')
                        ->setParameter('s', $structure->getChildrenIds());
                }
            }
        }
        return $queryBuilder;
    }

    /**
     * @param array $criteria
     * @return QueryBuilder
     */
    public function listToExportQueryBuilder($criteria) {
        $queryBuilder = $this->listValidQueryBuilder($criteria);
        return $queryBuilder->leftJoin('r.causeOfRisque', 'cor')
            ->leftJoin('r.causeOfRisque', 'cor')
            ->leftJoin('r.impactOfRisque', 'ior')
            ->leftJoin('cor.cause', 'c')
            ->leftJoin('i.impact', 'i')
            ->leftJoin('c.controle', 'o')
            ->leftJoin('r.planAction', 'pa');
    }

    /**
     * @param Risque $criteria
     * @return QueryBuilder
     */
    public function listUnValidatedRisquesQueryBuilder($criteria) {
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.processus', 'pm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rp.processus', 'pp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.menace', 'm')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('r.cartographie', 'cg')
            ->leftJoin('r.societe', 's')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->orderby('r.dateSaisie', 'DESC');
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['nouveau']);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        return $queryBuilder->addGroupBy('r.id');
    }

    /**
     * @param Risque $criteria
     * @return QueryBuilder
     */
    public function listUncompletedRisquesQueryBuilder($criteria) {
        $criteria = $criteria ? $criteria : new Risque();
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.menace', 'm')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('r.cartographie', 'cg')
            ->leftJoin('r.societe', 's')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->orderby('r.dateSaisie', 'DESC');
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['nouveau']);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        return $queryBuilder->addGroupBy('r.id');
    }

    /**
     * @param Risque $criteria
     * @return QueryBuilder
     */
    public function listRejectedRisquesQueryBuilder($criteria) {
        $criteria = $criteria ? $criteria : new Risque();
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.menace', 'm')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('r.cartographie', 'cg')
            ->leftJoin('r.societe', 's')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->orderby('r.dateSaisie', 'DESC');
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder->andWhere('r.etat = :etat')->setParameter('etat',$this->_states['risque']['rejete']);
        $this->filterBuilder($queryBuilder, $criteria);
        return $queryBuilder->addGroupBy('r.id');
    }

    /**
     * @param unknown $criteria
     */
    public function listToTransfert($criteria) {
        $criteria = $criteria ? $criteria : new Risque();
        $queryBuilder = $this->createQueryBuilder('r')
            ->innerJoin('r.risqueProjet', 'rp')
            ->innerJoin('rp.projet', 'p')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rp.structure', 'stP')
            ->leftJoin('r.menace', 'm')
            ->leftJoin('r.criticite', 'c')
            ->innerJoin('r.cartographie', 'cg')
            ->leftJoin('r.societe', 's')
            ->orderby('r.dateSaisie', 'DESC');
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder->andWhere('r.etat = :etat')
            ->setParameter('etat', $this->_states['risque']['valide'])
            ->andWhere('p.etat=:etatp')
            ->setParameter('etatp', $this->_states['projet']['cloture']);
        if($criteria->getMenace()) {
            $queryBuilder->andWhere('r.menace = :menace')->setParameter('menace', $criteria->getMenace());
        }
        $data = $criteria->getRisqueData($this->_ids['carto']);
        if(!$criteria->isPhysical() && $data){
            if($data->getDirection() || $data->getStructure()) {
                $structure = $data->getStructure() ? $data->getStructure() : ($data->getDirection() ? $data->getDirection() : null);
                $queryBuilder->andWhere('IDENTITY(rp.structure) IN (:structureIds)')->setParameter('structureIds', $structure->getStructureIds());
            }
            if(null!=$processus=$data->getProcessus()) {
                $queryBuilder->andWhere('IDENTITY(rm.processus) IN (:processusIds) OR IDENTITY(rp.processus) IN (:processusIds)')
                    ->setParameter('processusIds', $processus->getChildrenIds());
            }
        }
        if($criteria->cause) {
            $queryBuilder->innerJoin('r.causeOfRisque', 'cor')->andWhere('cor.cause = :cause')->setParameter('cause', $criteria->cause);
        }
        return $queryBuilder;
    }

    /**
     * @param integer $entite_id
     * @param integer $processus_id
     * @return QueryBuilder
     */
    public function listByProcessusQueryBuilder($entite_id, $processus_id) {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->leftJoin('r.activite', 'a')
            ->innerJoin('a.processus', 'c')
            ->innerJoin('c.structure', 's')
            ->innerJoin('s.entite', 'e')
            ->where('e.id = :entite_id')
            ->andWhere($queryBuilder->expr()->in('c.id',
                $queryBuilder->getEntityManager()->getRepository('App\Entity\Processus')->getIdsChilrenAndMeBuilder($processus_id)->getQuery()->getDQL()
            ))->setParameter('entite_id', $entite_id);
        if($processus_id) {
            $queryBuilder->setParameter('processus_id', $processus_id);
        }
        return $queryBuilder;
    }

    /**
     * @param integer $activite_id
     * @return QueryBuilder
     */
    public function listByActiviteQueryBuilder($activite_id) {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.activite', 'a')
            ->where('a.id = :activite_id')
            ->setParameter('activite_id', $activite_id);
    }

    /**
     * @return integer
     */
    public function getLastNumeroMetier(RisqueMetier $risque) {
        $data = $this->createQueryBuilder('r')
            ->select('MAX(r.numero) as number')
            ->innerJoin('r.risqueMetier', 'rm')
            ->where('rm.activite = :activite')
            ->setParameter('activite', $risque->getActivite())
            ->getQuery()->getOneOrNullResult();
        return $data['number'];
    }

    /**
     * @return integer
     */
    public function getLastNumeroEnvi(RisqueEnvironnemental $risque) {
        $data = $this->createQueryBuilder('r')
            ->select('MAX(r.numero) as number')
            ->innerJoin('r.risqueEnvironnemental', 're')
            ->where('re.equipement = :equipement')
            ->setParameter('equipement', $risque->getEquipement())
            ->getQuery()->getOneOrNullResult();
        return $data['number'];
    }
    /**
     * @return integer
     */
    public function getLastNumeroProjet(RisqueProjet $risque) {
        $data = $this->createQueryBuilder('r')
            ->select('MAX(r.numero) as number')
            ->innerJoin('r.risqueProjet', 'rp')
            ->where('rp.projet=:projet')
            ->setParameter('projet', $risque->getProjet())
            ->getQuery()->getOneOrNullResult();
        return $data['number'];
    }
    /**
     * @return integer
     */
    public function getLastNumeroSST(RisqueSST $risque) {
        $data = $this->createQueryBuilder('r')
            ->select('MAX(r.numero) as number')
            ->innerJoin('r.risqueSST', 'rs');
        if($risque->getEquipement())
            $data->where('rs.equipement = :equipement')
                ->setParameter('equipement', $risque->getEquipement());
        else
            $data->where('rs.activite = :equipement')
                ->setParameter('equipement', $risque->getEquipement());
        $data=$data->getQuery()->getOneOrNullResult();
        return $data['number'];
    }


    public function listAllQueryBuilder($criteria = null) {
        return $this->listValidQueryBuilder($criteria);
    }

    /**
     * @param array $criteria
     * @param array $carto
     * @param array $aggregate
     * @param array $current_aggregate
     * @return QueryBuilder
     */
    public function restitutionBuilder($criteria, $carto, $aggregate, $current_aggregate) {
        $queryBuilder = $this->listValidQueryBuilder($criteria);
        if($current_aggregate[$carto]==0) {
            $queryBuilder->select('partial r.{id, code, probabilite, gravite}, partial m.{ id, libelle }, partial c.{ id, niveau }, partial cg.{ id }')
                ->addSelect('partial rs.{ id, site }, partial re.{ id, site }, partial rm.{ id, structure }, partial rp.{ id, structure }')
                ->groupBy('r.id');
        } elseif($current_aggregate[$carto]==4) {
            $queryBuilder->select('m.libelle, count(r.id) as nombre')->groupBy('m.id');
        } elseif($carto==$this->_ids['carto']['metier']) {
            if($current_aggregate[$carto]==$aggregate['metier']['direction']) {
                $queryBuilder->select('dirM.libelle, count(r.id) as nombre')->groupBy('dirM.id');
            } elseif($current_aggregate[$carto]==$aggregate['metier']['structure']) {
                $queryBuilder->select('stM.libelle, count(r.id) as nombre')->groupBy('stM.id');
            } elseif($current_aggregate[$carto]==$aggregate['metier']['activite']) {
                $queryBuilder->select('a.libelle, count(r.id) as nombre')->groupBy('a.id');
            }
        } elseif($carto==$this->_ids['carto']['projet']) {
            if($current_aggregate[$carto]==$aggregate['projet']['direction']) {
                $queryBuilder->select('dirP.libelle, count(r.id) as nombre')->groupBy('dirP.id');
            } elseif($current_aggregate[$carto]==$aggregate['projet']['structure']) {
                $queryBuilder->select('stP.libelle, count(r.id) as nombre')->groupBy('stP.id');
            } elseif($current_aggregate[$carto]==$aggregate['projet']['projet']) {
                $queryBuilder->select('p.libelle, count(r.id) as nombre')->groupBy('p.id');
            }
        } elseif($carto==$this->_ids['carto']['sst']) {
            if($current_aggregate[$carto]==$aggregate['sst']['site']) {
                $queryBuilder->select('sitS.libelle, count(r.id) as nombre')->groupBy('sitS.id');
            } elseif($current_aggregate[$carto]==$aggregate['sst']['equipement']) {
                $queryBuilder->select('eqS.libelle, count(r.id) as nombre')->groupBy('eqS.id');
            } elseif($current_aggregate[$carto]==$aggregate['sst']['activite']) {
                $queryBuilder->select('actS.libelle, count(r.id) as nombre')->groupBy('actS.id');
            }
        } elseif($carto==$this->_ids['carto']['environnement']) {
            if($current_aggregate[$carto]==$aggregate['sst']['site']) {
                $queryBuilder->select('sitE.libelle, count(r.id) as nombre')->groupBy('sitE.id');
            } elseif($current_aggregate[$carto]==$aggregate['sst']['equipement']) {
                $queryBuilder->select('eqE.libelle, count(r.id) as nombre')->groupBy('eqE.id');
            } elseif($current_aggregate[$carto]==$aggregate['sst']['activite']) {
                $queryBuilder->select('actE.libelle, count(r.id) as nombre')->groupBy('actE.id');
            }
        }
        return $queryBuilder;
    }

    /**
     * @param Risque $criteria
     * @param integer $type
     * @return array
     */
    public function getMatrice($criteria, $type, &$probaKPIs, &$graviteKPIs) {
        $graviteKPIs = false;
        if($type==0 && $criteria->isPhysical()==false) {
            $probaKPIs = $this->getMaturiteProbabiliteByRisqueStructure($criteria, 0)->getQuery()->getArrayResult();
            $graviteKPIs = $this->getGraviteByRisqueStructure($criteria, 0)->getQuery()->getArrayResult();
        } elseif($type==1 && $criteria->isPhysical()==false) {
            $probaKPIs = $this->getMaturiteProbabiliteByRisqueStructure($criteria, 1)->getQuery()->getArrayResult();
            $graviteKPIs = $this->getGraviteByRisqueStructure($criteria, 1)->getQuery()->getArrayResult();
        } elseif($type==1 && $criteria->isPhysical()==true) {
            $probaKPIs  = $this->getMaturiteProbabiliteBySite($criteria)->getQuery()->getArrayResult();
            $graviteKPIs= $this->getGraviteByRisqueSite($criteria)->getQuery()->getArrayResult();
        } elseif($type==2 || $type==3) {
            $probaKPIs = $this->getMaturiteGraviteByType($criteria, $type)->getQuery()->getArrayResult();
        } elseif($type==4) {
            $probaKPIs = $this->getMaturiteGraviteProbabilteByRisque($criteria)->getQuery()->getArrayResult();
        } else {
            $qb = $this->createQueryBuilder('r')
                ->select('r.id, m.libelle, r.gravite, r.probabilite, 1 as nbRisk, (r.probabilite * r.gravite) as criticite')
                ->leftJoin('r.identification', 'i')
                ->leftJoin('r.evaluation', 'e')
                ->leftJoin('r.risqueMetier', 'rm')
                ->leftJoin('rm.activite', 'a')
                ->leftJoin('r.risqueProjet', 'rp')
                ->leftJoin('rp.projet', 'p')
                ->leftJoin('r.risqueEnvironnemental', 're')
                ->leftJoin('re.equipement', 'eqE')
                ->leftJoin('re.domaineActivite', 'actE')
                ->leftJoin('r.risqueSST', 'rs')
                ->leftJoin('rs.equipement', 'eqS')
                ->leftJoin('rs.domaineActivite', 'actS')
                ->leftJoin('r.utilisateur', 'u')
                ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
                ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
                ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
                ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
                ->leftJoin('re.site', 'sitE')
                ->leftJoin('rs.site', 'sitS')
                ->leftJoin('r.menace', 'm')
                ->leftJoin('r.criticite', 'c')
                ->leftJoin('r.cartographie', 'cg')
                ->leftJoin('r.societe', 's')
                ->leftJoin('rm.structure', 'sm')
                ->leftJoin('rp.structure', 'sp');
            $this->applyFilterByProfile($qb, $criteria);
            $probaKPIs = $this->filterBuilder($qb, $criteria)
                ->getQuery()->getArrayResult();
        }
    }

    /* requetes destinees pour les KPIs */
    /**
     * @param Risque $criteria
     */
    public function getGraviteByRisqueStructure($criteria,$lvl) {
        $criteria = $criteria ? $criteria : new Risque();
        $evalBuilder = $this->_em->getRepository('App\Entity\Evaluation')
            ->createQueryBuilder('ev')
            ->innerJoin('ev.risque','risk')
            ->select('MAX(ev.id) as id')
            ->groupBy('risk.id');
        $rep=$this->_em->getRepository('App\Entity\Structure');
        $queryBuilder = $rep->createQueryBuilder('q') ;
        $queryBuilder->innerJoin('q.typeStructure','ts')
            ->select('q.id,q.code code, q.name libelle,dom.libelle domaine, ROUND(AVG(n.valeur)) gravite, m.libelle menace')
            ->add('from', 'App\Entity\Risque r', true)
            ->leftJoin ('r.risqueMetier', 'rm')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM')
            ->leftJoin('rp.structure', 'stP')
            ->leftJoin('r.criticite', 'c')
            ->innerJoin('r.menace','m')
            ->innerJoin('r.evaluation', 'e')
            ->innerJoin('e.impactOfEvaluation','iOe')
            ->innerJoin('iOe.grille','g')
            ->innerJoin('g.note','n')
            ->innerJoin('iOe.impact','imp')
            ->innerJoin('imp.critere','crit')
            ->innerJoin('crit.domaine','dom')
            ->innerJoin('r.cartographie','cg')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->leftJoin('r.societe','s')
            ->andWhere($queryBuilder->expr()->in('e.id', $evalBuilder->getDQL()))
            ->andWhere ('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide']);
        if($lvl==0) {
            $queryBuilder->andWhere('q.lvl=:lvl')->setParameter('lvl', $lvl);
        } else{
            $queryBuilder->andWhere('q.lvl!=:lvl')->setParameter('lvl', 0);
        }
        $queryBuilder->andWhere('stM.lvl >= q.lvl or stP.lvl >= q.lvl')
            ->andWhere('stM.root = q.root or stP.root = q.root')
            ->andWhere('stM.lft  >= q.lft or stP.lft  >= q.lft')
            ->andWhere('stM.rgt <= q.rgt or stP.rgt <= q.rgt');
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        $queryBuilder->groupBy('m.id')
            ->addGroupBy('dom.id')
            ->addGroupBy('q.id');
        return $queryBuilder;
    }


    /**
     * @param Risque $criteria
     */
    public function getMaturiteProbabiliteByRisqueStructure($criteria,$lvl){
        $criteria = $criteria ? $criteria : new Risque();
        $rep=$this->_em->getRepository('App\Entity\Structure');

        $queryBuilder=$rep->createQueryBuilder('q')
            ->select('q.id,q.code code, q.name libelle,ROUND(AVG(r.probabilite)) probabilite, COUNT(r.id) nbrisk,ROUND(AVG(mt.valeur)) maturite')
            ->add('from', 'App\Entity\Risque r', true)
            ->leftJoin('r.maturiteTheorique','mt')
            ->leftJoin ('r.risqueMetier', 'rm')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM')
            ->leftJoin('rp.structure', 'stP')
            ->leftJoin('r.criticite', 'c')
            ->innerJoin('r.menace','m')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('e.impactOfEvaluation','iOe')
            ->leftJoin('iOe.grille','g')
            ->leftJoin('g.note','n')
            ->leftJoin('iOe.impact','imp')
            ->leftJoin('imp.critere','crit')
            ->leftJoin('crit.domaine','dom')
            ->innerJoin('r.cartographie','cg')
            ->leftJoin('r.societe','s')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->andWhere ('s = :societe')->setParameter('societe', $this->_user->getSociete())
            ->andWhere ('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide']);
        if($lvl==0)
            $queryBuilder->andWhere('q.lvl=:lvl')->setParameter('lvl', $lvl);
        else
            $queryBuilder->andWhere('q.lvl!=:lvl')->setParameter('lvl', 0);

        $queryBuilder->andWhere('stM.lvl >= q.lvl or stP.lvl >= q.lvl')
            ->andWhere('stM.root = q.root or stP.root = q.root')
            ->andWhere('stM.lft  >= q.lft or stP.lft  >= q.lft')
            ->andWhere('stM.rgt <= q.rgt or stP.rgt <= q.rgt');
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);

        $queryBuilder-> addGroupBy('q.id');

        if(count($criteria->maturiteForKpi)>0){
            $valeurs_maturite_criteria = array();
            foreach ($criteria->maturiteForKpi as $key =>$value)
                $valeurs_maturite_criteria [] = intval($value->getValeur());
            $queryBuilder->having('maturite IN (:maturites)')->setParameter('maturites', $valeurs_maturite_criteria);
        }
        return $queryBuilder;
    }

    /**
     * @param Risque $criteria
     */
    public function getGraviteByRisqueSite($criteria){
        $criteria = $criteria ? $criteria : new Risque();
        $evalBuilder = $this->_em->getRepository('App\Entity\Evaluation')
            ->createQueryBuilder('ev')
            ->innerJoin('ev.risque','risk')
            ->select('MAX(ev.id) as id')
            ->groupBy('risk.id');

        $queryBuilder=$this->createQueryBuilder('r');
        $queryBuilder
            ->innerJoin('r.menace','m')
            ->leftJoin('r.maturiteTheorique','mt')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('e.impactOfEvaluation','iOe')
            ->leftJoin('iOe.grille','g')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('g.note','n')
            ->leftJoin('iOe.impact','imp')
            ->leftJoin('imp.critere','crit')
            ->leftJoin('crit.domaine','dom')
            ->leftJoin('r.societe','s')
            ->innerJoin('r.cartographie','cg')
            ->andWhere($queryBuilder->expr()->in('e.id', $evalBuilder->getDQL()))
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide']);
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        if($criteria->getCartographie()->getId()==3) {
            $queryBuilder-> select('sitS.id,sitS.code code, sitS.libelle libelle, dom.libelle dom_libelle,ROUND(AVG(n.valeur)) gravite, m.libelle menace')
                ->groupBy('m.id')
                ->addGroupBy('dom.id')
                ->addGroupBy('sitS.id');
        } else {
            $queryBuilder->select('sitE.id,sitE.code code, sitE.libelle libelle, dom.libelle dom_libelle,ROUND(AVG(n.valeur)) gravite, m.libelle menace')
                ->groupBy('m.id')
                ->addGroupBy('dom.id')
                ->addGroupBy('sitE.id');
        }
        return $queryBuilder;

    }

    /**
     *
     * @param Risque $criteria
     */
    public function getMaturiteProbabiliteBySite($criteria){
        $criteria = $criteria ? $criteria : new Risque();

        $queryBuilder=$this->createQueryBuilder('r');
        $queryBuilder
            ->innerJoin('r.menace','m')
            ->leftJoin('r.maturiteTheorique','mt')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('e.impactOfEvaluation','iOe')
            ->leftJoin('iOe.grille','g')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('g.note','n')
            ->leftJoin('iOe.impact','imp')
            ->leftJoin('imp.critere','crit')
            ->leftJoin('crit.domaine','dom')
            ->innerJoin('r.societe','s')
            ->innerJoin('r.cartographie','cg')
            ->andWhere ('s = :societe')->setParameter('societe', $this->_user->getSociete())
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide']);
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        if($criteria->getCartographie()->getId()==3)
            $queryBuilder	    ->select('sitS.id, sitS.code code, sitS.libelle libelle, ROUND(MAX(mt.valeur)) maturite, ROUND(AVG(r.probabilite)) probabilite, COUNT(r.id) nbrisk')
                ->groupBy('sitS.id');
        else
            $queryBuilder	    ->select('sitE.id, sitE.code code, sitE.libelle libelle, ROUND(MAX(mt.valeur)) maturite, ROUND(AVG(r.probabilite)) probabilite, COUNT(r.id) nbrisk')
                ->groupBy('sitE.id');

        if(count($criteria->maturiteForKpi)>0){
            $valeurs_maturite_criteria = array();
            foreach ($criteria->maturiteForKpi as $key =>$value)
                $valeurs_maturite_criteria [] = intval($value->getValeur());
            $queryBuilder->having('maturite IN (:maturites)')->setParameter('maturites', $valeurs_maturite_criteria);
        }
        return $queryBuilder;

    }

    /**
     * @param Risque $criteria
     * @param integer $type
     */
    public function getMaturiteGraviteByType($criteria,$type){
        $criteria = $criteria ? $criteria : new Risque();
        $carto = $criteria->getCartographie()->getId();
        $queryBuilder=$this ->createQueryBuilder('r')
            ->innerJoin('r.menace','m')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.maturiteTheorique','rt')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('e.impactOfEvaluation','iOe')
            ->leftJoin('iOe.grille','g')
            ->leftJoin('g.note','n')
            ->leftJoin('iOe.impact','imp')
            ->leftJoin('imp.critere','crit')
            ->leftJoin('crit.domaine','dom')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('r.maturiteTheorique','mt')
            ->innerJoin('r.cartographie','cg')
            ->andWhere('r.etat = :etat')
            ->innerJoin('r.societe','s')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->setParameter('etat', $this->_states['risque']['valide']);
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        if($type==2 && $carto==1) {
            $queryBuilder ->select('count(r.id) nbRisk, a.id, a.libelle , a.code, ROUND(AVG(r.gravite)) gravite,ROUND(AVG(r.probabilite)) probabilite, ROUND(AVG(mt.valeur)) maturite,(ROUND(AVG(r.probabilite))*ROUND(AVG(r.gravite))) criticite');
            $queryBuilder->groupBy('a.id');
        }elseif ($type==2 && $carto==2) {
            $queryBuilder ->select('count(r.id) nbRisk, p.id, p.libelle , p.code, ROUND(AVG(r.gravite)) gravite,ROUND(AVG(r.probabilite)) probabilite, ROUND(AVG(mt.valeur)) maturite,(AVG(r.probabilite)*AVG(r.gravite)) criticite');
            $queryBuilder->groupBy('p.id');
        }elseif ($type==3 && $carto==3) {
            $queryBuilder ->select('count(r.id) nbRisk, eqS.id, eqS.libelle , eqS.code, ROUND(AVG(r.gravite)) gravite,ROUND(AVG(r.probabilite)) probabilite, ROUND(AVG(mt.valeur)) maturite,(AVG(r.probabilite)*AVG(r.gravite)) criticite');
            $queryBuilder->groupBy('eqS.id');
        }elseif ($type==3 && $carto==4) {
            $queryBuilder ->select('count(r.id) nbRisk, eqE.id, eqE.libelle , eqE.code, ROUND(AVG(r.gravite)) gravite,ROUND(AVG(r.probabilite)) probabilite, ROUND(AVG(mt.valeur)) maturite,(AVG(r.probabilite)*AVG(r.gravite)) criticite');
            $queryBuilder->groupBy('eqE.id');
        }
        if(count($criteria->maturiteForKpi)>0){
            $valeurs_maturite_criteria = array();
            foreach ($criteria->maturiteForKpi as $key =>$value)
                $valeurs_maturite_criteria [] = intval($value->getValeur());
            $queryBuilder->having('maturite IN (:maturites)')->setParameter('maturites', $valeurs_maturite_criteria);
        }
        if(count($criteria->criticiteForKpi)>0){
            $valeurs_criticite_criteria = array();
            foreach ($criteria->criticiteForKpi as $key =>$value)
                for ($i=$value->getVmin(); $i<=$value->getVmax();$i++)
                    $valeurs_criticite_criteria [] = $i;
            $queryBuilder->andHaving('criticite IN (:criticites)')->setParameter('criticites', $valeurs_criticite_criteria);
        }
        return $queryBuilder;
    }

    /**
     *
     * @param Risque $criteria
     */
    public function getMaturiteGraviteProbabilteByRisque($criteria){
        $criteria = $criteria ? $criteria : new Risque();
        $evalBuilder = $this->_em->getRepository('App\Entity\Evaluation')
            ->createQueryBuilder('ev')
            ->innerJoin('ev.risque','risk')
            ->select('MAX(ev.id) ')
            ->groupBy('risk.id');

        $queryBuilder=$this ->createQueryBuilder('r');
        $queryBuilder->select('count(r.id) nbRisk ,m.libelle libelle, m.id id,ROUND(AVG(n.valeur)) gravite, MAX(rt.valeur) maturite');
        if($criteria->getCartographie()==null || $criteria->getCartographie()->getId()<=2) {
            $queryBuilder->addSelect('MAX(r.probabilite) probabilite, (MAX(r.probabilite)*ROUND(AVG(n.valeur))) criticite');
        } else {
            $queryBuilder->addSelect('ROUND(AVG(r.probabilite)) probabilite, (ROUND(AVG(r.probabilite))*ROUND(AVG(n.valeur))) criticite');
        }
        $queryBuilder->innerJoin('r.menace','m')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.maturiteTheorique','rt')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('e.impactOfEvaluation','iOe')
            ->leftJoin('iOe.grille','g')
            ->leftJoin('g.note','n')
            ->leftJoin('iOe.impact','imp')
            ->leftJoin('imp.critere','crit')
            ->leftJoin('crit.domaine','dom')
            ->leftJoin('r.cartographie','cg')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->leftJoin('rm.processus', 'pm')
            ->leftJoin('rp.processus', 'pp')
            ->andWhere($queryBuilder->expr()->in('e.id', $evalBuilder->getDQL()))
            //->orWhere('e.id is null')
            ->leftJoin('r.societe','s')
            ->andWhere ('s = :societe')->setParameter('societe', $this->_user->getSociete())
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide']);
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        $queryBuilder->groupBy('m.id');
        if(count($criteria->maturiteForKpi)>0){
            $valeurs_maturite_criteria = array();
            foreach ($criteria->maturiteForKpi as $value) {
                $valeurs_maturite_criteria [] = intval($value->getValeur());
            }
            $queryBuilder->having('maturite IN (:maturites)')->setParameter('maturites', $valeurs_maturite_criteria);
        }
        if(count($criteria->criticiteForKpi)>0){
            $valeurs_criticite_criteria = array();
            foreach ($criteria->criticiteForKpi as $value)
                for ($i=$value->getVmin(); $i<=$value->getVmax();$i++) {
                    $valeurs_criticite_criteria [] = $i;
                }
            $queryBuilder->andHaving('criticite IN (:criticites)')->setParameter('criticites', $valeurs_criticite_criteria);
        }
        if(count($criteria->graviteForKpi)>0) {
            $queryBuilder->andHaving('gravite IN (:gravites)')->setParameter('gravites', $criteria->graviteForKpi);
        }
        if(count($criteria->probaForKpi)>0){
            $queryBuilder->andHaving('probabilite IN (:probabilites)')->setParameter('probabilites', $criteria->probaForKpi);
        }
        return $queryBuilder;
    }

    /**
     * @param Risque $criteria
     */
    public function risqueTransverses($criteria){
        $criteria = $criteria ? $criteria : new Risque();
        $carto=$criteria->getCartographie()->getId();
        $queryBuilder=$this ->createQueryBuilder('r')
            ->innerJoin('r.menace', 'm')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.maturiteTheorique','rt')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('e.impactOfEvaluation','iOe')
            ->leftJoin('iOe.grille','g')
            ->leftJoin('g.note','n')
            ->leftJoin('iOe.impact','imp')
            ->leftJoin('imp.critere','crit')
            ->leftJoin('crit.domaine','dom')
            ->innerJoin('r.cartographie', 'cg')
            ->leftJoin('r.societe','s')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->andWhere ('s = :societe')->setParameter('societe', $this->_user->getSociete())
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide']);
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder =  $this->filterBuilder($queryBuilder, $criteria);
        if($carto<=2) {
            if($carto==1) {
                $queryBuilder->select($queryBuilder->expr()->countDistinct('stM.root').' occurence')
                    ->groupBy('m.id')
                    ->andHaving($queryBuilder->expr()->gt($queryBuilder->expr()->countDistinct('stM.root'), 1));
            } else {
                $queryBuilder->select($queryBuilder->expr()->countDistinct('stP.root').' occurence')
                    ->groupBy('m.id')
                    ->andHaving($queryBuilder->expr()->gt($queryBuilder->expr()->countDistinct('stP.root'), 1));
            }
        } else {
            if($carto==3)
                $queryBuilder	->select($queryBuilder->expr()->countDistinct('sitS.id').' occurence')
                    ->groupBy('m.id')
                    ->andHaving($queryBuilder->expr()->gt($queryBuilder->expr()->countDistinct('sitS.id'), 1));
            else
                $queryBuilder	->select($queryBuilder->expr()->countDistinct('sitE.id').' occurence')
                    ->groupBy('m.id')
                    ->andHaving($queryBuilder->expr()->gt($queryBuilder->expr()->countDistinct('sitE.id'), 1));
        }
        $queryBuilder->addSelect('m.id, m.libelle libelle');
        return $queryBuilder;
    }

    /**
     * @param Risque $entity
     */
    public function getGraviteByMenaceStructure($entity,$carto){
        $queryBuilder=$this ->createQueryBuilder('r')
            ->innerJoin('r.menace','m')
            ->innerJoin('r.cartographie','carto')
            ->andWhere('m=:menace')->setParameter('menace',$entity)
            ->andWhere('carto.id = :carto')->setParameter('carto',$carto)
        ;
        return $queryBuilder;
    }

    /**
     * @param Risque $criteria
     */
    public function getMenacesTotalByYear($criteria){
        $criteria = $criteria ? $criteria : new Risque();
        $queryBuilder=$this ->createQueryBuilder('r')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.processus', 'pm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rp.processus', 'pp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.menace', 'm')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('r.cartographie', 'cg')
            ->leftJoin('r.societe','s')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->andWhere ('s = :societe')->setParameter('societe', $this->_user->getSociete())
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide'])
            ->select ('count(m.id) nombre, YEAR(r.dateSaisie) annee');
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        return $queryBuilder->groupBy('annee');
    }

    /**
     *
     * @param Risque $criteria
     */
    public function getMaturiteGraviteProbabiliteByRisqueByYear($criteria){
        $current_year = date('Y');
        $years = array($current_year-2, $current_year-1, $current_year);
        $criteria = $criteria ? $criteria : new Risque();
        $evalBuilder = $this->_em->getRepository('App\Entity\Evaluation')->createQueryBuilder('ev');
        $evalBuilder ->innerJoin('ev.risque','risk')
            ->select('MAX(ev.id)')
            ->groupBy('risk.id')->addGroupBy('ev.annee');

        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->select('count(r.id) nbrisk, e.annee annee,m.libelle menace, m.id id,ROUND(AVG(n.valeur)) gravite, ROUND(AVG(r.probabilite)) probabilite, ROUND(MAX(rt.valeur)) maturite, (ROUND(AVG(n.valeur))*ROUND(AVG(r.probabilite))) criticite')
            ->innerJoin('r.menace','m')
            ->leftJoin('r.maturiteTheorique','rt')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('r.societe', 's')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('e.impactOfEvaluation','iOe')
            ->leftJoin('iOe.grille','g')
            ->leftJoin('g.note','n')
            ->leftJoin('iOe.impact','imp')
            ->leftJoin('imp.critere','crit')
            ->leftJoin('crit.domaine','dom')
            ->leftJoin('r.cartographie','cg')
            ->leftJoin('rm.structure', 'sm')
            ->leftJoin('rp.structure', 'sp')
            ->andWhere($queryBuilder->expr()->in('e.id', $evalBuilder->getDQL()))
            ->andWhere ('s = :societe')->setParameter('societe', $this->_user->getSociete())
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide']);
        $this->applyFilterByProfile($queryBuilder, $criteria);
        $queryBuilder = $this->filterBuilder($queryBuilder, $criteria);
        $queryBuilder->groupBy('m.id')->addGroupBy('e.annee');
        if(count($criteria->maturiteForKpi)>0) {
            $valeurs_maturite_criteria = array();
            foreach ($criteria->maturiteForKpi as $value) {
                $valeurs_maturite_criteria [] = intval($value->getValeur());
            }
            $queryBuilder->having('maturite IN (:maturites)')->setParameter('maturites', $valeurs_maturite_criteria);
        }
        if(count($criteria->criticiteForKpi)>0) {
            $valeurs_criticite_criteria = array();
            foreach ($criteria->criticiteForKpi as $value) {
                for ($i=$value->getVmin(); $i<=$value->getVmax();$i++) {
                    $valeurs_criticite_criteria [] = $i;
                }
            }
            $queryBuilder->andHaving('criticite IN (:criticites)')->setParameter('criticites', $valeurs_criticite_criteria);
        }
        if(count($criteria->graviteForKpi)>0) {
            $queryBuilder->andHaving('gravite IN (:gravites)')->setParameter('gravites', $criteria->graviteForKpi);
        }
        if(count($criteria->probaForKpi)>0) {
            $queryBuilder->andHaving('probabilite IN (:probabilites)')->setParameter('probabilites', $criteria->probaForKpi);
        }
        if ($criteria->anneeEvaluationDebut) {
            $queryBuilder->andHaving('annee >=:debut')->setParameter('debut', $criteria->anneeEvaluationDebut);
        }
        if ($criteria->anneeEvaluationFin) {
            $queryBuilder->andHaving('annee <=:fin')->setParameter('fin', $criteria->anneeEvaluationFin);
        }
        if(!$criteria->anneeEvaluationFin && !$criteria->anneeEvaluationFin) {
            $queryBuilder->andHaving('annee in (:years)')->setParameter('years', $years);
        }
        return $queryBuilder->orderBy('e.annee','ASC')->orderBy('m.id');
    }

    public function getNextId() {
        $data = $this->createQueryBuilder('r')->select('MAX(r.id) as maxi')->getQuery ()->getArrayResult ();
        return(int) $data [0] ['maxi'] + 1;
    }

    public function listByImport($criteria, $id){
        $criteria = $criteria ? $criteria : new Risque();
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('r.menace', 'm')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('rm.structure', 'stM')
            ->leftJoin('rp.structure', 'stP')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->innerJoin('r.cartographie', 'cg')
            ->innerJoin('r.societe', 's')
            ->innerJoin('r.chargement', 'c')
            ->andWhere('c.id=:cId')->setParameter('cId', $id);
        return $queryBuilder->groupBy('r.id');
    }

    public function checkDoublonsMetierrrrrrrrr()
    {
        return $this->createQueryBuilder('r')
            ->select('rm.id')
            ->innerJoin('r.menace', 'm')
            ->innerJoin('r.risque_metier', 'rm')
            ->innerJoin('rm.activite', 'a')
            ->innerJoin('rm.structure', 's')
            ->innerJoin('rm.processus', 'p')
            ->where('m.id = :menace')->setParameter('menace', 5)
            ->where('s.id = :structure')->setParameter('structure', 564)
            ->where('p.id = :processus')->setParameter('processus', 7)
            ->where('a.id = :activite')->setParameter('activite', 15)
            ;
    }

    public function getNumberToMigrate() {
        $data = $this->createQueryBuilder('q')->select('COUNT(q.id) as number')->where('q.etat = 1 AND q.tobeMigrated = true')
            ->getQuery()
            ->getArrayResult();
        return $data[0]['number'];
    }

    public function findToMigrate() {
        return $this->createQueryBuilder('q')->where('q.etat = 1 AND q.tobeMigrated = true')
            ->setMaxResults(500)
            ->getQuery()
            ->getResult();
    }
}
