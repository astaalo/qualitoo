<?php 

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AdministrationVoter extends Voter {
	
	const CREATE 	  	     = 'create';
	const READ 	 		     = 'read';
	const UPDATE 	 	     = 'update';
	const DELETE	 	     = 'delete';
	const ACTIVATE 	 	     = 'activate';
	const DESACTIVATE	     = 'desactivate';
	const CHANGE_POSITION	 = 'changePosition';
	
	protected function supports($attribute, $entity): bool {
        return in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE, self::ACTIVATE, self::DESACTIVATE, self::CHANGE_POSITION])
			&& ($entity instanceof \App\Entity\Processus || $entity instanceof \App\Entity\Activite || 
				$entity instanceof \App\Entity\Projet || $entity instanceof \App\Entity\Equipement || 
				$entity instanceof \App\Entity\Structure || $entity instanceof \App\Entity\Site || 
				$entity instanceof \App\Entity\Menace || $entity instanceof \App\Entity\DomaineActivite || 
				$entity instanceof \App\Entity\DomaineImpact || $entity instanceof \App\Entity\DomaineSite || 
				$entity instanceof \App\Entity\Question
			);
    }

    protected function voteOnAttribute($attribute, $entity, TokenInterface $token): bool {
		$user = $token->getUser(); $output = false;
		// if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        } elseif($user->hasRole('ROLE_SUPER_ADMIN')) {
			return true;
		}
        // ... (check conditions and return true to grant permission) ...
		if (in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE, self::ACTIVATE, self::DESACTIVATE, self::CHANGE_POSITION])) {
			$output = $user->hasRole('ROLE_ADMIN');
		}
		return $output;
	}
}