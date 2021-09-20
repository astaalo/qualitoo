<?php

namespace App\Repository;

use App\Entity\Societe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Societe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Societe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Societe[]    findAll()
 * @method Societe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocieteRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Societe::class);
        //$this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }


    public function listUserSocieties() {
        $idUser=$this->_user->getId();
        $sociteUser=$this->_user->getSociete()->getIsAdmin();
        if($sociteUser) {
            $query = $this->createQueryBuilder('s')
                ->leftJoin('s.administrateur', 'a');
        }
        else {
            $query = $this->createQueryBuilder('s')
                ->leftJoin('s.administrateur', 'a')
                ->where('a.id=:user_id')->setParameter('user_id', $idUser);
        }

        return $query;
    }

    public function listAll() {
        return $this->listAllQueryBuilder()
            ->getQuery()
            ->execute();
    }

    public function listAllQueryBuilder($criteria = null) {
        return $this->createQueryBuilder('q')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime']);
    }
}
