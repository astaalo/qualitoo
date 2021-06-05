<?php
namespace App\Utils;

use App\Entity\Notification;

class NotificationUtil {
	/**
	 * @param string $libelle 
	 * @param string $description
	 * @param string $user
	 * 
	 * @return Notification
	 */	
	public static function create($libelle, $description, $entity, $type, $user) {
		$notif = new Notification();
		$notif->setLibelle($libelle);
		$notif->setDescription($description);
		$notif->setUser($user);
		
		$notif->setType ( $type );
		
		$entitySimpleClassName = self::getClassName($entity);
		$notifSimpleClassName = 'Notification' . $entitySimpleClassName;
		$notifFullClassName = 'App\Entity\\' . $notifSimpleClassName;
		$notifForClass= new $notifFullClassName;
		$notifForClass->{'set' . $entitySimpleClassName}($entity);
		$notif->{'set' . $notifSimpleClassName}($notifForClass);
		
		return $notif;
	}
	
	/**
	 * @param Entity $entity
	 * @return string
	 */
	public static function getClassName($entity) {
		$reflexion = new \ReflectionClass($entity);
		return $reflexion->getShortName();
	}
}
