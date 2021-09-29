<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\TypeDocument;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;


class DocumentRepository extends ServiceEntityRepository
{
    protected $_ids;
    protected $_states;
    protected $_user;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $param, Security $security)
    {
        parent::__construct($registry, Document::class);
        //$this->_ids		= $param->get('ids');
        $this->_states	= $param->get('states');
        $this->_user	= $security->getUser();
    }

     /**
     * @param \App\Entity\Document $document
     * @return QueryBuilder
     */
    public function listAll($document = null) {
        $queryBuilder = $this->createQueryBuilder('p')
            ->innerJoin('p.profil', 'q');
            //->where('q.etat != :etat')
            //->setParameter('etat', $this->_states['entity']['supprime']);
        if($document && $document->getTypeDocument()) {
            $queryBuilder->andWhere('p.TypeDocument = :TypeDocument')->setParameter('TypeDocument', $document->getTypeDocument());
        }
        return $queryBuilder;
    }

    /**
     *
     * @param Document $criteria
     * @param TypeDocument $type
     */
    public function getDocumentsByType($criteria){
        $criteria =$criteria ? $criteria : new Document();
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.typeDocument', 'td')
            ->leftJoin('d.utilisateursAutorises','ua')
            ->leftJoin('ua.utilisateur','u');

        if($criteria->getTypeDocument()){
            $qb -> andWhere('td =:type')->setParameter('type',$criteria->getTypeDocument());
        }
      /*  if(!$this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN) && !$this->_user->hasRole(Utilisateur::ROLE_ADMIN) && !$this->_user->hasRole(Utilisateur::ROLE_USER)){
            $roles = $this->_user->takeRoles();
            foreach ($roles as $key=> $role)
                $qb -> andWhere('d.profils like :role')->setParameter('role', '%'.$role.'%');
        }*/
        if($criteria->getLibelle()){
            $qb -> andWhere('d.libelle like :libelle')->setParameter('libelle','%'.$criteria->getLibelle().'%');
        }
       // $qb->andWhere('u.id is null or u.id =:user_id')->setParameter('user_id', $this->_user->getId());
        $qb->andWhere('d.deleted= false');
        return $qb;
    }
}
