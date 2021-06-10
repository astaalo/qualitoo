<?php

namespace App\Repository;

use App\Entity\Activite;
use App\Entity\Processus;
use App\Entity\Structure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Activite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActiviteRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Activite::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    public function listByProcessusQueryBuilder($entite_id, $processus_id) {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->innerJoin('a.processus', 'c')
            ->innerJoin('c.structure', 's')
            ->innerJoin('s.entite', 'e')
            ->where('e.id = :entite_id')
            ->andWhere($queryBuilder->expr()->in('c.id',
                $queryBuilder->getEntityManager()->getRepository(Processus::class)->getIdsChilrenAndMeBuilder($processus_id)->getQuery()->getDQL()
            ))->setParameter('entite_id', $entite_id);
        if($processus_id) {
            $queryBuilder->setParameter('processus_id', $processus_id);
        }
        return $queryBuilder;
    }

    /**
     * @param \App\Entity\Activite $activite
     * @return QueryBuilder
     */
    public function listAllQueryBuilder($activite = null) {
        $queryBuilder = $this->createQueryBuilder('a')
            ->innerJoin('a.processus', 'p')
            ->innerJoin('p.structure', 'q')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime']);

        if($activite && $activite->getProcessus() && $activite->getProcessus()->getStructure()) {
            $structure = $activite->getProcessus()->getStructure();
            $queryBuilder->andWhere('q.lvl >= :level')->setParameter('level', $structure->getLvl())
                ->andWhere('q.root = :root')->setParameter('root', $structure->getRoot())
                ->andWhere('q.lft >= :lft')->setParameter('lft', $structure->getLft())
                ->andWhere('q.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
        }
        if($activite && $activite->getProcessus() && $activite->getProcessus()->getLibelle()) {
            $queryBuilder->andWhere('q.libelle LIKE :libelle')
                ->setParameter('libelle', '%'.$activite->getProcessus()->getLibelle().'%');
        }
        return BaseRepository::filterBySociete($queryBuilder, 'q', $this->_user);
    }

    /* (non-PHPdoc)
     * @see \Doctrine\ORM\EntityRepository::findAll()
     */
    public function findAll() {
        $queryBuilder = $this->createQueryBuilder('a')
            ->innerJoin('a.processus', 'p')
            ->innerJoin('p.structure', 's');
        return BaseRepository::filterBySociete($queryBuilder, 's')->getQuery()->execute();
    }

    /**
     * @param \App\Entity\Processus $processus
     * @return integer
     */
    public function getLastNumero($processus) {
        $data = $this->createQueryBuilder('r')
            ->select('MAX(r.numero) as number')
            ->where('r.processus = :processus')
            ->setParameter('processus', $processus)
            ->getQuery()->getOneOrNullResult();
        return $data['number'];
    }

    public function findByProcessusId($processus_id) {
        return $this->createQueryBuilder('q')
            ->select('q.id, q.libelle')
            ->innerJoin('q.processus', 'p')
            ->innerJoin(Processus::class, 'r')
            ->where('r.id = :processus_id')
            ->andWhere('r.root = p.root AND r.lvl <= p.lvl AND r.lft <= p.lft AND r.rgt >= p.rgt')
            ->setParameter('processus_id', $processus_id)->orderBy('q.libelle')
            ->getQuery()->getArrayResult();
    }

    public function findByStructureId($structure_id) {
        return $this->createQueryBuilder('q')
            ->select('q.id, q.libelle')
            ->innerJoin('q.processus', 'p')
            ->innerJoin('p.structure', 's')
            ->innerJoin(Structure::class, 'r')
            ->where('r.id = :structure_id')
            ->andWhere('r.root = s.root AND r.lvl <= s.lvl AND r.lft <= s.lft AND r.rgt >= s.rgt')
            ->setParameter('structure_id', $structure_id)->orderBy('q.libelle')
            ->getQuery()->getArrayResult();
    }

    public function restitutionBuilder($criteria) {
        $criteria = $criteria ? $criteria : new \App\Entity\Activite();
        $queryBuilder = $this->createQueryBuilder('a')
            ->innerJoin('a.processus', 'p')
            ->innerJoin('p.structure', 's')
            ->where('s.societe = :societe')
            ->setParameter('societe', $this->_user->getSociete());
        if($criteria->structure) {
            $queryBuilder->innerJoin(Structure::class, 'ps')
                ->andWhere('ps = :structure')->setParameter('structure', $criteria->structure)
                ->andWhere('ps.root = s.root AND ps.lvl <= s.lvl AND ps.lft <= s.lft AND ps.rgt >= s.rgt');
        }
        if($criteria->getProcessus()) {
            $queryBuilder->innerJoin(Processus::class, 'pp')
                ->andWhere('pp = :processus')->setParameter('processus', $criteria->getProcessus())
                ->andWhere('pp.root = p.root AND pp.lvl <= p.lvl AND pp.lft <= p.lft AND pp.rgt >= p.rgt');
        }
        return $queryBuilder->groupBy('a.id');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return array
     */
    public function getMatrice($queryBuilder) {
        $data = array();
        $result = $queryBuilder->getQuery()->getResult();
        foreach($result as $activite) {
            $data[] = array('libelle'=>sprintf($activite), 'probabilite'=>$activite->getProbabilite(), 'gravite'=>$activite->getGravite(), 'icg'=>$activite->getICG());
        }
        return $data;
    }

    public function findStructureBy($structure_id,$libelleSansCs) {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.processus', 'p')
            ->leftJoin('p.structure', 's')
            ->where('q.libelleSansCarSpecial = :libelle')
            ->setParameter('libelle',$libelleSansCs)
            ->andWhere('s.id = :structure_id')
            ->setParameter('structure_id', $structure_id )
            ->getQuery()->getResult();
    }

    public function findStructureByEtat($structure_id,$libelleSansCs) {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.processus', 'p')
            ->leftJoin('p.structure', 's')
            ->where('q.etat =1 OR q.etat is NULL  ')
            ->andWhere('q.libelleSansCarSpecial = :libelle')
            ->setParameter('libelle',$libelleSansCs)
            ->andWhere('s.id = :structure_id')
            ->setParameter('structure_id', $structure_id )
            ->getQuery()->getResult();
    }
}
