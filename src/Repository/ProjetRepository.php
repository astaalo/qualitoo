<?php

namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Projet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projet[]    findAll()
 * @method Projet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjetRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Projet::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }


    public function findByProcessusId($processus_id) {
        return $this->createQueryBuilder('q')
            ->select('q.id, q.libelle')
            ->innerJoin('q.processus', 'p')
            ->innerJoin('App\Entity\Processus', 'r')
            ->where('r.id = :processus_id')
            ->andWhere('r.root = p.root AND r.lvl <= p.lvl AND r.lft <= p.lft AND r.rgt >= p.rgt')
            ->setParameter('processus_id', $processus_id)
            ->getQuery()->getArrayResult();
    }

    public function findByProcessus($id) {
        return $this->createQueryBuilder('q')
            ->innerJoin('q.processus', 'p')
            ->innerJoin('App\Entity\Processus', 'r')
            ->where('r.id = :processus_id')
            ->andWhere('r.root = p.root AND r.lvl <= p.lvl AND r.lft <= p.lft AND r.rgt >= p.rgt')
            ->setParameter('processus_id', $id)
            ->getQuery()->execute();
    }

    public function findByStructureId($structure_id) {
        return $this->createQueryBuilder('q')
            ->select('q.id, q.libelle')
            ->innerJoin('q.processus', 'p')
            ->innerJoin('p.structure', 's')
            ->innerJoin('App\Entity\Structure', 'r')
            ->where('r.id = :structure_id')
            ->andWhere('r.root = s.root AND r.lvl <= s.lvl AND r.lft <= s.lft AND r.rgt >= s.rgt')
            ->setParameter('structure_id', $structure_id)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param Projet $processus
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

    /**
     * @param array $criteria
     * @return QueryBuilder
     */
    public function listAllQueryBuilder($criteria = null) {
        $criteria = $criteria ? $criteria : new Projet();
        $societe_id=$this->_user->getSociete()->getId();
        $queryBuilder = $this->createQueryBuilder('p')->where('IDENTITY(p.societe)=:societe_id')->setParameter('societe_id', $societe_id);
        if($criteria->getUtilisateur()) {
            $queryBuilder->innerJoin('p.utilisateur', 'u')
                ->andWhere('u.id = :user')->setParameter('user', $criteria->getUtilisateur()->getId());
        }
        if($criteria->getProcessus()) {
            $queryBuilder->innerJoin('App\Entity\Processus', 'pp')
                ->andWhere('pp = :processus')->setParameter('processus', $criteria->getProcessus());
        }
        return $queryBuilder->groupBy('p.id');
    }
}
