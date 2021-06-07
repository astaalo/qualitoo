<?php 
namespace App\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Risque;


class RisqueVoter extends AbstractVoter {
	
	const CREATE 	  			= 'create';
	const READ 	 		 		= 'read';
	const REJET                 = 'rejet';
	const UPDATE 	 	 		= 'update';
	const DELETE	 	 		= 'delete';
	const EXPORT_RISQUE  		= 'export_rhc';
	const VALIDATE       		= 'validate';
	const MATRICE       	    = 'matrice';
	const ACCESS_ONE_RISQUE     = 'accesOneRisque';
	
	private $em;
	
	protected $container;
	
	public function __construct(EntityManager $em, ContainerInterface $container) {
		$this->em = $em;
		$this->container = $container;
	}
	
	protected function getSupportedAttributes() {
		return array(self::CREATE, self::READ, self::UPDATE, self::DELETE, self::EXPORT_RISQUE, self::VALIDATE, self::MATRICE, self::ACCESS_ONE_RISQUE, self::REJET);
	}
	
	protected function getSupportedClasses() {
		return array('App\Entity\Risque');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter::isGranted()
	 */
	protected function isGranted($attribute, $risque, $user = null) {
		$user = $this->container->get('security.context')->getToken()->getUser();
		if(!$user instanceof UserInterface) {
		} elseif($user->hasRole('ROLE_SUPER_ADMIN')) {
			return true;
		}
		if (!$user instanceof Utilisateur) {
			throw new \LogicException('The user is somehow not our User class!');
		}
		
		switch($attribute) {
			case self::CREATE:
				if($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_AUDITEUR') || $user->hasRole('ROLE_RESPONSABLE')) {
					return true;
				} elseif($user->hasRole('ROLE_RESPONSABLE_ONLY') && $user->getSite()->count()<=0 && $risque->isPhysical()==false) {
					return true;
				} elseif($user->hasRole('ROLE_RESPONSABLE_ONLY') && !$user->isManager() && $risque->isPhysical()==true) {
					return true;
				} else {
					return false;
			    }
			break;
			case self::READ:
				if($user->hasRoles(array('ROLE_PORTEUR'))) {
					return true;
				} else { return false; }
			break;
			
			case self::UPDATE:
				if($user->hasRoles(array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_CHEFPROJET', 'ROLE_RESPONSABLE'))) {
					return true;
				} elseif($user->hasRole('ROLE_RESPONSABLE_ONLY') && $user->getSite()->count()<=0 && $risque->isPhysical()==false && $this->isYourRisque($risque)) {
					return true;
				} elseif($user->hasRole('ROLE_RESPONSABLE_ONLY') && !$user->isManager() && $risque->isPhysical()==true && $this->isYourRisque($risque)) {
					return true;
				} else {
					return false;
				}
				break;
				
			case self::DELETE:
				if($user->hasRoles(array('ROLE_ADMIN', 'ROLE_RISKMANAGER'))) {
					return true;
				} else {
					return false;
				}
				break;
			
			case self::REJET:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_AUDITEUR')) {
					return true;
				}
			break;
			case self::EXPORT_RISQUE:
				if ($user->hasRoles(array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE_ONLY', 'ROLE_CHEFPROJET'))) {
					return true;
				}else{
					return false;
				}
			break;
			case self::VALIDATE:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') ) {
					return true;
				}
			break;
			
			case self::MATRICE:
				if ($user->hasRoles(array('ROLE_ADMIN', 'ROLE_AUDITEUR', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET'))) {
					return true;
				}
			break;
			case self::ACCESS_ONE_RISQUE:
				if($this->isYourRisque($risque)){
					return true;
				}
			break;
		}
		return false;
	}
	
	/**
	 * @param Risque $entity
	 */
	public function isYourRisque($entity) {
		$repo = $this->em->getRepository('OrangeMainBundle:Risque');
		$qb   = $repo ->getAllRisquesByUser();
		$qb->andWhere('r.id =:id ')->setParameter('id', $entity->getId());
		return count($qb->getQuery()->getArrayResult())>0;
	}
}