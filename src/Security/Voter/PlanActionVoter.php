<?php 

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PlanActionVoter extends Voter {
	
	const CREATE 	  	  = 'create';
	const READ 	 		  = 'read';
	const UPDATE 	 	  = 'update';
	const DELETE	 	  = 'delete';
	const ACCESS_ONE_PA   = 'accesOnePa';

	protected function supports($attribute, $risque): bool {
        return in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE,  self::ACCESS_ONE_PA])
			&& $risque instanceof \App\Entity\PlanAction;
    }

    protected function voteOnAttribute($attribute, $pa, TokenInterface $token): bool {
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
				return ($user->hasRoles(['ROLE_ADMIN', 'ROLE_USER']) || $this->isYourPA($pa));
				break;
			case self::UPDATE:
				return ($user->hasRoles(['ROLE_ADMIN', 'ROLE_USER']) || $this->isYourPA($pa));
				break;
			case self::DELETE:
				return ($user->hasRoles(['ROLE_ADMIN', 'ROLE_USER']));
				break;
			case self::ACCESS_ONE_PA:
				return $this->isYourPA($pa);
				break;
			default:
				return false;
				break;
		}
		return false;
	}
	
	/**
	 * @param PlanAction $entity
	 */
	public function isYourPA($entity){
			$qb  = $this->em->getRepository('OrangeMainBundle:PlanAction')
				->listAllQueryBuilder()
				->andWhere('q.id =:id ')->setParameter('id', $entity->getId());
			return count($qb->getQuery()->getArrayResult())>0;
	}
	
}