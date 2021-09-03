<?php
namespace App\MainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OrangeMainBundle extends Bundle
{
	const CTRL_VALIDATED = 'orange_main.controle.validated';
	const PA_VALIDATED = 'orange_main.pa.validated';
	const EVALUATION_VALIDATED = 'orange_main.evaluation.validated';
	const RISQUE_VALIDATED= 'orange_main.risque.validated';
	
	const CTRL_CREATED = 'orange_main.controle.created';
	const PA_CREATED = 'orange_main.pa.created';
	const EVALUATION_CREATED = 'orange_main.evaluation.created';
	const RISQUE_CREATED= 'orange_main.risque.created';
	const USER_CREATED= 'orange_main.risque.created';
	
	
	/* (non-PHPdoc)
	 * @see \Symfony\Component\HttpKernel\Bundle\Bundle::boot()
	 */
	public function boot() {
		// TODO: Auto-generated method stub
		$ids	= $this->container->getParameter('ids');
		$states = $this->container->getParameter('states');
		$types = $this->container->getParameter('types');
		// \App\Entity\Risque::$states = $states['risque'];
		// \App\Entity\Risque::$types = $ids['type_processus'];
		// \App\Entity\TypeProcessus::$ids = $ids['type_processus'];
		// \App\Entity\Cartographie::$ids = $ids['carto'];
		// \App\Entity\Risque::$carto = $ids['carto'];
		// \App\Entity\TypeEvaluation::$ids = $ids['type_evaluation'];
		// \App\Entity\ModeFonctionnement::$ids = $ids['mode_fonctionnement'];
		//\App\Entity\Projet::$states = $states['projet'];
		// \App\Entity\Equipement::$types = $types['equipement'];
		// \App\Entity\Rapport::$types = $types['rapportChargement'];
	}
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
