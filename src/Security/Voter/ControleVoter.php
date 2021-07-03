<?php 

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;


class ControleVoter extends Voter {
	
	const CREATE 	  	  = 'create';
	const READ 	 		  = 'read';
	const UPDATE 	 	  = 'update';
	const DELETE	 	  = 'delete';
	const EVALUER         = 'evaluer';
	const ACCESS_ONE_CTRL = 'accesOneCtrl';
	
	protected function supports($attribute, $controle): bool {
        return in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE, self::EVALUER, self::ACCESS_ONE_CTRL])
			&& $controle instanceof \App\Entity\Controle;
    }

    protected function voteOnAttribute($attribute, $controle, TokenInterface $token): bool {
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
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE']) || $this->isYourControle($controle);
				break;
			case self::EVALUER:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR']);
				break;
			case self::UPDATE:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE']) || $this->isYourControle($controle);
				break;
			case self::DELETE:
				return $user->hasRoles(['ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR']);
				break;
			case self::ACCESS_ONE_CTRL:
				return $this->isYourControle($controle);
				break;
			default:
				break;
		}
		return false;
	}
	
	/**
	 * @param Controle $entity
	 */
	public function isYourControle($entity){
		$repo = $this->em->getRepository(Controle::class);
		$qb = $repo ->listAllQueryBuilder();
		$qb->andWhere('q.id =:id ')->setParameter('id', $entity->getId());
		return count($qb->getQuery()->getArrayResult())>0;
	}
}