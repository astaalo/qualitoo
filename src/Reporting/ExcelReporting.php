<?php
namespace App\Reporting;


use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExcelReporting extends AbstractController   {
	protected function save($spreadsheet)
	{
		$writer = new Xlsx($spreadsheet);
		$temp_file = tempnam(sys_get_temp_dir(), uniqid().'.xlsx');
		$writer->save($temp_file);
		return $temp_file;
	}

	/**
	 * @param array $columns
	 * @param string $defaultWidth
	 * @param array $columnsWidth
	 */
	protected function setDimensionColumns($sheet, $columns = array(), $defaultWidth = null, $columnsWidth= array()) {
		foreach($columns as $column) {
			$sheet->getColumnDimension($column)->setWidth($defaultWidth);
		}
		foreach($columnsWidth as $column => $width) {
			$sheet->getColumnDimension($column)->setWidth($width);
		}
	}
	
	protected function setColors($sheet, $columns, $color) {
		$sheet
			->getStyle($columns)
			->getFill()
			->setFillType(Fill::FILL_SOLID)
			->getStartColor()->setARGB($color);
	}
	
	/**
	 * @param integer $row
	 * @param array $data
	 */
	protected function setValues($sheet, $row, $data = array()) {
		foreach($data as $column => $label) {
			$sheet->setCellValue($column.$row, $label);
		}
	}

	protected function addStyleEntete()
	{
		return array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'ffffff'),
			'size'  => 12,
			'name'  => 'Verdana'
		));
	}

	protected function addAllBorder()
	{
		return [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN //fine border
				]
			]
		];
	}
}
?>
