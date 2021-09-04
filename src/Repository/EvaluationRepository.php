<?php

namespace App\Repository;

use App\Entity\Evaluation;
use App\Entity\Risque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Evaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluation[]    findAll()
 * @method Evaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationRepository extends ServiceEntityRepository
{
    //protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Evaluation::class);
        //$this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    /**
     * @param Evaluation $criteria
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listQueryBuilder($criteria) {
        $criteria = $criteria ? $criteria : new Evaluation();
        $queryBuilder = $this->createQueryBuilder('e')
            ->innerJoin('e.risque', 'r')
            ->innerJoin('r.menace', 'm')
            ->innerJoin('e.criticite', 'c')
            ->where('r.etat = :etat')
            ->andWhere('r.societe = :societe')
            ->setParameter('societe', $this->_user->getSociete())
            ->setParameter('etat', $this->_states['risque']['valide']);
        if($criteria->menace) {
            $queryBuilder->andWhere('r.menace = :menace')->setParameter('menace', $criteria->menace);
        }
        return BaseRepository::filterBySociete($queryBuilder, 'r', $this->_user);
    }

    /**
     * @param Risque $risque
     */
    public function getlastEvaluation(Risque $risque){
        $queryBuilder = $this->createQueryBuilder('e')
            ->innerJoin('e.risque', 'r')
            ->where('r=:risque')->setParameter('risque', $risque)
            ->orderBy('e.dateEvaluation', 'DESC');
        return $queryBuilder
            ;
    }
}
