<?php
namespace App\Service;

use App\Entity\Risque;
use App\Entity\Utilisateur;
use App\Entity\HistoryEtatRisque;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Status
{
	private $states;
	protected $container;
	private $em;

	public function __construct($states = array(), ContainerInterface $container) {
		$this->states    = $states;
		$this->container = $container;
		$this->em = $this->container->get('doctrine.orm.entity_manager');
	}
	
	/**
	 * @param \App\Entity\Societe $entity
	 * @return string
	 */
    public function generateStatusForSociete($entity) {
    	return sprintf('<span class="label_state %s" style="padding: 5px 25px; border-radius: 5px;">%s</span>',
    			$entity->getEtat() ? 'success' : 'error',
    			$entity->getEtat() ? 'Actif' : 'Inactif');
    }

	/**
	 * @param \App\Entity\Utilisateur $entity
	 * @return string
	 */
    public function generateStatusForUtilisateur($entity) {
    	if($entity->isEnabled()) {
    		$label = 'Actif';
    		$color = '#01DF01';
    	} else {
    		$label = 'Inactif';
    		$color = '#FF4000';
    	}
    	return $this->showLabelWithColor($label, $color);
    }

	/**
	 * @param \App\Entity\Projet $entity
	 * @return string
	 */
    public function generateStatusForProjet($entity) {
    	if($entity->getEtat()==1) {
    		$label = 'Actif';
    		$color = '#01DF01';
    	}elseif($entity->getEtat()==0) {
    		$label = 'Inactif';
    		$color = '#FF4000';
    	}elseif ($entity->getEtat()==-1){
    		$label = 'Abandonné';
    		$color = '#000000';
    	}else{
    		$label = 'Cloturé';
    		$color = '#FF4000';
    	}
    	return $this->showLabelWithColor($label, $color);
    }

	/**
	 * @param \App\Entity\Question $entity
	 * @return string
	 */
    public function generateStatusForQuestion($entity) {
    	if($entity->getEtat()) {
    		$label = 'Actif';
    		$color = '#01DF01'; 
    	} else {
    		$label = 'Inactif';
    		$color = '#FF4000';
    	}
    	return $this->showLabelWithColor($label, $color);
    }
    
    /**
     * @param Mixed $entity
     * @return string
     */
    public function generateStatusForEntity($entity) {
    	if($entity->getEtat()) {
    		$label = 'Actif';
    		$color = '#01DF01'; 
    	} else {
    		$label = 'Inactif';
    		$color = '#FF4000';
    	}
    	return $this->showLabelWithColor($label, $color);
    }

    public function showLabelWithColor($label, $color) {
    	return sprintf('<span style="background-color: %s; color: #fff; font-weight: bold;font-size: 12px;padding: 5px 25px; border-radius: 5px;">%s</span>', $color, $label);
    }
    
    /**
     * 
     * @param Risque $risque
     * @param Utilisateur $user
     */
    public function logEtatRisque($risque,$user,$comment=''){
    	$history = new HistoryEtatRisque();
    	$etat = $this->states['risque']['rejete'];
    	$history->setEtat($etat);
    	$history->setRisque($risque);
    	$history->setComment($comment);
    	$history->setUtilisateur($user);
    	$this->em->persist($history);
    	$this->em->flush();
    	return;
    }
    
}
