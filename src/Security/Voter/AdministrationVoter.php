<?php 
namespace App\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;


class AdministrationVoter extends AbstractVoter {
	
	const CREATE 	  	     = 'create';
	const READ 	 		     = 'read';
	const UPDATE 	 	     = 'update';
	const DELETE	 	     = 'delete';
	const ACTIVATE 	 	     = 'activate';
	const DESACTIVATE	     = 'desactivate';
	const CHANGE_POSITION	 = 'changePosition';
	
	
	private $em;
	
	protected $container;
	
	public function __construct(EntityManager $em, ContainerInterface $container) {
		$this->em = $em;
		$this->container = $container;
	}
	
	protected function getSupportedAttributes() {
		return array(self::CREATE, self::READ, self::UPDATE, self::DELETE, self::ACTIVATE, self::DESACTIVATE, self::CHANGE_POSITION);
	}
	
	protected function getSupportedClasses() {
		return array('App\Entity\Processus', 
					  'App\Entity\Activite',
					  'App\Entity\Projet',
					 'App\Entity\Equipement',
					 'App\Entity\Structure',
					 'App\Entity\Site',
					 'App\Entity\Menace',
					 'App\Entity\DomaineActivite',
					 'App\Entity\DomaineImpact',
					 'App\Entity\Domainesite',
					 'App\Entity\Question',
					);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter::isGranted()
	 */
	protected function isGranted($attribute, $entity, $user = null) {
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
				if ($user->hasRole('ROLE_ADMIN') ) {
					return true;
				}
			break;
			case self::READ:
				if ($user->hasRole('ROLE_ADMIN')) {
					return true;
				}
			break;
			
			case self::UPDATE:
				if ($user->hasRole('ROLE_ADMIN')) {
					return true;
				}
			break;
			
			case self::DELETE:
				if ($user->hasRole('ROLE_ADMIN')) {
					return true;
				}
				break;
			
			case self::ACTIVATE:
				if ($user->hasRole('ROLE_ADMIN')) {
					return true;
				}
				break;
					
			case self::DESACTIVATE:
				if ($user->hasRole('ROLE_ADMIN')) {
					return true;
				}
				break;
			
		case self::CHANGE_POSITION:
			if ($user->hasRole('ROLE_ADMIN')) {
				return true;
			}
			break;
		}
		return false;
	}
	
}