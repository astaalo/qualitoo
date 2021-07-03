<?php 

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class KpiVoter extends Voter {
	const RCC 	  	 = 'rcc';
	const RRC 	 	 = 'rrc';
	const RT 	 	 = 'rt';
	const D_RT	 	 = 'drt';
	const CMC 	 	 = 'cmc';
	const TPRC 	 	 = 'tprc';
	const RAV 	 	 = 'rav';
	const EICG 	 	 = 'eicg';
	const EXPORT_KPI = 'export_kpi';
	
	protected function supports($attribute, $risque): bool {
        return in_array($attribute, [self::RCC, self::RRC, self::RT, self::D_RT,self::CMC,self::TPRC,self::RAV, self::EICG,self::EXPORT_KPI])
			&& $risque instanceof \App\Entity\Risque;
    }

    protected function voteOnAttribute($attribute, $risque, TokenInterface $token): bool {
		$user = $token->getUser();
		// if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        } elseif($user->hasRole('ROLE_SUPER_ADMIN')) {
			return true;
		}
        // ... (check conditions and return true to grant permission) ...
		switch($attribute) {
			case self::RCC:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE']);
				break;
			case self::RRC:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE']);
				break;
			case self::RT:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER']);
				break;
			case self::D_RT:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE']);
				break;
			case self::CMC:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE']);
				break;
			case self::TPRC:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE']);
				break;
			case self::RAV:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER']);
				break;
			case self::EICG:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE']);
				break;
			case self::EXPORT_KPI:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE']);
				break;
			default:
				break;
		}
		return false;
	}
	
}