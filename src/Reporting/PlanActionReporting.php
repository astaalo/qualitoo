<?php
namespace App\Reporting;

class PlanActionReporting extends ExcelReporting {
	
	/**
	 * @param array $data
	 */
	public function extract($data) {
		$this->getActiveSheet()->setCellValue('A1', 'Code');
		$this->getActiveSheet()->setCellValue('B1', 'Risque');
		$this->getActiveSheet()->setCellValue('C1', 'Cause');
		$this->getActiveSheet()->setCellValue('D1', 'Description');
		$this->getActiveSheet()->setCellValue('E1', 'Porteur');
		$this->getActiveSheet()->setCellValue('F1', 'Structure du porteur');
		$this->getActiveSheet()->setCellValue('G1', 'Superviseur');
		$this->getActiveSheet()->setCellValue('H1', 'Structure du superviseur');
		$this->getActiveSheet()->setCellValue('I1', 'Statut');
		$this->getActiveSheet()->setCellValue('J1', 'Date de début');
		$this->getActiveSheet()->setCellValue('K1', 'Date de fin');
		$this->getActiveSheet()->setCellValue('L1', 'Avancement');
		$row = 2;
		$this->getActiveSheet()->getStyle('B1:D'.$this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle('L1:L'.$this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		foreach($data as $value) {
			$this->getActiveSheet()->setCellValue('A'.$row, $value['code']);
			$this->getActiveSheet()->setCellValue('B'.$row, $value['risque']);
			$this->getActiveSheet()->setCellValue('C'.$row, $value['cause']);
			$this->getActiveSheet()->setCellValue('D'.$row, $value['description']);
			$this->getActiveSheet()->setCellValue('E'.$row, $value['porteur']);
			$this->getActiveSheet()->setCellValue('F'.$row, $value['structurePorteur']);
			$this->getActiveSheet()->setCellValue('G'.$row, $value['superviseur']);
			$this->getActiveSheet()->setCellValue('H'.$row, $value['structureSuperviseur']);
			$this->getActiveSheet()->setCellValue('I'.$row, $value['statut']);
			$this->getActiveSheet()->setCellValue('J'.$row, $value['echeance']);
			$this->getActiveSheet()->setCellValue('K'.$row, $value['date_fin']);
			$this->getActiveSheet()->setCellValue('L'.$row, $value['avancement']);
			$row = $row + 1;
		}
		$this->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth(30);
		$this->getActiveSheet()->getColumnDimensionByColumn(2)->setWidth(40);
		$this->getActiveSheet()->getColumnDimensionByColumn(3)->setWidth(50);
		$this->getActiveSheet()->getColumnDimensionByColumn(11)->setWidth(70);
		return $this;
	}
	
}
