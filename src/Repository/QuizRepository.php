<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Risque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Quiz::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    /**
     * @param Risque $criteria
     */
    public function getRisquesTesterByYear($criteria){
        $risqueRepo = $this->_em->getRepository(Risque::class);
        $criteria = $criteria ? $criteria : new \App\Entity\Risque();
        $queryBuilder=$this ->createQueryBuilder('q');
        $queryBuilder		->innerJoin('q.controle','ct')
            ->innerJoin('ct.causeOfRisque','cOr')
            ->innerJoin('cOr.risque','r')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.processus', 'pm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
            ->leftJoin('rp.processus', 'pp')
            ->leftJoin('rp.projet', 'p')
            ->leftJoin('r.risqueEnvironnemental', 're')
            ->leftJoin('re.equipement', 'eqE')
            ->leftJoin('re.domaineActivite', 'actE')
            ->leftJoin('r.risqueSST', 'rs')
            ->leftJoin('rs.equipement', 'eqS')
            ->leftJoin('rs.domaineActivite', 'actS')
            ->leftJoin('r.utilisateur', 'u')
            ->leftJoin('rm.structure', 'stM', 'WITH', 'stM.lvl != 0')
            ->leftJoin('rp.structure', 'stP', 'WITH', 'stP.lvl != 0')
            ->leftJoin('rm.structure', 'dirM', 'WITH', 'dirM.lvl = 0')
            ->leftJoin('rp.structure', 'dirP', 'WITH', 'dirP.lvl = 0')
            ->leftJoin('re.site', 'sitE')
            ->leftJoin('rs.site', 'sitS')
            ->leftJoin('r.menace', 'm')
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('r.cartographie', 'cg')
            ->leftJoin('r.societe', 's')
            ->andWhere('s = :societe')->setParameter('societe', $this->_user->getSociete())
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide'])
            ->select ($queryBuilder->expr()->countDistinct('r.id').'nb')
            ->addSelect('YEAR(q.dateEvaluation) annee')
        ;
        $queryBuilder = $risqueRepo->filterBuilder($queryBuilder,$criteria);
        return $queryBuilder->groupBy('annee');
    }
}
