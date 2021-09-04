<?php

namespace App\Repository;

use App\Entity\Grille;
use App\Entity\RisqueHasCause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @method Grille|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grille|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grille[]    findAll()
 * @method Grille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrilleRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Grille::class);
        $this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

    /**
     * Get active grille
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listActiveQueryBuilder() {
        return $this->createQueryBuilder ( 'g' )->innerJoin ( 'g.typeGrille', 'tg' )->innerJoin ( 'tg.entite', 'e' )->where ( 'e.id = :entite_id' )->andWhere ( 'tg.etat = :etat' )->setParameters ( array (
            'entite_id' => $this->_user->getEntite ()->getId (),
            'etat' => true
        ) );
    }
    public function listByCritere($id) {
        return $this->createQueryBuilder ( 'g' )->select ( 'g.id, g.libelle' )->innerJoin ( 'g.grilleImpact', 'gi' )->innerJoin ( 'gi.critere', 'c' )->where ( 'c.id = :id' )->setParameter ( 'id', $id )->getQuery ()->getArrayResult ();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getGrilleByCartoForMaturiteSSTE($causeOfRisqueId) {
        /** @var RisqueHasCause $causeOfRisque */
        $causeOfRisque = $this->_em->getRepository(RisqueHasCause::class)->find($causeOfRisqueId);
        $qb = $this->createQueryBuilder ( 'g' )
            ->innerJoin ( 'g.typeGrille', 'tg' )
            ->where ( 'IDENTITY(tg.cartographie) = :carto' )->setParameter ( 'carto', $causeOfRisque->getRisque ()->getCartographie ()->getId () )
            ->andWhere('tg.typeEvaluation=:typeEv')->setParameter ( 'typeEv', $this->_ids ['type_evaluation'] ['maturite'] );
        if ($causeOfRisque->getRisque () && $causeOfRisque->getRisque ()->isPhysical () && $causeOfRisque->getRisque ()->getCartographie ()->getId () == $this->_ids ['carto'] ['environnement'])
            $qb->andWhere ( "IDENTITY(tg.modeFonctionnement) = :modeFonct" )->setParameter ( 'modeFonct', $causeOfRisque->getModeFonctionnement ()->getId () );
        return $qb;
    }
}
