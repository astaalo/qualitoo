<?php
namespace App\Reporting;


class ExcelReporting extends \PHPExcel   {
	
	/**
	 * @param string $path
	 * @return \PHPExcel_Writer_IWriter
	 */
	public function save($path) {
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		ob_end_clean();
		$objWriter->save($path);
		return $objWriter;
	}
	
	/**
	 * @param string $filename
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function sendHeaders($filename) {
		$response = new \Symfony\Component\HttpFoundation\Response();
		$response->headers->set('Content-Disposition', sprintf('attachment;filename=%s.xlsx', $filename));
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$response->headers->set('Content-Transfer-Encoding', 'binary');
		$response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
		$response->headers->set('Expires', 0);
		return $response->sendHeaders();
	}
	
	/**
	 * @param string $path
	 * @param string $filename
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getResponseAfterSave($path, $filename) {
		$response = $this->sendHeaders($filename);
		$this->save($path);
		return $response;
	}
	
	/**
	 * @param array $columns
	 * @param string $defaultWidth
	 * @param array $columnsWidth
	 */
	protected function setDimensionColumns($columns = array(), $defaultWidth = null, $columnsWidth= array()) {
		foreach($columns as $column) {
			$this->getActiveSheet()->getColumnDimension($column)->setWidth($defaultWidth);
		}
		foreach($columnsWidth as $column => $width) {
			$this->getActiveSheet()->getColumnDimension($column)->setWidth($width);
		}
	}
	
	protected function setColors($columns, $color) {
		$this->getActiveSheet()->getStyle($columns)->applyFromArray(array(
						'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $color))
				));
	}
	
	/**
	 * @param integer $row
	 * @param array $data
	 */
	protected function setValues($row, $data = array()) {
		foreach($data as $column => $label) {
			$this->getActiveSheet()->setCellValue($column.$row, $label);
		}
	}
}
?>
