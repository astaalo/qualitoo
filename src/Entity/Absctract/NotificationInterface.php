<?php

namespace App\Entity\Absctract;

use App\Entity\Utilisateur;
use App\Entity\TypeNotification;
use App\Entity\Notification;
/**
 * NotificationInterface
 */
interface NotificationInterface
{
	/**
     * Get Temple of notification
     *
     * @param array $notifs
     *
     */
    public function generateNotification(Utilisateur $user, TypeNotification $type, $isNew);
    
	/**
     * Get Temple of notification
     *
     * @param array $notifs
     *
     */
    public function generateWorkflow(Notification $notification, TypeNotification $type, $isNew);
}
