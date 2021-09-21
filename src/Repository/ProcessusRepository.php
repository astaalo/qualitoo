<?php

namespace App\Repository;

use App\Entity\Processus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Processus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Processus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Processus[]    findAll()
 * @method Processus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessusRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Processus::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    public function listAllProcessus() {
        return $this->createQueryBuilder('q')
            ->add('where', 'q.parent is NULL')
            ->getQuery()
            ->execute();
    }

    public function findSousProcessus($id) {
        return $this->createQueryBuilder('q')
            ->add('where', 'q.parent = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    public function getIdsChilrenAndMeBuilder($processus_id) {
        $queryBuilder = $this->createQueryBuilder('q')
            ->select('q.id')
            ->innerJoin('App\Entity\Processus', 'p', 'WITH', 'q.root = p.root')
            ->where('p.lft <= q.lft AND p.rgt >= q.rgt AND p.lvl <= q.lvl');
        if($processus_id) {
            $queryBuilder->andWhere('p.id = :processus_id');
        } else {
            $queryBuilder->where('p.parent IS NULL');
        }
        return $queryBuilder;
    }

    public function listByParentQueryBuilder($entite_id, $processus_id) {
        $queryBuilder = $this->createQueryBuilder('q')
            ->leftJoin('q.parent', 'p')
            ->innerJoin('q.structure', 's')
            ->innerJoin('s.entite', 'e')
            ->where('e.id = :entite_id')
            ->setParameter('entite_id', $entite_id);
        if($processus_id) {
            $queryBuilder->andWhere('p.id = :processus_id')->setParameter('processus_id', $processus_id);
        } else {
            $queryBuilder->andWhere('p IS NULL');
        }
        return $queryBuilder;
    }

    /**
     * @param \App\Entity\Processus $processus
     * @return QueryBuilder
     */
    public function listAll($processus = null) {
        $queryBuilder = $this->createQueryBuilder('p')
            ->innerJoin('p.structure', 'q')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime']);
        if($processus && $processus->getTypeProcessus()) {
            $queryBuilder->andWhere('p.typeProcessus = :typeProcessus')->setParameter('typeProcessus', $processus->getTypeProcessus());
        }
        return $queryBuilder;
    }

    public function findByStructureId($structure_id) {
        return $this->createQueryBuilder('q')
            ->select('q.id, q.libelle, tp.id type')
            ->innerJoin('q.structure', 's')
            ->innerJoin('q.typeProcessus', 'tp')
            ->innerJoin('App\Entity\Structure', 'r')
            ->where('r.id = :structure_id')
            ->andWhere('r.root = s.root AND r.lvl <= s.lvl AND r.lft <= s.lft AND r.rgt >= s.rgt')
            ->setParameter('structure_id', $structure_id)->orderBy('q.libelle')
            ->getQuery()->getArrayResult();
    }

    public function findMacroByDirection($directionId){
        return $this->createQueryBuilder('q')
            ->select('q.id, q.libelle')
            ->innerJoin('q.structure', 's')
            ->where('s.id = :directionId')
            ->setParameter('directionId', $directionId)
            ->andWhere('q.lvl=0')
            ->orderBy('q.libelle')
            ->getQuery()->getArrayResult();

    }

}