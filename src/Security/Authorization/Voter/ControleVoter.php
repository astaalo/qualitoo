<?php 
namespace App\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Risque;
use App\Entity\Controle;


class ControleVoter extends AbstractVoter {
	
	const CREATE 	  	  = 'create';
	const READ 	 		  = 'read';
	const UPDATE 	 	  = 'update';
	const DELETE	 	  = 'delete';
	const EVALUER         = 'evaluer';
	const ACCESS_ONE_CTRL = 'accesOneCtrl';
	
	private $em;
	
	protected $container;
	
	public function __construct(EntityManager $em, ContainerInterface $container) {
		$this->em = $em;
		$this->container = $container;
	}
	
	protected function getSupportedAttributes() {
		return array(self::CREATE, self::READ, self::UPDATE, self::DELETE, self::EVALUER, self::ACCESS_ONE_CTRL);
	}
	
	protected function getSupportedClasses() {
		return array('App\Entity\Controle');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter::isGranted()
	 */
	protected function isGranted($attribute, $controle, $user = null) {
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
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_AUDITEUR') || $user->hasRole('ROLE_RESPONSABLE') || $this->isYourControle($controle)) {
					return true;
				}
			break;
			case self::EVALUER:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_AUDITEUR')) {
					return true;
				}
			break;
			
			case self::UPDATE:
				if (($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_AUDITEUR') || $user->hasRole('ROLE_RESPONSABLE')) || ($this->isYourControle($controle))) {
					return true;
				}
			break;
			
			case self::DELETE:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_AUDITEUR')) {
					return true;
				}
			break;
			case self::ACCESS_ONE_CTRL:
				if($this->isYourControle($controle)){
					return true;
				}
			break;
		}
		return false;
	}
	
	/**
	 * @param Controle $entity
	 */
	public function isYourControle($entity){
			$repo  = $this->em->getRepository('OrangeMainBundle:Controle');
			$qb    = $repo ->listAllQueryBuilder();
			$qb    ->andWhere('q.id =:id ')->setParameter('id', $entity->getId());
			return count($qb->getQuery()->getArrayResult())>0;
	}
	
}