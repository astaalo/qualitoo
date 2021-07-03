<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RisqueVoter extends Voter {
	const CREATE 	  			= 'create';
	const READ 	 		 		= 'read';
	const REJET                 = 'rejet';
	const UPDATE 	 	 		= 'update';
	const DELETE	 	 		= 'delete';
	const EXPORT_RISQUE  		= 'export_rhc';
	const VALIDATE       		= 'validate';
	const MATRICE       	    = 'matrice';
	const ACCESS_ONE_RISQUE     = 'accesOneRisque';
	
	protected function supports($attribute, $risque): bool {
        return in_array($attribute, [self::CREATE, self::READ, self::REJET, self::UPDATE, self::DELETE,
			self::EXPORT_RISQUE, self::VALIDATE, self::MATRICE, self::ACCESS_ONE_RISQUE])
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
			case self::CREATE:
				return 
					($user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE']))
					|| ($user->hasRole('ROLE_RESPONSABLE_ONLY') && $user->getSite()->count()<=0 && $risque->isPhysical()==false)
					|| ($user->hasRole('ROLE_RESPONSABLE_ONLY') && !$user->isManager() && $risque->isPhysical()==true);
					break;
			case self::READ:
				return $user->hasRoles(array('ROLE_PORTEUR'));
				break;
			case self::UPDATE:
				return
					($user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_CHEFPROJET', 'ROLE_RESPONSABLE']))
					|| ($user->hasRole('ROLE_RESPONSABLE_ONLY') && $user->getSite()->count()<=0 && $risque->isPhysical() && $this->isYourRisque($risque))
					|| ($user->hasRole('ROLE_RESPONSABLE_ONLY') && !$user->isManager() && $risque->isPhysical() && $this->isYourRisque($risque));
				break;
			case self::DELETE:
				return $user->hasRoles(array('ROLE_ADMIN', 'ROLE_RISKMANAGER'));
				break;
			case self::REJET:
				return
					($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER') || $user->hasRole('ROLE_AUDITEUR'));
				break;
			case self::EXPORT_RISQUE:
				return
					($user->hasRoles(array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE_ONLY', 'ROLE_CHEFPROJET')));
				break;
			case self::VALIDATE:
				return ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_RISKMANAGER'));
				break;
			case self::MATRICE:
				return
					($user->hasRoles(array('ROLE_ADMIN', 'ROLE_AUDITEUR', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET')));
				break;
			case self::ACCESS_ONE_RISQUE:
				return $this->isYourRisque($risque);
				break;
			default:
				return false;
		}
		return false;
    }
	
	/**
	 * @param Risque $entity
	 */
	public function isYourRisque($risque) {
		$repo = $this->em->getRepository(Risque::class);
		$qb = $repo ->getAllRisquesByUser();
		$qb->andWhere('r.id =:id ')->setParameter('id', $risque->getId());
		return count($qb->getQuery()->getArrayResult())>0;
	}
}