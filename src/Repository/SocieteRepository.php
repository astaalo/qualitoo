<?php

namespace App\Repository;

use App\Entity\Activite;
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
    protected $_user;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Societe::class);
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
}
