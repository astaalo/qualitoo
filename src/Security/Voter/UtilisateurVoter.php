<?php

namespace App\Security\Voter;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UtilisateurVoter extends Voter {
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
        } elseif($user->hasRole('ROLE_SUPER_ADMIN')) {
			return true;
		}

        // ... (check conditions and return true to grant permission) ...
		if (in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE, self::ACTIVATE, self::DESACTIVATE])) {
			return $user->hasRole('ROLE_ADMIN');
		}
		return false;
    }
}
