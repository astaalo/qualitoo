<?php

/*
 * edited by @mariteuw
 */
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use App\Annotation\QMLogger;
use App\Entity\Notification;

class NotificationController extends BaseController {
	/**
	 * @QMLogger(message="Affichage d'une notif")
	 * @Route("/{id}/read_notification", name="read_notification")
	 */
	public function indexAction(Request $request, $id) {
		$notif = $this->getDoctrine ()->getRepository ( 'OrangeMainBundle:Notification' )->find ( $id );
		
		if (! $notif) {
			throw $this->createNotFoundException ( 'aucun enregistrement de Notification trouvé avecc l\'id ' . $id );
		}
		
		$notif->setRead ( true );
		$params = $notif->getNotificationLink ();
		
		$em = $this->getDoctrine ()->getManager ();
		$em->persist ( $notif );
		$em->flush ();
		
		$flashLabel = $notif->getLibelle() . ' par ' . $notif->getUser();
		
		$this->get ( 'session' )->getFlashBag ()->add ( 'success', $flashLabel);
		return $this->redirect ( $this->generateUrl ( $params ['name'], $params ['params'] ) );
	}
	
	/**
     * @QMLogger(message="afficher toutes les notifications")
     * @Route("/les_notifs", name="les_notifs")
     * @Template("OrangeMainBundle:Notification:index.html.twig")
     */
	public function showUnreadNotificationAction(Request $request){
		return array();
	}
	
	 /**
     * @QMLogger(message="Chargemetn ajax des notifs non lues")
     * @Route("/liste_des_notifs", name="liste_des_notifs")
     * @Template()
     */
	public function listUnreadNotificationAction(Request $request){
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('OrangeMainBundle:Notification')->getUnreadNotif();
		return $this->paginate($request, $queryBuilder);
	}
	/**
	 * @param Notification $entity
	 */
	public function addRowInTable($entity){
		return array(
				$entity->getLibelle(),
				$entity->getUser() ?$entity->getUser()->__toString(): '-',
				$entity->getDateCreation() ? $entity->getDateCreation()->format('d/m/Y') : '-', 
				$this->service_action->generateActionsForNotification($entity)
		);
	}
}