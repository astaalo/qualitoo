<?php	
namespace App\Reporting;

use PHPExcel_Style_Border;

class KpiReporting extends ExcelReporting {
	
	
	/**
	 * @param array $data
	 */
	public function extractRFI($data, $type) {
		$this->setDimensionColumns(array('A', 'B', 'C'), 50);
		$this->setDimensionColumns(array('B', 'C'), 20);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
     	 ));
		$this->getActiveSheet()->getStyle("A1:C1")->applyFromArray($styleEntete);
		
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
		
		$this->getActiveSheet()->getDefaultStyle()->applyFromArray($styleGlobal);
		
		$this->setValues(1, array('A'=>$type, 'B'=>'Gravité', 'C'=>'Probabilité'));
		
		$row = 2;
		foreach($data as $key=>$value) {
			if($type=='structure')
				$this->getActiveSheet()->setCellValue('A'.$row, ($value['name'] ?  $value['name']  : $value['libelle']));
			elseif($type=='site')
				$this->getActiveSheet()->setCellValue('A'.$row, $value['libelle'].'('.$value['code'].')');
			elseif ($type=='activite')
				$this->getActiveSheet()->setCellValue('A'.$row, ($value['code'] .'==>'.$value['libelle']));
			
			$this->getActiveSheet()->setCellValue('B'.$row, intval($value['gravite']));
			$this->getActiveSheet()->setCellValue('C'.$row, intval($value['proba']));
			$row++;
		}
		$this->setColors('A1', 'CCCCCC');
		$this->setColors('B1', '9164cd');
		$this->setColors('C1', 'ff6600');
		return $this;
	}
	
	/**
	 * @param array $data
	 */
	public function extractRCC($data, $type) {
		$this->setDimensionColumns(array('A'), 50);
		$this->setDimensionColumns(array('B', 'C'), 20);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
     	 ));
		$this->getActiveSheet()->getStyle("A1:C1")->applyFromArray($styleEntete);
		
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
		
		$this->getActiveSheet()->getDefaultStyle()->applyFromArray($styleGlobal);
		
		if($type=='departement')
				$this->setValues(1, array('A'=>'Direction', 'B'=>'Département' ,'C'=>'Criticité', 'D'=>'Maturité'));
		elseif ($type=='direction')
				$this->setValues(1, array('A'=>'Direction', 'B'=>'Criticité', 'C'=>'Maturité'));
		else
			$this->setValues(1, array('A'=>'Code', 'B'=>'libelle' ,'C'=>'Criticité', 'D'=>'Maturité'));
		
		$row = 2;
		foreach($data as $key=>$value) {
			if($type=='departement'){
				$this->getActiveSheet()->setCellValue('A'.$row, $value['direction']);
				$this->getActiveSheet()->setCellValue('B'.$row, $value['code']);
				$this->getActiveSheet()->setCellValue('C'.$row, isset($value['criticite'])?intval($value['criticite']):0);
				$this->getActiveSheet()->setCellValue('D'.$row, intval($value['maturite']));
			}elseif($type=='direction'){
				$this->getActiveSheet()->setCellValue('A'.$row, $value['name']);
				$this->getActiveSheet()->setCellValue('B'.$row, intval($value['criticite']));
				$this->getActiveSheet()->setCellValue('C'.$row, intval($value['maturite']));
			}else{
				$this->getActiveSheet()->setCellValue('A'.$row, $value['code']);
				$this->getActiveSheet()->setCellValue('B'.$row, $value['libelle']);
				$this->getActiveSheet()->setCellValue('C'.$row, intval($value['criticite']));
				$this->getActiveSheet()->setCellValue('D'.$row, intval($value['maturite']));
			}
			$row++;
		}
		if($type=='direction'){
				$this->setColors('A1', 'CCCCCC');
				$this->setColors('B1', '9164cd');
				$this->setColors('C1', 'ff6600');
		}else{
				$this->setColors('A1', 'CCCCCC');
				$this->setColors('B1', 'CCCCCC');
				$this->setColors('C1', '9164cd');
				$this->setColors('D1', 'ff6600');
		}
		return $this;
	}
	

	 /**
	 * @param array $data
	 */
	public function extractRRC($data) {
		$this->setDimensionColumns(array('A'), 50);
		$this->setDimensionColumns(array('B', 'C', 'D', 'E'), 20);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
     	 ));
		$this->getActiveSheet()->getStyle("A1:E1")->applyFromArray($styleEntete);
		
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
		$this->setValues(1, array('A'=>'Risque', 'B'=>'Probabilité' ,'C'=>'Gravité', 'D'=>'Criticité', 'E'=>'Maturité'));
		$this->getActiveSheet()->getDefaultStyle()->applyFromArray($styleGlobal);
		
		$row = 2;
		foreach($data as $key=>$value) {
				$this->getActiveSheet()->setCellValue('A'.$row, $value['libelle']);
				$this->getActiveSheet()->setCellValue('B'.$row, intval($value['probabilite']));
				$this->getActiveSheet()->setCellValue('C'.$row, intval($value['gravite']));
				$this->getActiveSheet()->setCellValue('D'.$row, intval($value['criticite']));
				$this->getActiveSheet()->setCellValue('E'.$row, intval($value['maturite']));
				$row++;
		}
		$this->setColors('A1', 'CCCCCC');
		$this->setColors('B1', 'ff6600');
		$this->setColors('C1', '9164cd');
		$this->setColors('D1', 'ff6600');
		$this->setColors('E1', '9164cd');
		return $this;
	}
	
	/**
	 * @param array $data
	 */
	public function extractRT($data){
		$this->setDimensionColumns(array('A'), 50);
		$this->setDimensionColumns(array('B'), 20);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
				));
		$this->getActiveSheet()->getStyle("A1:C1")->applyFromArray($styleEntete);
		
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
		
		$this->getActiveSheet()->getDefaultStyle()->applyFromArray($styleGlobal);
		
		$this->setValues(1, array('A'=>'Risques', 'B'=>'Ocurrences' ));
		
				$row = 2;
		foreach($data as $key=>$value) {
				$this->getActiveSheet()->setCellValue('A'.$row, $value['libelle']);
				$this->getActiveSheet()->setCellValue('B'.$row, intval($value['occurence']));
			$row++;
		}
		$this->setColors('A1', 'CCCCCC');
		$this->setColors('B1', '9164cd');
		return $this;
	}
	
	public function extractDetailsRTMetier($data,$em){
		$this->setDimensionColumns(array('A','B','C'), 40);
		$this->setDimensionColumns(array('D'), 30);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
				));
		$this->getActiveSheet()->getStyle("A1:D1")->applyFromArray($styleEntete);
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
		
		$this->getActiveSheet()->getDefaultStyle()->applyFromArray($styleGlobal);
		
		$this->setValues(1, array('A'=>'Entité', 'B'=>'Sous-entité','C'=>'Activité' ,'D'=>'Criticité'));
		$row = 2;
		foreach($data as $key=>$value) {
			$structure = $em->getRepository('OrangeMainBundle:Structure')->find($value->getStructreOrSite()->getId());
			$activite  = $em->getRepository('OrangeMainBundle:Activite')->find($value->getActivite()->getId());
			$criticite = $em->getRepository('OrangeMainBundle:Criticite')->find($value->getCriticite()->getId());
			$this->getActiveSheet()->setCellValue('A'.$row, $structure->getCode());
			$this->getActiveSheet()->setCellValue('B'.$row, explode('\\', $structure->getName())[0]);
			$this->getActiveSheet()->setCellValue('C'.$row, $activite->getLibelle());
			$this->getActiveSheet()->setCellValue('D'.$row, $value->getProbabilite().'*'.$value->getGravite() .'='.($value->getProbabilite()*$value->getGravite()).' ou '.$criticite);
			$row++;
		}
		$this->setColors('A1:C1', 'CCCCCC');
		$this->setColors('D1', '9164cd');
		return $this;
		
	}
	
	public function extractDetailsRTEnv($data,$em){
		$this->setDimensionColumns(array('A','B'), 40);
		$this->setDimensionColumns(array('C'), 30);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
				));
		$this->getActiveSheet()->getStyle("A1:D1")->applyFromArray($styleEntete);
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
	
		$this->getActiveSheet()->getDefaultStyle()->applyFromArray($styleGlobal);
	
		$this->setValues(1, array('A'=>'Site', 'B'=>'Activité' ,'C'=>'Criticité'));
		$row = 2;
		foreach($data as $key=>$value) {
			$site = $em->getRepository('OrangeMainBundle:Site')->find($value->getStructreOrSite()->getId());
			$activite  = $em->getRepository('OrangeMainBundle:Activite')->find($value->getActivite()->getId());
			$criticite = $em->getRepository('OrangeMainBundle:Criticite')->find($value->getCriticite()->getId());
			$this->getActiveSheet()->setCellValue('A'.$row, $site->getCode());
			$this->getActiveSheet()->setCellValue('B'.$row, $activite->getLibelle());
			$this->getActiveSheet()->setCellValue('C'.$row, $value->getProbabilite().'*'.$value->getGravite() .'='.($value->getProbabilite()*$value->getGravite()).' ou '.$criticite);
			$row++;
		}
		$this->setColors('A1:C1', 'CCCCCC');
		$this->setColors('D1', '9164cd');
		return $this;
	
	}

	/**
	 * @param array $data
	 */
	public function extractTPRC($data) {
		$this->setDimensionColumns(array('A', 'B', 'C'), 20);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
				));
		$this->getActiveSheet()->getStyle("A1:C1")->applyFromArray($styleEntete);
	
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
	
		$this->getActiveSheet()->getDefaultStyle()->applyFromArray($styleGlobal);
	
		$this->setValues(1, array('A'=>'Année', 'B'=>'% risques testés', 'C'=>'% controles testés'));
	
		$row = 2;
		
		
		foreach($data as $key=>$value) {
				$this->getActiveSheet()->setCellValue('A'.$row, $value['annee']);
				$this->getActiveSheet()->setCellValue('B'.$row, $value['risk_total']!=0 ?(($value['risk_test']/$value['risk_total'])*100).'%' :'0%');
				$this->getActiveSheet()->setCellValue('C'.$row, $value['ctrl_total']!=0 ?(($value['ctrl_test']/$value['ctrl_total'])*100).'%' :'0%');
				$row++;
		}
		$this->setColors('A1', 'CCCCCC');
		$this->setColors('B1', '9164cd');
		$this->setColors('C1', 'ff6600');
		return $this;
	}
	
	/**
	 * @param array $data
	 */
	public function extractCMC($data,$em) {
		$this->setDimensionColumns(array('A', 'B', 'C','D','E','F','G','H'), 35);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
				));
		$this->getActiveSheet()->getStyle("A1:H1")->applyFromArray($styleEntete);
	
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
	
		$this->getActiveSheet()->getDefaultStyle()->applyFromArray($styleGlobal);
	
		$this->setValues(1, array('A'=>'Direction', 'B'=>'Département', 'C'=>'Activité','D'=>'Risque', 'E'=>'Code controle', 'F'=>'Libellé controle', 'G'=>'Maturité avant','H'=>'Maturité aprés'));
		$row = 2;
		foreach($data as $key=>$value) {
			$cOr = $em->getRepository('OrangeMainBundle:RisqueHasCause')->find($value->getCauseOfRisque()->getId());
			$this->getActiveSheet()->setCellValue('A'.$row, $cOr->getRisque()->getDirection()->getCode());
			$this->getActiveSheet()->setCellValue('B'.$row, $cOr->getRisque()->getStructreOrSite()->getCode());
			$this->getActiveSheet()->setCellValue('C'.$row, $cOr->getRisque()->getActivite());
			$this->getActiveSheet()->setCellValue('D'.$row, $cOr->getRisque()->getMenace());
			$this->getActiveSheet()->setCellValue('E'.$row, $value->getCode());
			$this->getActiveSheet()->setCellValue('F'.$row, $value);
			$this->getActiveSheet()->setCellValue('G'.$row, $value->getMaturiteTheorique());
			$this->getActiveSheet()->setCellValue('H'.$row, $value->getMaturiteReel());
			$row++;
		}
		$this->setColors('A1:F1', 'CCCCCC');
		$this->setColors('G1', '9164cd');
		$this->setColors('H1', 'ff6600');
		return $this;
	}
	
	/**
	 * @param array $data
	 */
	public function extractRAV($data,$em) {
		$this->setDimensionColumns(array('A'), 50);
		$this->setDimensionColumns(array('B', 'C', 'D', 'E'), 20);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
				));
		$this->getActiveSheet()->getStyle("A1:E1")->applyFromArray($styleEntete);
	
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
		$this->setValues(1, array('A'=> 'Période','B'=>'Risque', 'C'=>'Probabilité' ,'D'=>'Gravité', 'E'=>'Criticité', 'F'=>'Maturité'));
		$this->getActiveSheet()->getDefaultStyle()->applyFromArray($styleGlobal);
	
		$row = 2;
		foreach($data as $key=>$value) {
			$this->getActiveSheet()->setCellValue('A'.$row, date_format($value['debut'], 'm-Y').'  _ '.date_format($value['fin'], 'm-Y'));
			$this->getActiveSheet()->setCellValue('B'.$row, $value['menace']);
			$this->getActiveSheet()->setCellValue('C'.$row, intval($value['probabilite']));
			$this->getActiveSheet()->setCellValue('D'.$row, intval($value['gravite']));
			$this->getActiveSheet()->setCellValue('E'.$row, intval($value['gravite'])*intval($value['probabilite']));
			$this->getActiveSheet()->setCellValue('F'.$row, intval($value['maturite']));
			$row++;
		}
		$this->setColors('A1', 'CCCCCC');
		$this->setColors('B1', 'ff6600');
		$this->setColors('C1', '9164cd');
		$this->setColors('D1', 'ff6600');
		$this->setColors('E1', '9164cd');
		$this->setColors('F1', 'ff6600');
		return $this;
	}

	/**
	 * @param unknown $data
	 */
	public function extractEICG($data){
		$styles_borders = array('style'=>\PHPExcel_Style_Border::BORDER_THIN, 'size'=>12, 'color'=>array('rgb'=>'000000'));
		$styles = array(
				'borders' => array( 'top'=> $styles_borders,
									'bottom'=> $styles_borders,
									'left'=> $styles_borders,
									'right'=> $styles_borders),
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				)
		);
		
		$this->setDimensionColumns(array('A'), 60);
		$this->setValues(1, array('A'=>'Risque'));
		$this->getActiveSheet()->mergeCells('A1:A2');
		$this->setColors('A1:A2', 'ff6600');
		$column = 'B';
		foreach ($data['global'] as $key => $value){
			$n = \PHPExcel_Cell::columnIndexFromString($column);
			$to = \PHPExcel_Cell::stringFromColumnIndex(($n-2)+4);
			
			$pb = \PHPExcel_Cell::stringFromColumnIndex($n-2+1);
			$gr = \PHPExcel_Cell::stringFromColumnIndex($n-2+2);
			$cr = \PHPExcel_Cell::stringFromColumnIndex($n-2+3);
			$mt = \PHPExcel_Cell::stringFromColumnIndex($n-2+4);
			
			$this->getActiveSheet()->setCellValue($column.'1', "Evaluation ".$key);
			$this->getActiveSheet()->getStyle($column.'1')->applyFromArray($styles);
			$this->getActiveSheet()->mergeCells($column.'1:'.$to.'1');
			$this->setColors($column.'1:'.$to.'1', '33ccff');
			$this->setValues(2, array($pb =>'Probabilité', $gr => 'Gravité', $cr=> 'Criticité', $mt=>'Maturité'));
			$this->getActiveSheet()->getStyle($pb.'2')->applyFromArray($styles);
			$this->getActiveSheet()->getStyle($gr.'2')->applyFromArray($styles);
			$this->getActiveSheet()->getStyle($cr.'2')->applyFromArray($styles);
			$this->getActiveSheet()->getStyle($mt.'2')->applyFromArray($styles);
			$this->setColors($pb.'2'.':'.$mt.'2', 'ffcc00');
			$n = \PHPExcel_Cell::columnIndexFromString($to);
			$column = \PHPExcel_Cell::stringFromColumnIndex($n);
		}
		$row = 3;
		foreach ($data['risque'] as $key => $value){
			$test = false;
			$column = 'A';
			
			foreach ($value as $cle => $val){
				if($test==false){
					$this->getActiveSheet()->setCellValue($column.$row, $val['libelle']);
					$this->getActiveSheet()->getStyle($column.$row)->applyFromArray($styles);
					$test = true;
				}
				$column= $this->getColonne($column, 1);
				$this->getActiveSheet()->setCellValue($column.$row, intval($val['probabilite']));
				$this->getActiveSheet()->getStyle($column.$row)->applyFromArray($styles);
				$column= $this->getColonne($column, 1);
				$this->getActiveSheet()->setCellValue($column.$row, intval($val['gravite']));
				$this->getActiveSheet()->getStyle($column.$row)->applyFromArray($styles);
				$column= $this->getColonne($column, 1);
				$criticite = intval($val['gravite'])*intval($val['probabilite']);
				$this->getActiveSheet()->setCellValue($column.$row, intval(round($criticite)));
				$this->getActiveSheet()->getStyle($column.$row)->applyFromArray($styles);
				$color = $this->getColorCriticite($criticite);
				$this->setColors($column.$row, $color);
				$column= $this->getColonne($column, 1);
				$this->getActiveSheet()->setCellValue($column.$row, intval($val['maturite']));
				$this->getActiveSheet()->getStyle($column.$row)->applyFromArray($styles);
			}
			$row++;
		}
		
		$test = false; $column = 'A';
		foreach ($data['global'] as $key => $value){
			if($test==false){
				$this->getActiveSheet()->setCellValue($column.$row, 'CT(Somme des criticités des risques )');
				$this->setColors($column.$row, 'e6e6e6');
				$this->getActiveSheet()->getStyle($column.$row)->applyFromArray($styles);
				$column= $this->getColonne($column, 1);
				$test = true;
			}
			$this->getActiveSheet()->setCellValue($column.$row, $value['ct']);
			$this->getActiveSheet()->mergeCells($column.$row.':'.$this->getColonne($column, 3).$row);
			$this->getActiveSheet()->getStyle($column.$row.':'.$this->getColonne($column, 3).$row)->applyFromArray($styles);
			$this->setColors($column.$row.':'.$this->getColonne($column, 3).$row, 'e6e6e6');
			$column= $this->getColonne($column, 4);
		}$row++;
		
		$test = false; $column = 'A';
		foreach ($data['global'] as $key => $value){
			if($test==false){
				$this->getActiveSheet()->setCellValue($column.$row, 'CMP(Criticité maximale possible )	');
				$this->setColors($column.$row, 'e6e6e6');
				$this->getActiveSheet()->getStyle($column.$row)->applyFromArray($styles);
				$column= $this->getColonne($column, 1);
				$test = true;
			}
			$this->getActiveSheet()->setCellValue($column.$row, $value['cmp']);
			$this->getActiveSheet()->mergeCells($column.$row.':'.$this->getColonne($column, 3).$row);
			$this->getActiveSheet()->getStyle($column.$row.':'.$this->getColonne($column, 3).$row)->applyFromArray($styles);
			$this->setColors($column.$row.':'.$this->getColonne($column, 3).$row, 'e6e6e6');
			$column= $this->getColonne($column, 4);
		}$row++;
		
		$test = false; $column = 'A';
		foreach ($data['global'] as $key => $value){
			if($test==false){
				$this->getActiveSheet()->setCellValue($column.$row, 'ICG(Indice de Criticité Globale)');
				$this->setColors($column.$row, 'e6e6e6');
				$this->getActiveSheet()->getStyle($column.$row)->applyFromArray($styles);
				$column= $this->getColonne($column, 1);
				$test = true;
			}
			$this->getActiveSheet()->setCellValue($column.$row, $value['icg']);
			$this->getActiveSheet()->mergeCells($column.$row.':'.$this->getColonne($column, 3).$row);
			$this->getActiveSheet()->getStyle($column.$row.':'.$this->getColonne($column, 3).$row)->applyFromArray($styles);
			$this->setColors($column.$row.':'.$this->getColonne($column, 3).$row, 'e6e6e6');
			$column= $this->getColonne($column, 4);
		}$row++;
		
		return $this;
	}
	
	
	public function getColorCriticite($criticite){
		$colors =array ('red'=> 'ff4000', 'yellow'=> 'ffff00', 'orange'=> 'ff6600', 'green'=> '00ff00');
		if($criticite<=3)
			return $colors['green'];
		elseif ($criticite<=6)
			return $colors['yellow'];
		elseif ($criticite<=9)
			return $colors['orange'];
		elseif ($criticite>9)
			return $colors['red'];
	}
	
	/**
	 * @param unknown $colonne
	 * @param unknown $compteur
	 */
	public function getColonne($colonne, $compteur){
		$deb = \PHPExcel_Cell::stringFromColumnIndex(\PHPExcel_Cell::columnIndexFromString($colonne)+($compteur-1));
		return $deb;
	}
	
}
