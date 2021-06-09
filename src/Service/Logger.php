<?php

namespace Orange\MainBundle\Services;

Class Logger 
{
	public function log($container, $logger, $user, $descAction)
	{
		$login = $user ? $user->getUsername() : 'inconnu';
		$logger->info('Action : '.$descAction);
		$logger->info('Date : '.date('Y-m-d H:i:s'));
		$logger->info('Prénom et nom : '.$user->getPrenom().' '.$user->getNom());
		$logger->info('Nom utilisateurs : '.$login);
		$logger->info('Adresse ip : '.$container->get('request')->getClientIp());
	}
}