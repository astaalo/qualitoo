<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UtilisateurVoter extends Voter
{
    const CREATE 	  	 = 'create';
	const READ 	 		 = 'read';
	const UPDATE 	 	 = 'update';
	const DELETE	 	 = 'delete';
	const ACTIVATE       = 'activate';
	const DESACTIVATE    = 'desactivate';

    protected function supports($attribute, $user): bool
    {
        return in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE, self::ACTIVATE, self::DESACTIVATE])
            && $user instanceof \App\Entity\Utilisateur;
    }

    protected function voteOnAttribute($attribute, $user, TokenInterface $token): bool
    {
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch($attribute) {
			case self::CREATE:
				return $user->hasRole('ROLE_ADMIN');
				break;
			case self::READ:
				return $user->hasRole('ROLE_ADMIN');
				break;
			case self::UPDATE:
				return $user->hasRole(Utilisateur::ROLE_ADMIN);
				break;
			case self::DELETE:
				break;
			case self::ACTIVATE:
				return $user->hasRole('ROLE_ADMIN');
				break;
			case self::DESACTIVATE:
				return $user->hasRole('ROLE_ADMIN');
				break;
			default:
				return false;
				break;
		}
        return false;
    }
}
