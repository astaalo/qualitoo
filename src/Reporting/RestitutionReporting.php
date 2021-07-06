<?php	
namespace App\Reporting;

class RestitutionReporting extends ExcelReporting {
	
	/**
	 * @param Binary $image
	 * @param array $societe
	 */
	public function extractMatriceSimple($image, $data, $rootDir) {
		$image = str_replace('data:image/png;base64,', '', $image);
		$decoded = base64_decode($image);
		$filename = sprintf("restitution_%s", date('YmdHis'));
		$path = sprintf("%s/../web/upload/restitution/%s.png", $rootDir, $filename);
		file_put_contents($path, $decoded, LOCK_EX);
		return sprintf("/upload/restitution/%s.png", $filename);
	}
	
}
