<?php

namespace App\Repository;

use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Site|null find($id, $lockMode = null, $lockVersion = null)
 * @method Site|null findOneBy(array $criteria, array $orderBy = null)
 * @method Site[]    findAll()
 * @method Site[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Site::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    /**
     * @param Site $site
     * @return QueryBuilder
     */
    public function listAllQueryBuilder($site = null) {
        $societe_id = $this->_user->getStructure()->getSociete()->getId();
        $query = $this->createQueryBuilder('s')
            ->where('IDENTITY(s.societe)=:societe_id')->setParameter('societe_id', $societe_id);
        return $query;
    }


    public function filter() {
        $queryBuilder = $this->createQueryBuilder('q')
            ->leftJoin('q.societe','soc');
        return BaseRepository::filterBySociete($queryBuilder, 'q', $this->_user);
    }
}
