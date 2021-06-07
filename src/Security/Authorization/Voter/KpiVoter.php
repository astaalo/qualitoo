<?php 
namespace App\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;


class KpiVoter extends AbstractVoter {
	const RCC 	  	 = 'rcc';
	const RRC 	 	 = 'rrc';
	const RT 	 	 = 'rt';
	const D_RT	 	 = 'drt';
	const CMC 	 	 = 'cmc';
	const TPRC 	 	 = 'tprc';
	const RAV 	 	 = 'rav';
	const EICG 	 	 = 'eicg';
	const EXPORT_KPI = 'export_kpi';
	
	private $em;
	
	protected $container;
	
	public function __construct(EntityManager $em, ContainerInterface $container) {
		$this->em = $em;
		$this->container = $container;
	}
	
	protected function getSupportedAttributes() {
		return array(self::RCC, self::RRC, self::RT, self::D_RT,self::CMC,self::TPRC,self::RAV, self::EICG,self::EXPORT_KPI);
	}
	
	protected function getSupportedClasses() {
		return array('App\Entity\Risque');
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
			case self::RCC:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_RESPONSABLE')) {
					return true;
				}
			break;
			case self::RRC:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_RESPONSABLE')) {
					return true;
				}
			break;
			case self::RT:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER')) {
					return true;
				}
			break;
			
			case self::D_RT:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_RESPONSABLE')) {
					return true;
				}
				break;
			case self::CMC:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_RESPONSABLE')) {
					return true;
				}
				break;
			case self::TPRC:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_RESPONSABLE')) {
					return true;
				}
			break;
			
			case self::RAV:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER')) {
					return true;
				}
				break;
			case self::EICG:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_RESPONSABLE')) {
					return true;
				}
				break;
			case self::EXPORT_KPI:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_RESPONSABLE')) {
					return true;
				}
				break;
		}
		return false;
	}
	
}