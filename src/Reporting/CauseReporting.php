<?php
namespace App\Reporting;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CauseReporting extends ExcelReporting {
	
	/**
	 * @param array $data
	 */
	public function extractByCause($data) {
        $spreadsheet = new Spreadsheet();
        $this->sheet = $spreadsheet->getActiveSheet();

        $this->sheet->setCellValue('A1', 'Famille');
		$this->sheet->setCellValue('B1', 'Sous famille');
		$this->sheet->setCellValue('C1', 'Cause');
		$this->sheet->setCellValue('D1', 'Constat');
		$this->sheet->setCellValue('E1', 'Probabilité');
		$row = 2;
		$this->sheet->getStyle('A2')->getAlignment()->setWrapText(true);
		$this->sheet->getStyle('A1:C'.$this->sheet->getHighestRow())->getAlignment()->setWrapText(true);
		foreach($data as $famille) {
			$AFirst = $row;
			$this->sheet->setCellValue('A'.$row, $famille['name']);
			foreach($famille['children'] as $sousFamille) {
				$BFirst = $row;
				$this->sheet->setCellValue('B'.$row, $sousFamille['name']);
				foreach($sousFamille['cause'] as $cause) {
					$this->sheet->setCellValue('C'.$row, $cause['name']);
					$this->sheet->setCellValue('D'.$row, $cause['constat']);
					$this->sheet->setCellValue('E'.$row, $cause['note']);
					$row = $row + 1;
				}
				$this->sheet->mergeCells(sprintf('B%s:B%s', $BFirst, $row));
				$row = ($BFirst==$row) ? $row + 1 : $row;
			}
			foreach($famille['cause'] as $cause) {
				$this->sheet->setCellValue('C'.$row, $cause['name']);
				$this->sheet->setCellValue('D'.$row, $cause['constat']);
				$this->sheet->setCellValue('E'.$row, $cause['note']);
				$row = $row + 1;
			}
			$this->sheet->mergeCells(sprintf('A%s:A%s', $AFirst, ($AFirst==$row) ? $row : $row - 1));
			$row = ($AFirst==$row) ? $row + 1 : $row;
		}
		$this->sheet->getColumnDimensionByColumn(0)->setWidth(30);
		$this->sheet->getColumnDimensionByColumn(1)->setWidth(50);
		$this->sheet->getColumnDimensionByColumn(2)->setWidth(50);
		$this->sheet->getColumnDimensionByColumn(3)->setWidth(20);
		$this->sheet->getColumnDimensionByColumn(4)->setWidth(20);
        // Create your Office 2007 Excel (XLSX Format)
        return $this->save($spreadsheet);
	}
	
}
