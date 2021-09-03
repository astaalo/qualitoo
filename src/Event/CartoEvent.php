<?php
namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;

class CartoEvent extends Event
{
	/**
	 * 
	 * @var EntityManager
	 */
	private $entityManager;
	
	/**
	 * @param \App\Entity\Utilisateur $user
	 */
	private $user;
	
	/**
	 * 
	 * @param Container $container
	 */
	public function __construct($container)
	{
		$this->entityManager=$container->get('doctrine.orm.entity_manager');
	}

	
	/**
	 * get user
	 * @return \App\Entity\Utilisateur
	 */
	public  function getUser(){
		return $this->user;
	}
	
	/**
	 *
	 * @param \App\Entity\Utilisateur $user
	 */
	public function setUser(\App\Entity\Utilisateur $user){
		$this->user=$user;
	}
	
	/**
	 * get container
	 */
	public function getContainer()
	{
		return $this->container;
	}
	
	/**
	 * get entity
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}
	
}
