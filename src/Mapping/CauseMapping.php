<?php
namespace App\Mapping;

class CauseMapping extends BaseMapping {
	
	/**
	 * @param array $result
	 * @return array
	 */
	public function mapForBasicExport($result) {
		$data = array();
		foreach($result as $cause) {
			$this->putFamilleInArch($cause->getArchFamille(), $data);
			$this->putMeInArch($cause->getArchFamille(), $data, $cause);
		}
		return $data;
	}

	/**
	 * @param array $archRisque
	 * @param array $data
	 */
	private function putFamilleInArch($archCause, &$data) {
		foreach($archCause as $key => $value) {
			if(isset($data[$key])) {
				$this->putFamilleInArch($value['children'], $data[$key]['children']);
			} else {
				$data[$key] = $value;
			}
		}
	}

	/**
	 * @param array $archCause
	 * @param array $data
	 * @param \Orange\MainBundle\Entity\Cause $cause
	 */
	private function putMeInArch($archCause, &$data, $cause) {
		foreach($archCause as $key => $value) {
			if(count($value['children'])==0) {
				$causeId = $cause->getId();
				$data[$key]['cause'][$causeId] = array(
						'name'=>$cause->getLibelle(), 
						'constat'=>$cause->getGrille() ? $cause->getGrille()->__toString() : 'Non renseigné', 
						'note'=>$cause->getGrille() ? $cause->getGrille()->getValeur() : 'Non renseigné'
					);
				break;
			}
			$this->putMeInArch($value['children'], $data[$key]['children'], $cause);
		}
	}

	
}
