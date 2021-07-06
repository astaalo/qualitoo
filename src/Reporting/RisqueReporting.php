<?php	
namespace App\Reporting;

class RisqueReporting extends ExcelReporting {
	
	/**
	 * @param array $data
	 * @param integer $profilType
	 * @param Societe $societe
	 */
	public function extract($data, $cartographie_id, $societe) {
		switch($cartographie_id) {
			case 1:
				$this->extractMetier($data, $societe);
				break;
			case 2:
				$this->extractProjet($data, $societe);
				break;
			case 3:
				$this->extractSite($data, $societe);
				$this->setValues(1, array('G'=>'Danger'));
				break;
			case 4:
				$this->extractSite($data, $societe);
				break;
		}
		return $this;
	}
	
	/**
	 * @param array $data
	 * @param Societe $societe
	 */
	public function extractMetier($data, $societe) {
		$this->setDimensionColumns(array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'O'), 50);
		$this->setDimensionColumns(array('J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z'), 25);
		$this->setValues(1, array('A'=>'Macro Processus', 'B'=>'Processus', 'C'=>'Sous Processus', 'D'=>'Entité', 'E'=>'Sous-Entité'));
		$this->setValues(1, array('F'=>'Code Activité','G'=>'Activité', 'H'=>'Code risque', 'I'=>'Risque', 'J'=>'Causes', 
								  'k'=>'Code PA','L'=>"Description du plan d'action", 'M'=>'Porteur'));
		$this->setValues(1, array('N'=>'Date de début', 'O'=>'Date de fin', 'P'=>'Avancement', 'Q'=>'Statut','R'=>'Code Controle' ,'S'=>'Objectifs de controle'));
		$this->setValues(1, array('T'=>'Contrôle description','U' =>'Type de controle' ,'V'=>'Methode de contrôle', 'W'=>'Probabilité', 'X'=>'Gravité'));
		$column = 'Y';
		foreach($data['domaine'] as $key=>$domaine) {
			$this->getActiveSheet()->setCellValue($column.'1', $domaine['name']);
			$data['domaine'][$key]['column'] = $column;
			$column++;
		}
		$this->getActiveSheet()->setCellValue($column++.'1', 'Maturité CI');
		$this->getActiveSheet()->setCellValue($column++.'1', 'Criticité');
		$row = 2;
		foreach($data['carto'] as $processus) {
			$this->extractByProcessus($processus, $data['domaine'] ,$row, $row, 1);
		}
		$this->getActiveSheet()->getStyle('A1:Z'.$this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->setColors('A1:J1', 'FF6600');
		$this->setColors('K1:Q1', 'CCCCCC');
		$this->setColors('R1:V1', '2EFE2E');
		$this->setColors(sprintf('W1:%s1', $column), 'FF6600');
	}
	
