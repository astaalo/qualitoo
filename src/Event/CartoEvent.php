<?php
namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\Risque;
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
	 * @param \App\Entity\Risque $risque
	 */
	private $risque;
	
	/**
	 * @param \App\Entity\PlanAction $planAction
	 */
	private $planAction;
	
	/**
	 * @param \App\Entity\Controle $controle
	 */
	private $controle;
	
	/**
	 * @param \App\Entity\Evaluation $evaluation
	 */
	private $evaluation;
	
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
	 * set risque
	 * @param \App\Entity\Risque $risque
	 */
	public function setRisque(\App\Entity\Risque $risque) {
		$this->risque = $risque;
	}
	
	/**
	 * get Suivi
	 * @return \App\Entity\Risque
	 */
	public function getRisque() {
		return $this->risque;
	}
	
	/**
	 * set planAction
	 * @param \App\Entity\PlanAction $planAction
	 */
	public function setPlanAction(\App\Entity\PlanAction $planAction) {
		$this->planAction = $planAction;
	}
	
	/**
	 * get PA
	 * @return \App\Entity\PlanAction
	 */
	public function getPlanAction() {
		return $this->planAction;
	}
	
	/**
	 * get controle
	 * @return \App\Entity\Controle
	 */
	public  function getControle(){
		return $this->controle;
	}
	
	/**
	 * 
	 * @param \App\Entity\Controle $controle
	 */
	public function setControle(\App\Entity\Controle $controle){
		$this->controle=$controle;
	}
	
	/**
	 * get evaluation
	 * @return \App\Entity\Evaluation
	 */
	public  function getEvaluation(){
		return $this->evaluation;
	}
	
	/**
	 *
	 * @param \App\Entity\Evaluation $evaluation
	 */
	public function setEvaluation(\App\Entity\Evaluation $evaluation){
		$this->evaluation=$evaluation;
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
