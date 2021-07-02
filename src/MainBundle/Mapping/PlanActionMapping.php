<?php
namespace Orange\MainBundle\Mapping;

use Orange\MainBundle\Entity\Risque;

class PlanActionMapping extends BaseMapping {
	
	/**
	 * @param array $result
	 * @return array
	 */
	public function mapForExport($result) {
		$data = array();
		foreach($result as $planAction) {
			$data[$planAction->getId()] = array(
					'code' 					=> sprintf('%s', $planAction->getCode()),
					'risque'				=> sprintf('%s', $planAction->getCauseOfRisque()->getRisque()),
					'cause'					=> sprintf('%s', $planAction->getCauseOfRisque()->getCause()),
					'description'			=> sprintf('%s', $planAction->getLibelle()),
					'porteur'				=> sprintf('%s', $planAction->getPorteur()),
					'structurePorteur'		=> sprintf('%s', $planAction->getStructurePorteur()),
					'superviseur'			=> sprintf('%s', $planAction->getSuperviseur()),
					'structureSuperviseur'	=> sprintf('%s', $planAction->getStructureSuperviseur()),
					'statut'				=> sprintf('%s', $planAction->getStatut()),
					'echeance'				=> sprintf('%s', $planAction->getDateDebut() ? $planAction->getDateDebut()->format('d/m/Y') : null),
					'date_fin'				=> sprintf('%s', $planAction->getDateFin() ? $planAction->getDateFin()->format('d/m/Y') : null),
					'avancement'			=> sprintf('%s', $planAction->getAvancementInText())
				);
		}
		return $data;
	}
	
	
}