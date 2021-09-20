<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param)
    {
        parent::__construct($registry, Utilisateur::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
    }

    /**
     * @param \App\Entity\Utilisateur $user
     * @return QueryBuilder
     */
    public function listAll($user = null) {
        $this->_user = $user;
        $queryBuilder = $this->createQueryBuilder('q')
            ->innerJoin('q.profils', 'e');
            //->where('q.etat != :etat')
           // ->setParameter('etat', $this->_states['entity']['supprime']);
            if($this->_user->getSociete()) {
                $queryBuilder->andWhere('q.societe = :societe')->setParameter('societe', $this->_user->getSociete());
            }
            return $queryBuilder;
    }

    /* (non-PHPdoc)
     * @see \Doctrine\ORM\EntityRepository::findAll()
     */
    public function findAll() {
        $queryBuilder = $this->createQueryBuilder('q')
            ->innerJoin('q.structure', 's');
        return BaseRepository::filterBySociete($queryBuilder, 's')->getQuery()->execute();
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
