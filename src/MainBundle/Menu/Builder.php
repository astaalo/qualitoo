<?php

namespace App\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

	public function showMenu(FactoryInterface $factory, array $options) {
		$menu = $factory->createItem('menu');
		$menu->addChild('Processus', array('route' => 'les_processus', 'label' => 'Processus', 'attributes' => array('class' => 'repeat')));
		$menu->addChild('Societe', array('route' => 'les_societes', 'label' => "Societe", 'attributes' => array('class' => 'access_point')));
		$menu->addChild('Utilisateur', array('route' => 'les_utilisateurs', 'label' => 'Utilisateur', 'attributes' => array('class' => 'administrator')));
		$menu->addChild('Structure', array('route' => 'les_structures', 'label' => 'Structure/Entité', 'attributes' => array('class' => 'door')));
		$menu->addChild('Document', array('route' => 'les_documents', 'label' => 'Document', 'attributes' => array('class' => 'door')));
		//$menu->addChild('Rubrique', array('route' => 'les_structures', 'label' => 'Rubriques', 'attributes' => array('class' => 'spreadsheet')));
		//$menu->addChild('Theme', array('route' => 'les_structures', 'label' => 'Thémes', 'attributes' => array('class' => 'spreadsheet')));
		return $menu;
	}
	
	public function showShortcut(FactoryInterface $factory, array $options) {
		$notificationCount = $this->getNotificationsCount();
		$notifications = $this->getNotifications();
		$menu = $factory->createItem('menu')->setChildrenAttributes(array('id' => 'dropdown', 'class' => 'shortcut'));
		$menu->addChild('Accueil', array('route' => 'dashboard', 'label' => 'Accueil', 'attributes' => array('icon' => 'shortcut/home.png')))
			->setLabelAttribute('title', 'Page d\'accueil');
		$menu->addChild('Notifications', array('uri' => '#', 'label' => 'Notifications', 'attributes' => array(
						'icon' => 'shortcut/bell.png', 'has_notifications' => true, 'notifications_count' => $notificationCount 
				)))->setLabelAttribute('title', 'Notifications');
		// Notifications children
		foreach($notifications as $notification) {
			$menu ['Notifications']->addChild($notification->getId(), array('route' => 'read_notification', 
					'routeParameters' => array('id' => $notification->getId(), 'label' => 'Add new page'), 'label' => $notification->getLibelle(),
					'attributes' => array('description' => sprintf('par %s, %s', $notification->getUser(), $notification->getDateModification()->format('à d/m/Y')))
				));
		}
		if($notificationCount >= 5) {
			$menu ['Notifications']->addChild($notification->getId(), array('route' => 'les_notifs', 'label' => 'Tout afficher', 'attributes' => array('class' => 'last')));
		}
		/*if($this->hasRole('ROLE_ADMIN')) {
			$menu->addChild('Config', array('route' => 'les_processus', 'label' => 'Config.', 'attributes' => array('icon' => 'shortcut/setting.png')))
				->setLabelAttribute('title', 'Configuration');
		}
		if($this->hasRole('ROLE_RISKMANAGER') || $this->hasRole('ROLE_ADMIN') || $this->hasRole('ROLE_RESPONSABLE')) {
			$menu->addChild('Reporting', array('uri' => $this->container->get('router')->generate('tprc', array('carto' =>1)), 'label' => 'Reporting', 'attributes' => array('icon' => 'shortcut/graph.png')))
				->setLabelAttribute('title', 'Reporting');
		}*/
		if($this->hasRole('ROLE_SUPER_ADMIN')) {
			$menu->addChild('Parameters', array(
				'route' => 'les_utilisateurs', 'label' => 'Parameters', 'attributes' => array('icon' => 'shortcut/setting.png') 
				))->setLabelAttribute('title', 'Parameters');
		}
		return $menu;
	}

	public function getNotificationsCount() {
		return $this->container->get('orange_main.notification')->getNotificationNumber();
	}
	
	public function getNotifications() {
		return $this->container->get('orange_main.notification')->getUnreadNotifications();
	}
	
	public function hasRole($role) {
		return $this->container->get('security.token_storage')->getToken()->getUser()->hasRole($role);
	}
}