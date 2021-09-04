<?php

namespace App\Repository;

use App\Entity\Evaluation;
use App\Entity\Menace;
use App\Entity\Risque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Menace|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menace|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menace[]    findAll()
 * @method Menace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenaceRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Menace::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    /**
     * @param integer $profilRisqueId
     * @return array
     */
    public function getMenaceByProfilRisqueId($profilRisqueId) {
        return $this->createQueryBuilder('m')
            ->select('m.id, m.libelle')
            ->innerJoin('m.profilRisque', 'pr')
            ->where('pr.id IN (:profilRisqueId)')
            ->andWhere('m.etat = :etat')
            ->setParameters(array('profilRisqueId' => $profilRisqueId, 'etat' => true))
            ->getQuery()->getArrayResult();
    }

    /**
     * @param integer $carto
     * @return array
     */
    public function menaceHasProfilRisque($carto) {
        $result = $this->createQueryBuilder('m')
            ->select("COUNT(m.id)")
            ->innerJoin('m.cartographie', 'c')
            ->where('c.id IN (:carto)')
            ->andWhere('m.etat = :etat')
            ->setParameters(array('carto' => $carto, 'etat' => true))
            ->getQuery()->getArrayResult();
        return count($result) > 0;
    }

    public function listAverableByPeriode($periode_id){
        $qb =  $this->createQueryBuilder('m');
        $qb -> leftJoin('m.menaceAvere', 'ma')
            -> leftJoin('ma.periode','p')
            -> where($qb->expr()->notIn('p.id', $periode_id))
            -> orWhere('p.id is null');
        return $qb;
    }

    /**
     *
     * @param Risque $criteria
     */
    public function getRisquesAveresByPeriode($criteria){
        $criteria = $criteria ? $criteria : new \App\Entity\Risque();
        $risqueRepo = $this->_em->getRepository(Risque::class);
        $evalBuilder = $this->_em->getRepository(Evaluation::class)
            ->createQueryBuilder('ev')
            ->innerJoin('ev.risque','risk')
            ->select('MAX(ev.id) ')
            ->groupBy('risk.id');
        $qb =  $this->createQueryBuilder('m');
        $qb -> innerJoin('m.menaceAvere', 'ma')
            -> innerJoin('ma.periode','per')
            -> leftJoin('m.risque','r')
            ->leftJoin('r.identification', 'i')
            ->leftJoin('r.risqueMetier', 'rm')
            ->leftJoin('rm.activite', 'a')
            ->leftJoin('r.risqueProjet', 'rp')
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
            ->leftJoin('r.criticite', 'c')
            ->leftJoin('r.cartographie', 'cg')
            ->leftJoin('r.maturiteTheorique','mt')
            ->leftJoin('r.evaluation', 'e')
            ->leftJoin('e.impactOfEvaluation','iOe')
            ->leftJoin('iOe.grille','g')
            ->leftJoin('g.note','n')
            ->leftJoin('iOe.impact','imp')
            ->leftJoin('imp.critere','crit')
            ->leftJoin('crit.domaine','dom')
            ->select('per.id periode_id, per.dateDebut debut, per.dateFin fin,m.libelle menace, m.id id, ROUND(AVG(n.valeur)) gravite, ROUND(AVG(r.probabilite)) probabilite, MAX(mt.valeur) maturite, (ROUND(AVG(n.valeur))*ROUND(AVG(r.probabilite))) criticite  ')
            ->andWhere($qb->expr()->in('e.id', $evalBuilder->getDQL()))
            ->orWhere('e.id is null')
            ->andWhere('r.etat = :etat')->setParameter('etat', $this->_states['risque']['valide'])
        ;
        $qb = $risqueRepo->filterBuilder($qb,$criteria);
        $qb->groupBy('per.id')->addGroupBy('m.id');
        if(count($criteria->maturiteForKpi)>0){
            $valeurs_maturite_criteria = array();
            foreach ($criteria->maturiteForKpi as $key =>$value)
                $valeurs_maturite_criteria [] = intval($value->getValeur());
            $qb->having('maturite IN (:maturites)')->setParameter('maturites', $valeurs_maturite_criteria);
        }
        if(count($criteria->criticiteForKpi)>0){
            $valeurs_criticite_criteria = array();
            foreach ($criteria->criticiteForKpi as $key =>$value)
                for ($i=$value->getVmin(); $i<=$value->getVmax();$i++)
                    $valeurs_criticite_criteria [] = $i;
            $qb->andHaving('criticite IN (:criticites)')->setParameter('criticites', $valeurs_criticite_criteria);
        }
        if(count($criteria->graviteForKpi)>0){
            $qb->andHaving('gravite IN (:gravites)')->setParameter('gravites', $criteria->graviteForKpi);
        }
        if(count($criteria->probaForKpi)>0){
            $qb->andHaving('probabilite IN (:probabilites)')->setParameter('probabilites', $criteria->probaForKpi);
        }
        return $qb;
    }
    /**
     * @param unknown $periode_id
     */
    public function listAvereByPeriode($periode_id){
        $qb =  $this->createQueryBuilder('m');
        $qb 	-> leftJoin('m.menaceAvere', 'ma')
            -> leftJoin('ma.periode','p')
            -> where($qb->expr()->in('p.id', $periode_id))
        ;
        return $qb;
    }

    public function listAllQueryBuilder($criteria = null) {
        return $this->createQueryBuilder('q')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime']);
    }
}
