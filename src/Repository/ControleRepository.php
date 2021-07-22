<?php

namespace App\Repository;

use App\Entity\Activite;
use App\Entity\Controle;
use App\Entity\Risque;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;


class ControleRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Controle::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    public function findAll() {
        //return $this->filterBySociete($this->createQueryBuilder('q'))->getQuery()->execute();
        return $this->createQueryBuilder('c')->getQuery()->getResult();
    }
    /**
     * @param integer $entite_id
     * @param integer $processus_id
     * @return QueryBuilder
     */
    public function listByProcessusQueryBuilder($entite_id, $processus_id) {
        $queryBuilder = $this->createQueryBuilder('co');
        $queryBuilder->andWhere($queryBuilder->expr()->in('co.id', $this->getIdsByProcessusQueryBuilder($entite_id, $processus_id)->getQuery()->getDQL()))
            ->setParameter('entite_id', $entite_id);
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
        return $this->createQueryBuilder('c')
            ->innerJoin('c.risque', 'r')
            ->innerJoin('r.activite', 'a')
            ->where('a.id = :activite_id')
            ->setParameter('activite_id', $activite_id);
    }

    /**
     * @param integer $risque_id
     * @return QueryBuilder
     */
    public function listByRisqueQueryBuilder($risque_id) {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.risque', 'r')
            ->where('r.id = :risque_id')
            ->setParameter('risque_id', $risque_id);
    }

    /**
     * @param integer $entite_id
     * @param integer $processus_id
     * @return QueryBuilder
     */
    private function getIdsByProcessusQueryBuilder($entite_id, $processus_id) {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->select('c.id')
            ->innerJoin('c.risque', 'r')
            ->innerJoin('r.activite', 'a')
            ->innerJoin('a.processus', 't')
            ->innerJoin('t.structure', 's')
            ->innerJoin('s.entite', 'e')
            ->where($queryBuilder->expr()->in('t.id',
                $queryBuilder->getEntityManager()->getRepository('App\Entity\Processus')->getIdsChilrenAndMeBuilder($processus_id)->getQuery()->getDQL()
            ))->andWhere('e.id = :entite_id');
        return $queryBuilder;
    }

    /**
     * @param Risque $entity
     * @return integer
     */
    public function getLastNumero($entity) {
        $data = $this->createQueryBuilder('r')->select('MAX(r.numero) as number')
            ->innerJoin('r.causeOfRisque', 'cor')
            ->where('cor.risque = :entity')
            ->setParameter('entity', $entity)
            ->getQuery()->getOneOrNullResult();
        return $data['number'];
    }

    /* (non-PHPdoc)
     * @see \Orange\QuickMakingBundle\Repository\EntityRepository::listAllQueryBuilder()
     */
    public function listAllQueryBuilder($criteria = null) {
        $criteria = $criteria ? $criteria : new \App\Entity\Controle();
        // TODO: Auto-generated method stub
        $queryBuilder = $this->createQueryBuilder('q')
            ->innerJoin('q.causeOfRisque', 'cor')
            ->leftJoin('cor.risque', 'r')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM')
            ->leftJoin('rp.structure', 'stP')
            ->leftJoin('r.societe', 's')
            ->leftJoin('q.superviseur', 'sup')
        ;
        $this->applyFilterByProfile($queryBuilder);
        if($criteria->menace) {
            $queryBuilder->andWhere('r.menace = :menace')->setParameter('menace', $criteria->menace);
        }
        if($criteria->getCause()) {
            $queryBuilder
                ->andWhere('cor.cause = :cause')
                ->setParameter('cause', $criteria->cause);
        }
        if(count($criteria->getTraitement())>0){
            $traitementIds=array();
            foreach ($criteria->getTraitement() as $key=>$val)
                $traitementIds[]=$val->getId();

            $queryBuilder
                ->leftJoin('q.traitement', 't')
                ->andWhere('t.id in (:traitement)')
                ->setParameter('traitement', $traitementIds);
        }
        $data = $criteria->getRisque()?$criteria->getRisque()->getRisqueData($this->_ids['carto']):null;
        if($data==null) {
        } elseif($data->isPhysical()) {
            if($data->getSite()) {
                $queryBuilder->andWhere('rs.site = :site OR re.site = :site')->setParameter('site', $data->getSite());
            }
            if($data->getDomaineActivite()) {
                $queryBuilder->andWhere('rs.domaineActivite = :site OR re.domaineActivite = :site')->setParameter('domaineActivite', $data->getDomaineActivite());
            }
            if($data->getEquipement()) {
                $queryBuilder->andWhere('rs.equipement = :site OR re.equipement = :equipement')->setParameter('equipement', $data->getEquipement());
            }
        } else {
            if($data->getDirection() || $data->getStructure()) {
                $structure = $data->getDirection() ? $data->getDirection() : $data->getStructure();
                $queryBuilder->leftJoin('rm.structure', 'sm')->leftJoin('rp.structure', 'sp')
                    ->andWhere('(sm.lvl >= :slvl OR sp.lvl >= :slvl) AND (sm.root = :sroot OR sp.root = :sroot) AND (sm.lft >= :slft OR sp.lft >= :slft) AND (sm.rgt <= :srgt OR sp.rgt <= :srgt)')
                    ->setParameter('slvl', $structure->getLvl())->setParameter('sroot', $structure->getRoot())
                    ->setParameter('slft', $structure->getLft())->setParameter('srgt', $structure->getRgt());
            }
            if($data->getProcessus()) {
                $processus = $data->getProcessus();
                $queryBuilder->leftJoin('rm.processus', 'pm')->leftJoin('rp.processus', 'pp')
                    ->andWhere('(pm.lvl >= :slvl OR pp.lvl >= :slvl) AND (pm.root = :sroot OR pp.root = :sroot) AND (pm.lft >= :slft OR pp.lft >= :slft) AND (pm.rgt <= :srgt OR pp.rgt <= :srgt)')
                    ->setParameter('slvl', $processus->getLvl())->setParameter('sroot', $processus->getRoot())
                    ->setParameter('slft', $processus->getLft())->setParameter('srgt', $processus->getRgt());
            }
            if($criteria->cartographie->getId()==Risque::$carto['metier'] && $data->getActivite()) {
                $queryBuilder->andWhere('rm.activite = :activite')->setParameter('activite', $data->getActivite());
            } elseif($criteria->cartographie->getId()==Risque::$carto['projet'] && $data->getProjet()) {
                $queryBuilder->andWhere('rp.projet = :projet')->setParameter('projet', $data->getProjet());
            }
        }
        return $queryBuilder->addGroupBy('q.id');
    }


    public function applyFilterByProfile($queryBuilder){
        if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN))
            $queryBuilder=BaseRepository::filterByProfile($queryBuilder, null,Utilisateur::ROLE_SUPER_ADMIN);
        if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)){
            $queryBuilder=BaseRepository::filterByProfile($queryBuilder, 's',Utilisateur::ROLE_ADMIN);
        }elseif($this->_user->hasRole(Utilisateur::ROLE_RISKMANAGER)){
            $queryBuilder=BaseRepository::filterByProfile($queryBuilder, 's',Utilisateur::ROLE_RISKMANAGER);
        }elseif($this->_user->hasRole(Utilisateur::ROLE_AUDITEUR)) {
            $queryBuilder=BaseRepository::filterByProfile($queryBuilder, 's',Utilisateur::ROLE_AUDITEUR);
        }elseif($this->_user->hasRole(Utilisateur::ROLE_RESPONSABLE)){
            if($this->_user->getManager()==true){
                $structure=$this->_user->getStructure();
                $queryBuilder
                    ->orWhere('stM.lvl >= :lvl or stP.lvl >= :lvl')
                    ->andWhere('stM.root = :root or stP.root = :root')
                    ->andWhere('stM.lft  >= :lft or stP.lft  >= :lft')
                    ->andWhere('stM.rgt <= :rgt or stP.rgt <= :rgt')
                    ->setParameter('lvl', $structure->getLvl())
                    ->setParameter('root', $structure->getRoot())
                    ->setParameter('lft', $structure->getLft())
                    ->setParameter('rgt', $structure->getRgt());
            }
            if($this->_user->getSite()->count()!=0){
                $arrSite=array();
                foreach($this->_user->getSite() as $site)
                    $arrSite[]=$site->getId();
                $queryBuilder->leftJoin('rs.site', 'sitS')
                    ->leftJoin('re.site', 'sitE')
                    ->orWhere('sitE.id in (:site) or sitS.id in (:site)')
                    ->setParameter('site', $arrSite);
            }
        }
    }


    /**
     * @param Risque $criteria
     */
    public function getControlesTotalByYear($criteria){
        $risqueRepo = $this->_em->getRepository('App\Entity\Risque');
        $criteria = $criteria ? $criteria : new \App\Entity\Risque();
        $queryBuilder = $this->createQueryBuilder('ct')
            ->innerJoin('ct.causeOfRisque','cOr')
            ->innerJoin('cOr.risque','r')
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
            ->andWhere('s = :societe')->setParameter('societe', $this->_user->getSociete())
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide'])
            ->select ('count(ct.id) nombre, YEAR(ct.dateCreation) annee');
        $queryBuilder = $risqueRepo->filterBuilder($queryBuilder, $criteria);
        return $queryBuilder->groupBy('annee');
    }

    /**
     * @param Risque $criteria
     */
    public function getControles($criteria){
        $criteria = $criteria ? $criteria : new \App\Entity\Risque();
        $queryBuilder = $this->createQueryBuilder('c')
            ->innerJoin('c.causeOfRisque','cOr')
            ->innerJoin('cOr.risque','r')
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
            ->leftJoin('r.cartographie', 'cg')
            ->leftJoin('r.societe', 's')
            ->leftJoin('c.maturiteReel','mr')
            ->leftJoin('c.maturiteTheorique','mt')
            ->andWhere('s = :societe')->setParameter('societe', $this->_user->getSociete())
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide']);
        $queryBuilder = $this->_em->getRepository('App\Entity\Risque')->filterBuilder($queryBuilder, $criteria);
        $queryBuilder = $this->_em->getRepository('App\Entity\Risque')->applyFilterByProfile($queryBuilder, $criteria);
        if(count($criteria->maturiteReels)>0) {
            $valeurs_maturite_criteria = array();
            foreach($criteria->maturiteReels as $value) {
                $valeurs_maturite_rl_criteria [] = intval($value->getValeur());
            }
            $queryBuilder->andWhere('mr.valeur IN (:maturites)')->setParameter('maturites', $valeurs_maturite_rl_criteria);
        }
        if(count($criteria->maturiteTheoriques)>0){
            $valeurs_maturite_th_criteria = array();
            foreach ($criteria->maturiteTheoriques as $key =>$value)
                $valeurs_maturite_criteria [] = intval($value->getValeur());
            $queryBuilder->andWhere('mt.valeur IN (:maturites)')->setParameter('maturites', $valeurs_maturite_th_criteria);
        }
        return $queryBuilder->addGroupBy('r.id');
    }
}
