<?php

namespace App\Repository;

use App\Entity\DomaineActivite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

class DomaineActiviteRepository extends ServiceEntityRepository
{
    protected $_states;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param)
    {
        parent::__construct($registry, DomaineActivite::class);
        $this->_states	= $param->get('states');
    }

    public function listAllQueryBuilder($criteria = null) {
        return $this->createQueryBuilder('q')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime']);
    }
}
