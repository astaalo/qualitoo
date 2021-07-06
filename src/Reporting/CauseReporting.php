<?php
namespace App\Reporting;

class CauseReporting extends ExcelReporting {
	
	/**
	 * @param array $data
	 */
	public function extractByCause($data) {
		$this->getActiveSheet()->setCellValue('A1', 'Famille');
		$this->getActiveSheet()->setCellValue('B1', 'Sous famille');
		$this->getActiveSheet()->setCellValue('C1', 'Cause');
		$this->getActiveSheet()->setCellValue('D1', 'Constat');
		$this->getActiveSheet()->setCellValue('E1', 'Probabilité');
		$row = 2;
		$this->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle('A1:C'.$this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		foreach($data as $famille) {
			$AFirst = $row;
			$this->getActiveSheet()->setCellValue('A'.$row, $famille['name']);
			foreach($famille['children'] as $sousFamille) {
				$BFirst = $row;
				$this->getActiveSheet()->setCellValue('B'.$row, $sousFamille['name']);
				foreach($sousFamille['cause'] as $cause) {
					$this->getActiveSheet()->setCellValue('C'.$row, $cause['name']);
					$this->getActiveSheet()->setCellValue('D'.$row, $cause['constat']);
					$this->getActiveSheet()->setCellValue('E'.$row, $cause['note']);
					$row = $row + 1;
				}
				$this->getActiveSheet()->mergeCells(sprintf('B%s:B%s', $BFirst, $row));
				$row = ($BFirst==$row) ? $row + 1 : $row;
			}
			foreach($famille['cause'] as $cause) {
				$this->getActiveSheet()->setCellValue('C'.$row, $cause['name']);
				$this->getActiveSheet()->setCellValue('D'.$row, $cause['constat']);
				$this->getActiveSheet()->setCellValue('E'.$row, $cause['note']);
				$row = $row + 1;
			}
			$this->getActiveSheet()->mergeCells(sprintf('A%s:A%s', $AFirst, ($AFirst==$row) ? $row : $row - 1));
			$row = ($AFirst==$row) ? $row + 1 : $row;
		}
		$this->getActiveSheet()->getColumnDimensionByColumn(0)->setWidth(30);
		$this->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth(50);
		$this->getActiveSheet()->getColumnDimensionByColumn(2)->setWidth(50);
		$this->getActiveSheet()->getColumnDimensionByColumn(3)->setWidth(20);
		$this->getActiveSheet()->getColumnDimensionByColumn(4)->setWidth(20);
		return $this;
	}
	
}