	/**
	 * @param array $data
	 * @param Societe $societe
	 */
	public function extractProjet($data, $societe) {
		$this->setDimensionColumns(array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'O'), 50);
		$this->setDimensionColumns(array('J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z'), 25);
		$this->setValues(1, array('A'=>'Macro Processus', 'B'=>'Processus', 'C'=>'Sous Processus', 'D'=>'Entité', 'E'=>'Sous-Entité'));
		$this->setValues(1, array('F'=>'Code Projet','G'=>'Projet', 'H'=>'Code risque', 'I'=>'Risque', 'J'=>'Causes',
				'k'=>'Code PA','L'=>"Description du plan d'action", 'M'=>'Porteur'));
		$this->setValues(1, array('N'=>'Date de début', 'O'=>'Date de fin', 'P'=>'Avancement', 'Q'=>'Statut','R'=>'Code Controle' ,'S'=>'Objectifs de controle'));
		$this->setValues(1, array('T'=>'Contrôle description','U' =>'Type de controle' ,'V'=>'Methode de contrôle', 'W'=>'Probabilité', 'X'=>'Gravité'));
		$column = 'Y';
		foreach($data['domaine'] as $key=>$domaine) {
			$this->getActiveSheet()->setCellValue($column.'1', $domaine['name']);
			$data['domaine'][$key]['column'] = $column;
			$column++;
		}
		$this->getActiveSheet()->setCellValue($column++.'1', 'Maturité CI');
		$this->getActiveSheet()->setCellValue($column++.'1', 'Criticité');
		$row = 2;
		foreach($data['carto'] as $processus) {
			$this->extractByProcessus($processus, $data['domaine'] ,$row, $row, 1);
		}
		$this->getActiveSheet()->getStyle('A1:Z'.$this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->setColors('A1:J1', 'FF6600');
		$this->setColors('K1:Q1', 'CCCCCC');
		$this->setColors('R1:V1', '2EFE2E');
		$this->setColors(sprintf('W1:%s1', $column), 'FF6600');
	}
	
	
	/**
	 * @param array $data
	 * @param Societe $societe
	 */
	public function extractSite($data, $societe) {
		$this->setDimensionColumns(array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'), 40);
		$this->setDimensionColumns(array('K', 'L', 'O'), 100);
		$this->setDimensionColumns(array('J', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'), 35);
		$this->setValues(1, array('A'=>'Site', 'B'=>'Domaine', 'C'=>'thème du risque', 'D'=>'Activité/Equipement', 'E'=>'Propriétaire'));
		$this->setValues(1, array('F'=>'Code Risks', 'G'=>'Aspect', 'H'=>'Mode Fonctionnement', 'I'=>"Risque", 'J'=>'Lieu'));
		$this->setValues(1, array('K'=>'Manifestation', 'L'=>'Dispositif de maitrise', 'M'=>'Type de controle', 'N'=>'Méthode de controle', 'O'=>'Decription PA'));
		$this->setValues(1, array('P'=>'Type de contrôle', 'Q'=>'Porteur', 'R'=>'Date fin', 'S'=>'Statut', 'T'=>'Probabilité/Exposition', 'U'=>'Gravité', 'V'=>'Criticité','W'=>'Maturité'));
		$row = 2;
		foreach($data as $dt) {
			$this->extractForEnvironnement($dt, $row);
		}
		$this->getActiveSheet()->getStyle('A1:W'.$this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->setColors('A1:C1', 'c3c3c3');
		$this->setColors('D1:K1', 'FF6600');
		$this->setColors('L1:N1', 'EEFF00');
		$this->setColors('O1:S1', '22CCEE');
		$this->setColors('T1:W1', 'EECECE');
	}
	
	
	
	/**
	 * @param array $processus
	 * @pram integer $row
	 */
	public function extractByProcessus($processus, $domaines, $start, &$row, $level) {
		$line = $start;
		//var_dump($processus['children'][5]['children'][12]['activite'][2]['risque'][7646]['cause']);exit;
		foreach($processus['activite'] as $activite) {
			foreach($activite['risque'] as $risque) {
				$this->extractByRisque($risque, $domaines, $row, $row);
			}
			for($index=$line;$index<$row;$index++) {
				$this->getActiveSheet()->setCellValue('F'.$index, $activite['code']);
				$this->getActiveSheet()->setCellValue('G'.$index, $activite['name']);
			}
			$line = $row;
		}
		foreach($processus['children'] as $data) {
			$this->extractByProcessus($data, $domaines,$row, $row, $level + 1);
		}
		$line = $start;
		switch($level) {
			case 1:
				if($level==$processus['type']) {
					for($index=$line;$index<$row;$index++) {
						$this->getActiveSheet()->setCellValue('A'.$index, $processus['name']);
						$this->getActiveSheet()->setCellValue('D'.$index, $processus['structure']);
					}
				}
				break;
			case 2:
				if($level==$processus['type']) {
					for($index=$line;$index<$row;$index++) {
						$this->getActiveSheet()->setCellValue('B'.$index, $processus['name']);
						$this->getActiveSheet()->setCellValue('E'.$index, $processus['structure']);
					}
				}
				break;
			case 3:
				if($level==$processus['type']) {
					for($index=$line;$index<$row;$index++) {
						$this->getActiveSheet()->setCellValue('C'.$index, $processus['name']);
					}
				}
				break;
		}
		return $this;
	}
	
	/**
	 * 
	 * @param unknown $risque
	 * @param unknown $domaines
	 * @param unknown $start
	 * @param unknown $row
	 */
	public function extractByRisque($risque, $domaines, $start, &$row) {
		$row += count($risque['cause']) ? 0 : 1;
		foreach($risque['cause'] as $cause) {
			$this->extractBycause($cause, $domaines, $risque['impact'], $cause['probabilite'], $risque['gravite'],$row);
		}
		for($index=$start;$index<$row;$index++) {
			$this->getActiveSheet()->setCellValue('H'.$index, $risque['code']);
			$this->getActiveSheet()->setCellValue('I'.$index, $risque['name']);
		}
		return $this;
	}
	
	/**
	 * 
	 * @param unknown $cause
	 * @param unknown $domaines
	 * @param unknown $impacts
	 * @param unknown $probabilite
	 * @param unknown $gravite
	 * @param unknown $row
	 */
	public function extractBycause($cause, $domaines, $impacts, $probabilite, $gravite, &$row) {
		$maturite=($probabilite<3) ? (4-$probabilite) : 1;
		$ctrl=null;
		$start = $row;
		$row += (count($cause['pa']) || count($cause['controle'])) ? 0 : 1;
		if(isset($cause['pa'])) {
			foreach ($cause['pa'] as $pa) {
				$this->getActiveSheet()->setCellValue('K'.$row, $pa['code']);
				$this->getActiveSheet()->setCellValue('L'.$row, $pa['name']);
				$this->getActiveSheet()->setCellValue('M'.$row, $pa['porteur']);
				$this->getActiveSheet()->setCellValue('N'.$row, $pa['date_debut']);
				$this->getActiveSheet()->setCellValue('O'.$row, $pa['date_fin']);
				$this->getActiveSheet()->setCellValue('P'.$row, $pa['avancement']);
				$this->getActiveSheet()->setCellValue('Q'.$row, $pa['statut']);
				if(isset($pa['ctrl'])) {
					$ctrl=&$pa['ctrl'];
					$this->getActiveSheet()->setCellValue('R'.$row, $ctrl->getCode());
					$this->getActiveSheet()->setCellValue('S'.$row, $ctrl->getDescription());
					$this->getActiveSheet()->setCellValue('T'.$row, $ctrl->getDescription());
					$this->getActiveSheet()->setCellValue('U'.$row, $ctrl->getTypeControle());
					$this->getActiveSheet()->setCellValue('V'.$row, $ctrl->getMethodeControle());
				}
				if($impacts!=null) {
					$this->extractByImpact($domaines, $impacts,$row);
				}
				$row++;
			}
		}
		$codePa=($ctrl!=null) ? $ctrl->getId() : 0;
		foreach ($cause['controle'] as $ctrl) {
			if($codePa!=$ctrl['id']) { 				
				$this->getActiveSheet()->setCellValue('R'.$row, $ctrl['code']);
				$this->getActiveSheet()->setCellValue('S'.$row, $ctrl['name']);
				$this->getActiveSheet()->setCellValue('T'.$row, $ctrl['description']);
				$this->getActiveSheet()->setCellValue('U'.$row, $ctrl['type']);
				$this->getActiveSheet()->setCellValue('V'.$row, $ctrl['methode']);
				if(isset($ctrl['pa'] )) {
					$pa=&$ctrl['pa'];
					$this->getActiveSheet()->setCellValue('K'.$row, $pa->getCode());
					$this->getActiveSheet()->setCellValue('L'.$row, $pa->getLibelle());
					$this->getActiveSheet()->setCellValue('M'.$row, sprintf($pa->getPorteur()));
					$this->getActiveSheet()->setCellValue('N'.$row, $pa->getDateDebut()->format('d-m-Y'));
					$this->getActiveSheet()->setCellValue('O'.$row, $pa->getDateFin()->format('d-m-Y'));
					$this->getActiveSheet()->setCellValue('P'.$row, $pa->getAvancementInText());
					$this->getActiveSheet()->setCellValue('Q'.$row, sprintf($pa->getStatut()));
				}
			}
			if($impacts!=null)
				$this->extractByImpact($domaines, $impacts,$row);
			if($codePa!=$ctrl['id']) { 
				$row++;
			}
		}
		for($index=$start;$index<$row;$index++) {
			$column = end($domaines)['column']++;
			$column++;
			$this->getActiveSheet()->setCellValue('J'.$index, $cause['name']);
			$this->getActiveSheet()->setCellValue('W'.$index, $probabilite);
			$this->getActiveSheet()->setCellValue('X'.$index, $gravite);
			$this->getActiveSheet()->setCellValue($column++.$index, $maturite);
			$this->getActiveSheet()->setCellValue($column++.$index, $probabilite*$gravite);
		}
		return $this;
	}
	
	/**
	 * 
	 * @param unknown $domaines
	 * @param unknown $impacts
	 * @param unknown $row
	 */
	public  function extractByImpact(&$domaines, &$impacts,$row){
		foreach ($domaines as $key=>$domaine){
			foreach ($impacts as $cle=>$impact){
				foreach ($impact['domaine'] as $cl=>$imp) {
					if($cl==$key) {
						$this->getActiveSheet()->setCellValue($domaine['column'].$row,$imp);
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $row
	 */
	public function extractForEnvironnement($data, &$row) {
		if(!isset($data['cause'])) {
			$this->extractByRisqueEnv($data, $row);
		} else {
			foreach ($data['cause'] as $cause) {
				$this->extractByDanger($cause, $data,$row);
			}
		}
	}
	/**
	 * 
	 * @param unknown $cause
	 * @param unknown $data
	 * @param unknown $row
	 */
	public function extractByDanger($cause,$data,&$row) {
		if(isset($cause['controle'])) {
			foreach ($cause['controle'] as $ctrl){
				$this->extractByRisqueEnv($data, $row);
				$this->getActiveSheet()->setCellValue('G'.$row, $cause['name']);
				$this->getActiveSheet()->setCellValue('H'.$row, $cause['mode']);
				$this->getActiveSheet()->setCellValue('L'.$row, $ctrl['description']);
				$this->getActiveSheet()->setCellValue('M'.$row, $ctrl['type']);
				$this->getActiveSheet()->setCellValue('N'.$row, $ctrl['methode']);
				if(isset($ctrl['pa'] )){
					$pa=$ctrl['pa'];
					$this->getActiveSheet()->setCellValue('O'.$row, $pa->getLibelle());
					$this->getActiveSheet()->setCellValue('Q'.$row, sprintf($pa->getPorteur()));
					$this->getActiveSheet()->setCellValue('R'.$row, $pa->getDateFin()->format('d-m-Y'));
					$this->getActiveSheet()->setCellValue('S'.$row, $pa->getAvancementInText());
				}
				$this->getActiveSheet()->setCellValue('T'.$row, $cause['probabilite']);
				$row++;
			}
			if(isset($cause['pa'])) {
				foreach ($cause['pa'] as $pa){
					$this->extractByRisqueEnv($data, $row);
					$this->getActiveSheet()->setCellValue('G'.$row, $cause['name']);
					$this->getActiveSheet()->setCellValue('H'.$row, $cause['mode']);
					$this->getActiveSheet()->setCellValue('O'.$row, $pa['name']);
					$this->getActiveSheet()->setCellValue('Q'.$row, $pa['porteur']);
					$this->getActiveSheet()->setCellValue('R'.$row, $pa['date_fin']);
					$this->getActiveSheet()->setCellValue('S'.$row, $pa['statut']);
					if(isset($pa['ctrl'] )){
						$ctrl=&$pa['ctrl'];
						$this->getActiveSheet()->setCellValue('L'.$row, $ctrl->getDescription());
						$this->getActiveSheet()->setCellValue('M'.$row, sprintf($ctrl->getTypeControle()));
						$this->getActiveSheet()->setCellValue('N'.$row, $ctrl->getMethodeControle()?$ctrl->getMethodeControle()->getLibelle():'');
					}
					$this->getActiveSheet()->setCellValue('T'.$row, $cause['probabilite']);
					$row++;
				}
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $row
	 */
	public function extractByRisqueEnv($data,$row){
		$this->getActiveSheet()->setCellValue('A'.$row, $data['site']);
		$this->getActiveSheet()->setCellValue('B'.$row, $data['domaine']);
		$this->getActiveSheet()->setCellValue('C'.$row, '');
		$this->getActiveSheet()->setCellValue('D'.$row, $data['equipement']);
		$this->getActiveSheet()->setCellValue('E'.$row, $data['proprietaire']);
		$this->getActiveSheet()->setCellValue('F'.$row, $data['code_risque']);
		$this->getActiveSheet()->setCellValue('I'.$row, $data['risque']);
		$this->getActiveSheet()->setCellValue('J'.$row, $data['lieu']);
		$this->getActiveSheet()->setCellValue('K'.$row, $data['manifestation']);
		//$this->getActiveSheet()->setCellValue('T'.$row, $data['probabilite']);
		$this->getActiveSheet()->setCellValue('U'.$row, $data['gravite']);
		$this->getActiveSheet()->setCellValue('V'.$row, $data['probabilite']*$data['gravite']);
		$this->getActiveSheet()->setCellValue('W'.$row, ($data['probabilite']<3) ? (4-$data['probabilite']) : 1);
	}
}

