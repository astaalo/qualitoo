<?php
namespace App\Reporting;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PlanActionReporting extends ExcelReporting {
	
	/**
	 * @param array $data
	 */
	public function extract($data) {
        $spreadsheet = new Spreadsheet();
        $this->sheet = $spreadsheet->getActiveSheet();
        
		$this->sheet->setCellValue('A1', 'Code');
		$this->sheet->setCellValue('B1', 'Risque');
		$this->sheet->setCellValue('C1', 'Cause');
		$this->sheet->setCellValue('D1', 'Description');
		$this->sheet->setCellValue('E1', 'Porteur');
		$this->sheet->setCellValue('F1', 'Structure du porteur');
		$this->sheet->setCellValue('G1', 'Superviseur');
		$this->sheet->setCellValue('H1', 'Structure du superviseur');
		$this->sheet->setCellValue('I1', 'Statut');
		$this->sheet->setCellValue('J1', 'Date de début');
		$this->sheet->setCellValue('K1', 'Date de fin');
		$this->sheet->setCellValue('L1', 'Avancement');
		$row = 2;
		$this->sheet->getStyle('B1:D'.$this->sheet->getHighestRow())->getAlignment()->setWrapText(true);
		$this->sheet->getStyle('L1:L'.$this->sheet->getHighestRow())->getAlignment()->setWrapText(true);
		foreach($data as $value) {
			$this->sheet->setCellValue('A'.$row, $value['code']);
			$this->sheet->setCellValue('B'.$row, $value['risque']);
			$this->sheet->setCellValue('C'.$row, $value['cause']);
			$this->sheet->setCellValue('D'.$row, $value['description']);
			$this->sheet->setCellValue('E'.$row, $value['porteur']);
			$this->sheet->setCellValue('F'.$row, $value['structurePorteur']);
			$this->sheet->setCellValue('G'.$row, $value['superviseur']);
			$this->sheet->setCellValue('H'.$row, $value['structureSuperviseur']);
			$this->sheet->setCellValue('I'.$row, $value['statut']);
			$this->sheet->setCellValue('J'.$row, $value['echeance']);
			$this->sheet->setCellValue('K'.$row, $value['date_fin']);
			$this->sheet->setCellValue('L'.$row, $value['avancement']);
			$row = $row + 1;
		}
		$this->sheet->getColumnDimensionByColumn(1)->setWidth(30);
		$this->sheet->getColumnDimensionByColumn(2)->setWidth(40);
		$this->sheet->getColumnDimensionByColumn(3)->setWidth(50);
		$this->sheet->getColumnDimensionByColumn(11)->setWidth(70);
        // Create your Office 2007 Excel (XLSX Format)
        return $this->save($spreadsheet);
	}
	
}
