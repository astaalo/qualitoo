<?php 
namespace App\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ParametrageVoter extends AbstractVoter {
	const CREATE 	  	 = 'create';
	const READ 	 		 = 'read';
	const UPDATE 	 	 = 'update';
	const DELETE	 	 = 'delete';
	
	private $em;
	
	protected $container;
	
	public function __construct(EntityManager $em, ContainerInterface $container) {
		$this->em = $em;
		$this->container = $container;
	}
	
	protected function getSupportedAttributes() {
		return array(self::CREATE, self::READ, self::UPDATE, self::DELETE);
	}
	
	protected function getSupportedClasses() {
		return array( 
					 'App\Entity\Cause',
					 'App\Entity\Critere',
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
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_RESPONSABLE')) {
					return true;
				}
			break;
			case self::READ:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER')) {
					return true;
				}
			break;
			case self::UPDATE:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER')) {
					return true;
				}
			break;
			
			case self::DELETE:
			break;
		}
		return false;
	}
	
}