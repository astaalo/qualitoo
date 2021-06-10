<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    /*public function findAll() {
        return $this->findBy(array(), array('position' => 'ASC', 'id' => 'ASC'));
    }*/
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Question::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    public function listAll($criteria = null) {
        return $this->createQueryBuilder('q')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime'])
            ->orderBy('q.position', 'ASC');
    }

    public function listAllQueryBuilder($criteria = null) {
        return $this->createQueryBuilder('q')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime']);
    }
}
