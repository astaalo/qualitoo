<?php

namespace App\Repository;

use App\Entity\Equipement;
use App\Entity\Processus;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

class EquipementRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Equipement::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    /**
     * @param Equipement $equipement
     * @return QueryBuilder
     */
    public function listAll(Equipement $equipement = null) {
        $queryBuilder = $this->createQueryBuilder('e')
        ;
        return BaseRepository::filterBySociete($queryBuilder, 'e');
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
     * @param Processus $processus
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

    public function listAllQueryBuilder($criteria = null) {
        return $this->createQueryBuilder('q')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime']);
    }
}
