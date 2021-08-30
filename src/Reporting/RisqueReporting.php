<?php	
namespace App\Reporting;

use App\Entity\Societe;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RisqueReporting extends ExcelReporting {
	protected $sheet;
	/**
	 * @param array $data
	 * @param integer $profilType
	 * @param Societe $societe
	 */
	public function extract($data, $cartographie_id, $societe) {
		switch($cartographie_id) {
			case 1:
				$report = $this->extractMetier($data, $societe);
				break;
			case 2:
                $report = $this->extractProjet($data, $societe);
				break;
			case 3:
                $report = $this->extractSite($data, $societe);
				$this->setValues(1, array('G'=>'Danger'));
				break;
			case 4:
                $report = $this->extractSite($data, $societe);
				break;
		}
		return $report;
	}
	
	/**
	 * @param array $data
	 * @param Societe $societe
	 */
	public function extractMetier($data, $societe) {
        $spreadsheet = new Spreadsheet();
        $this->sheet = $spreadsheet->getActiveSheet();

        $this->setDimensionColumns($this->sheet, array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'O'), 50);
        $this->setDimensionColumns($this->sheet, array('J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z'), 25);
        $this->sheet->setTitle("Prises en charges des risques");
        $styleEntete = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'ffffff'),
                'size'  => 12,
                'name'  => 'Verdana'
            ));
        $this->sheet->getStyle("A1:D1")->applyFromArray($styleEntete);
        
        $this->setValues($this->sheet,1, array('A'=>'Macro Processus', 'B'=>'Processus', 'C'=>'Sous Processus', 'D'=>'Entité', 'E'=>'Sous-Entité'));
		$this->setValues($this->sheet,1, array('F'=>'Code Activité','G'=>'Activité', 'H'=>'Code risque', 'I'=>'Risque', 'J'=>'Causes', 
								  'k'=>'Code PA','L'=>"Description du plan d'action", 'M'=>'Porteur'));
		$this->setValues($this->sheet,1, array('N'=>'Date de début', 'O'=>'Date de fin', 'P'=>'Avancement', 'Q'=>'Statut','R'=>'Code Controle' ,'S'=>'Objectifs de controle'));
		$this->setValues($this->sheet,1, array('T'=>'Contrôle description','U' =>'Type de controle' ,'V'=>'Methode de contrôle', 'W'=>'Probabilité', 'X'=>'Gravité'));
		$column = 'Y';
		foreach($data['domaine'] as $key=>$domaine) {
			$this->sheet->setCellValue($column.'1', $domaine['name']);
			$data['domaine'][$key]['column'] = $column;
			$column++;
		}
		$this->sheet->setCellValue($column++.'1', 'Maturité CI');
		$this->sheet->setCellValue($column++.'1', 'Criticité');
		$row = 2;
		foreach($data['carto'] as $processus) {
			$this->extractByProcessus($processus, $data['domaine'] ,$row, $row, 1);
		}
		$this->sheet->getStyle('A1:Z'.$this->sheet->getHighestRow())->getAlignment()->setWrapText(true);
		$this->setColors($this->sheet, 'A1:J1', 'FF6600');
		$this->setColors($this->sheet, 'K1:Q1', 'CCCCCC');
		$this->setColors($this->sheet, 'R1:V1', '2EFE2E');
		$this->setColors($this->sheet, sprintf('W1:%s1', $column), 'FF6600');
        // Create your Office 2007 Excel (XLSX Format)
        return $this->save($spreadsheet);
	}
	
	/**
	 * @param array $data
	 * @param Societe $societe
	 */
	public function extractProjet($data, $societe) {
        $spreadsheet = new Spreadsheet();
        $this->sheet = $spreadsheet->getActiveSheet();

		$this->setDimensionColumns($this->sheet, array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'O'), 50);
		$this->setDimensionColumns($this->sheet, array('J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z'), 25);
		$this->setValues($this->sheet,1, array('A'=>'Macro Processus', 'B'=>'Processus', 'C'=>'Sous Processus', 'D'=>'Entité', 'E'=>'Sous-Entité'));
		$this->setValues($this->sheet,1, array('F'=>'Code Projet','G'=>'Projet', 'H'=>'Code risque', 'I'=>'Risque', 'J'=>'Causes',
				'k'=>'Code PA','L'=>"Description du plan d'action", 'M'=>'Porteur'));
		$this->setValues($this->sheet,1, array('N'=>'Date de début', 'O'=>'Date de fin', 'P'=>'Avancement', 'Q'=>'Statut','R'=>'Code Controle' ,'S'=>'Objectifs de controle'));
		$this->setValues($this->sheet,1, array('T'=>'Contrôle description','U' =>'Type de controle' ,'V'=>'Methode de contrôle', 'W'=>'Probabilité', 'X'=>'Gravité'));
		$column = 'Y';
		foreach($data['domaine'] as $key=>$domaine) {
			$this->sheet->setCellValue($column.'1', $domaine['name']);
			$data['domaine'][$key]['column'] = $column;
			$column++;
		}
		$this->sheet->setCellValue($column++.'1', 'Maturité CI');
		$this->sheet->setCellValue($column++.'1', 'Criticité');
		$row = 2;
		foreach($data['carto'] as $processus) {
			$this->extractByProcessus($processus, $data['domaine'] ,$row, $row, 1);
		}
		$this->sheet->getStyle('A1:Z'.$this->sheet->getHighestRow())->getAlignment()->setWrapText(true);
		$this->setColors($this->sheet,'A1:J1', 'FF6600');
		$this->setColors($this->sheet,'K1:Q1', 'CCCCCC');
		$this->setColors($this->sheet,'R1:V1', '2EFE2E');
		$this->setColors($this->sheet,sprintf('W1:%s1', $column), 'FF6600');
        // Create your Office 2007 Excel (XLSX Format)
        return $this->save($spreadsheet);
	}
	
	
	/**
	 * @param array $data
	 * @param Societe $societe
	 */
	public function extractSite($data, $societe) {
        $spreadsheet = new Spreadsheet();
        $this->sheet = $spreadsheet->getActiveSheet();

		$this->setDimensionColumns($this->sheet, array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'), 40);
		$this->setDimensionColumns($this->sheet, array('K', 'L', 'O'), 100);
		$this->setDimensionColumns($this->sheet, array('J', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'), 35);
		$this->setValues($this->sheet,1, array('A'=>'Site', 'B'=>'Domaine', 'C'=>'thème du risque', 'D'=>'Activité/Equipement', 'E'=>'Propriétaire'));
		$this->setValues($this->sheet,1, array('F'=>'Code Risks', 'G'=>'Aspect', 'H'=>'Mode Fonctionnement', 'I'=>"Risque", 'J'=>'Lieu'));
		$this->setValues($this->sheet,1, array('K'=>'Manifestation', 'L'=>'Dispositif de maitrise', 'M'=>'Type de controle', 'N'=>'Méthode de controle', 'O'=>'Decription PA'));
		$this->setValues($this->sheet,1, array('P'=>'Type de contrôle', 'Q'=>'Porteur', 'R'=>'Date fin', 'S'=>'Statut', 'T'=>'Probabilité/Exposition', 'U'=>'Gravité', 'V'=>'Criticité','W'=>'Maturité'));
		$row = 2;
		foreach($data as $dt) {
			$this->extractForEnvironnement($dt, $row);
		}
		$this->sheet->getStyle('A1:W'.$this->sheet->getHighestRow())->getAlignment()->setWrapText(true);
		$this->setColors($this->sheet,'A1:C1', 'c3c3c3');
		$this->setColors($this->sheet,'D1:K1', 'FF6600');
		$this->setColors($this->sheet,'L1:N1', 'EEFF00');
		$this->setColors($this->sheet,'O1:S1', '22CCEE');
		$this->setColors($this->sheet,'T1:W1', 'EECECE');
        // Create your Office 2007 Excel (XLSX Format)
        return $this->save($spreadsheet);
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
				$this->sheet->setCellValue('F'.$index, $activite['code']);
				$this->sheet->setCellValue('G'.$index, $activite['name']);
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
						$this->sheet->setCellValue('A'.$index, $processus['name']);
						$this->sheet->setCellValue('D'.$index, $processus['structure']);
					}
				}
				break;
			case 2:
				if($level==$processus['type']) {
					for($index=$line;$index<$row;$index++) {
						$this->sheet->setCellValue('B'.$index, $processus['name']);
						$this->sheet->setCellValue('E'.$index, $processus['structure']);
					}
				}
				break;
			case 3:
				if($level==$processus['type']) {
					for($index=$line;$index<$row;$index++) {
						$this->sheet->setCellValue('C'.$index, $processus['name']);
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
			$this->sheet->setCellValue('H'.$index, $risque['code']);
			$this->sheet->setCellValue('I'.$index, $risque['name']);
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
				$this->sheet->setCellValue('K'.$row, $pa['code']);
				$this->sheet->setCellValue('L'.$row, $pa['name']);
				$this->sheet->setCellValue('M'.$row, $pa['porteur']);
				$this->sheet->setCellValue('N'.$row, $pa['date_debut']);
				$this->sheet->setCellValue('O'.$row, $pa['date_fin']);
				$this->sheet->setCellValue('P'.$row, $pa['avancement']);
				$this->sheet->setCellValue('Q'.$row, $pa['statut']);
				if(isset($pa['ctrl'])) {
					$ctrl=&$pa['ctrl'];
					$this->sheet->setCellValue('R'.$row, $ctrl->getCode());
					$this->sheet->setCellValue('S'.$row, $ctrl->getDescription());
					$this->sheet->setCellValue('T'.$row, $ctrl->getDescription());
					$this->sheet->setCellValue('U'.$row, $ctrl->getTypeControle());
					$this->sheet->setCellValue('V'.$row, $ctrl->getMethodeControle());
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
				$this->sheet->setCellValue('R'.$row, $ctrl['code']);
				$this->sheet->setCellValue('S'.$row, $ctrl['name']);
				$this->sheet->setCellValue('T'.$row, $ctrl['description']);
				$this->sheet->setCellValue('U'.$row, $ctrl['type']);
				$this->sheet->setCellValue('V'.$row, $ctrl['methode']);
				if(isset($ctrl['pa'] )) {
					$pa=&$ctrl['pa'];
					$this->sheet->setCellValue('K'.$row, $pa->getCode());
					$this->sheet->setCellValue('L'.$row, $pa->getLibelle());
					$this->sheet->setCellValue('M'.$row, sprintf($pa->getPorteur()));
					$this->sheet->setCellValue('N'.$row, $pa->getDateDebut()->format('d-m-Y'));
					$this->sheet->setCellValue('O'.$row, $pa->getDateFin()->format('d-m-Y'));
					$this->sheet->setCellValue('P'.$row, $pa->getAvancementInText());
					$this->sheet->setCellValue('Q'.$row, sprintf($pa->getStatut()));
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
			$this->sheet->setCellValue('J'.$index, $cause['name']);
			$this->sheet->setCellValue('W'.$index, $probabilite);
			$this->sheet->setCellValue('X'.$index, $gravite);
			$this->sheet->setCellValue($column++.$index, $maturite);
			$this->sheet->setCellValue($column++.$index, $probabilite*$gravite);
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
						$this->sheet->setCellValue($domaine['column'].$row,$imp);
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
				$this->sheet->setCellValue('G'.$row, $cause['name']);
				$this->sheet->setCellValue('H'.$row, $cause['mode']);
				$this->sheet->setCellValue('L'.$row, $ctrl['description']);
				$this->sheet->setCellValue('M'.$row, $ctrl['type']);
				$this->sheet->setCellValue('N'.$row, $ctrl['methode']);
				if(isset($ctrl['pa'] )){
					$pa=$ctrl['pa'];
					$this->sheet->setCellValue('O'.$row, $pa->getLibelle());
					$this->sheet->setCellValue('Q'.$row, sprintf($pa->getPorteur()));
					$this->sheet->setCellValue('R'.$row, $pa->getDateFin()->format('d-m-Y'));
					$this->sheet->setCellValue('S'.$row, $pa->getAvancementInText());
				}
				$this->sheet->setCellValue('T'.$row, $cause['probabilite']);
				$row++;
			}
			if(isset($cause['pa'])) {
				foreach ($cause['pa'] as $pa){
					$this->extractByRisqueEnv($data, $row);
					$this->sheet->setCellValue('G'.$row, $cause['name']);
					$this->sheet->setCellValue('H'.$row, $cause['mode']);
					$this->sheet->setCellValue('O'.$row, $pa['name']);
					$this->sheet->setCellValue('Q'.$row, $pa['porteur']);
					$this->sheet->setCellValue('R'.$row, $pa['date_fin']);
					$this->sheet->setCellValue('S'.$row, $pa['statut']);
					if(isset($pa['ctrl'] )){
						$ctrl=&$pa['ctrl'];
						$this->sheet->setCellValue('L'.$row, $ctrl->getDescription());
						$this->sheet->setCellValue('M'.$row, sprintf($ctrl->getTypeControle()));
						$this->sheet->setCellValue('N'.$row, $ctrl->getMethodeControle()?$ctrl->getMethodeControle()->getLibelle():'');
					}
					$this->sheet->setCellValue('T'.$row, $cause['probabilite']);
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
		$this->sheet->setCellValue('A'.$row, $data['site']);
		$this->sheet->setCellValue('B'.$row, $data['domaine']);
		$this->sheet->setCellValue('C'.$row, '');
		$this->sheet->setCellValue('D'.$row, $data['equipement']);
		$this->sheet->setCellValue('E'.$row, $data['proprietaire']);
		$this->sheet->setCellValue('F'.$row, $data['code_risque']);
		$this->sheet->setCellValue('I'.$row, $data['risque']);
		$this->sheet->setCellValue('J'.$row, $data['lieu']);
		$this->sheet->setCellValue('K'.$row, $data['manifestation']);
		//$this->sheet->setCellValue('T'.$row, $data['probabilite']);
		$this->sheet->setCellValue('U'.$row, $data['gravite']);
		$this->sheet->setCellValue('V'.$row, $data['probabilite']*$data['gravite']);
		$this->sheet->setCellValue('W'.$row, ($data['probabilite']<3) ? (4-$data['probabilite']) : 1);
	}
}

