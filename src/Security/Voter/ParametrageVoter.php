<?php 

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ParametrageVoter extends Voter {
	const CREATE 	  	 = 'create';
	const READ 	 		 = 'read';
	const UPDATE 	 	 = 'update';
	const DELETE	 	 = 'delete';
	
	protected function supports($attribute, $entity): bool {
        return in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE])
			&& ($entity instanceof \App\Entity\Cause || $entity instanceof \App\Entity\Critere);
    }

    protected function voteOnAttribute($attribute, $entity, TokenInterface $token): bool {
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
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE']);
				break;
			case self::READ:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER']);
				break;
			case self::UPDATE:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER']);
				break;
			case self::DELETE:
				break;
			default:
				break;
		}
		return false;
	}
}