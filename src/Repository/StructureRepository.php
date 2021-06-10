<?php

namespace App\Repository;

use App\Entity\Processus;
use App\Entity\Structure;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

class StructureRepository extends ServiceEntityRepository {

    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Structure::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    public function findOneByFullname($fullname = null) {
        if($fullname==null) {
            return null;
        }
        $data = explode('/', $fullname);
        if(count($data)==1) {
            return $this->findOneByLibelle($data[0]);
        }
        $last = $data[count($data)-1];
        unset($data[count($data)-1]);
        $qb = $this->createQueryBuilder('s');
        return $qb->leftJoin(Structure::class, 'p', 'WITH', 'p.root = s.root')
            ->where('s.libelle LIKE :structure')
            ->andWhere($qb->expr()->in('p.libelle', $data))
            ->setParameter('structure', $last)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param integer $id
     * @return array
     */
    public function listAllDirectionBySociete() {
        $societe_id=$this->_user->getSociete()->getId();
        $query = $this->createQueryBuilder('s')
            ->where('IDENTITY(s.societe)=:societe_id')->setParameter('societe_id', $societe_id)
            ->andWhere('s.lvl=:lvl')->setParameter('lvl', 0);
        if( $this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {

        } elseif(! $this->_user->hasRole(Utilisateur::ROLE_ADMIN) && !$this->_user->hasRole(Utilisateur::ROLE_RISKMANAGER) && !$this->_user->hasRole(Utilisateur::ROLE_AUDITEUR)) {
            $query->andWhere('s.root=:root')->setParameter('root', $this->_user->getStructure()->getRoot());
        }
        return $query->orderBy('s.name');
    }

    /**
     *
     * @return QueryBuilder
     */
    public function listByType($type){
        $societe_id=$this->_user->getStructure()->getSociete()->getId();
        $query = $this->createQueryBuilder('s')
            ->where('IDENTITY(s.societe)=:societe_id')->setParameter('societe_id', $societe_id);
        if($type==0)
            $query->andWhere('s.lvl=:lvl')->setParameter('lvl',$type);
        else
            $query->andWhere('s.lvl!=:lvl')->setParameter('lvl',0);
        return $query;
    }

    /**
     *
     * @return QueryBuilder
     */
    public function listBySociete() {
        $societe_id=$this->_user->getStructure()->getSociete()->getId();
        $query = $this->createQueryBuilder('s')
            ->where('IDENTITY(s.societe)=:societe_id')->setParameter('societe_id', $societe_id)
        ;
        return $query;
    }

    /**
     * @param integer $id
     * @return array
     */
    public function listByParent($id = null) {
        $structure = $this->_user->getStructure();
        $query = $this->createQueryBuilder('s')
            ->leftJoin('s.parent', 'p');
        if($id) {
            if($this->_user->hasRole(Utilisateur::ROLE_RESPONSABLE_ONLY)){
                $query->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
                    ->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
                    ->andWhere('s.lft >= :lft')->setParameter('lft', $structure->getLft())
                    ->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
            }else
                $query->andWhere('p.id = :id')->setParameter('id', $id);
        } else {
            $query->where('p IS NULL');
        }
        return $query->orderBy('s.name');
    }

    /**
     * @param \App\Entity\Structure $structure
     * @return QueryBuilder
     */
    public function listAllQueryBuilder($structure = null) {
        $queryBuilder = $this->createQueryBuilder('q');
        if($structure && $structure->getParent()) {
            $queryBuilder->andWhere('q.lvl >= :level')->setParameter('level', $structure->getParent()->getLvl())
                ->andWhere('q.root = :root')->setParameter('root', $structure->getParent()->getRoot())
                ->andWhere('q.lft >= :lft')->setParameter('lft', $structure->getParent()->getLft())
                ->andWhere('q.rgt <= :rgt')->setParameter('rgt', $structure->getParent()->getRgt());
        }
        if($structure && $structure->getTypeStructure()) {
            $queryBuilder->andWhere('q.typeStructure = :typeStructure')->setParameter('typeStructure', $structure->getTypeStructure());
        }

        return BaseRepository::filterBySociete($queryBuilder, 'q', $this->_user)->orderBy('q.lvl');
    }

    /* (non-PHPdoc)
     * @see EntityRepository::findAll()
     */
    public function findAll() {
        $queryBuilder = $this->createQueryBuilder('q');
        return BaseRepository::filterBySociete($queryBuilder)->getQuery()->execute();
    }


    public function filter() {
        $queryBuilder = $this->createQueryBuilder('q')
            ->leftJoin('q.societe','soc');
        if( $this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {

        } elseif( $this->_user->hasRole(Utilisateur::ROLE_ADMIN) || $this->_user->hasRole(Utilisateur::ROLE_RISKMANAGER) || $this->_user->hasRole(Utilisateur::ROLE_AUDITEUR)) {
            $societe = $this->_user->getSociete();
            $queryBuilder->orWhere('soc.id = :societeId')->setParameter('societeId', $societe->getId());
        } elseif($this->_user->hasRole(Utilisateur::ROLE_RESPONSABLE)) {
            $structure = $this->_user->getStructure();
            $queryBuilder
                ->orWhere('q.lvl >= :lvl ')
                ->andWhere('q.root = :root ')
                ->andWhere('q.lft  >= :lft ')
                ->andWhere('q.rgt <= :rgt')
                ->setParameter('lvl', $structure->getLvl())
                ->setParameter('root', $structure->getRoot())
                ->setParameter('lft', $structure->getLft())
                ->setParameter('rgt', $structure->getRgt());
        }  else{
            $queryBuilder->andWhere('q.id=:n')->setParameter('n', -1);
        }

        return BaseRepository::filterBySociete($queryBuilder, 'q', $this->_user)->orderBy('q.name');
    }

    public function listUserStructure() {
        $societeNotAdmin=$this->_user->getSociete()->getId();
        $sociteUser=$this->_user->getSociete()->getIsAdmin();
        if($sociteUser) {
            $query = $this->createQueryBuilder('s');
        }
        else {
            $query = $this->createQueryBuilder('s')

                ->leftJoin('s.societe', 'so')
                ->where('so.id=:societeNotAdmin')->setParameter('societeNotAdmin', $societeNotAdmin);
        }

        return $query;
    }

    public function filterBySociete(QueryBuilder $queryBuilder, $alias = null) {
        if(!$alias) {
            $aliases = $queryBuilder->getRootAliases();
            $alias = $aliases[0];
        }
        if($this->_user->getSociete()) {
            $queryBuilder->andWhere($alias . '.societe = :societe')->setParameter('societe', $this->_user->getSociete());
        }
        return $queryBuilder;
    }
}