<?php	
namespace App\Reporting;

use App\Entity\Activite;
use App\Entity\Criticite;
use App\Entity\Structure;
use PHPExcel_Style_Border;
// Include PhpSpreadsheet required namespaces
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
		$sheet->getStyle("A1:C1")->applyFromArray($styleEntete);
		
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
		
		$sheet->getDefaultStyle()->applyFromArray($styleGlobal);
		
		$this->setValues(1, array('A'=>$type, 'B'=>'Gravité', 'C'=>'Probabilité'));
		
		$row = 2;
		foreach($data as $key=>$value) {
			if($type=='structure')
				$sheet->setCellValue('A'.$row, ($value['name'] ?  $value['name']  : $value['libelle']));
			elseif($type=='site')
				$sheet->setCellValue('A'.$row, $value['libelle'].'('.$value['code'].')');
			elseif ($type=='activite')
				$sheet->setCellValue('A'.$row, ($value['code'] .'==>'.$value['libelle']));
			
			$sheet->setCellValue('B'.$row, intval($value['gravite']));
			$sheet->setCellValue('C'.$row, intval($value['proba']));
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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->setDimensionColumns($sheet, array('A'), 50);
        $this->setDimensionColumns($sheet, array('B', 'C', 'D'), 20);
        $sheet->setTitle("Prises en charges des risques");
        $styleEntete = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'ffffff'),
                'size'  => 12,
                'name'  => 'Verdana'
            ));
        $sheet->getStyle("A1:D1")->applyFromArray($styleEntete);

        if($type=='departement')
            $this->setValues($sheet,1, array('A'=>'Direction', 'B'=>'Département' ,'C'=>'Criticité', 'D'=>'Maturité'));
        elseif ($type=='direction')
            $this->setValues($sheet, 1, array('A'=>'Direction', 'B'=>'Criticité', 'C'=>'Maturité'));
        else
            $this->setValues($sheet, 1, array('A'=>'Code', 'B'=>'libelle' ,'C'=>'Criticité', 'D'=>'Maturité'));

        $row = 2;
        foreach($data as $key=>$value) {
            if($type=='departement'){
                $sheet->setCellValue('A'.$row, $value['direction']);
                $sheet->setCellValue('B'.$row, $value['code']);
                $sheet->setCellValue('C'.$row, isset($value['criticite'])?intval($value['criticite']):0);
                $sheet->setCellValue('D'.$row, intval($value['maturite']));
            }elseif($type=='direction'){
                $sheet->setCellValue('A'.$row, $value['name']);
                $sheet->setCellValue('B'.$row, intval($value['criticite']));
                $sheet->setCellValue('C'.$row, intval($value['maturite']));
            }else{
                $sheet->setCellValue('A'.$row, $value['code']);
                $sheet->setCellValue('B'.$row, $value['libelle']);
                $sheet->setCellValue('C'.$row, isset($value['criticite'])?intval($value['criticite']):0);
                $sheet->setCellValue('D'.$row, intval($value['maturite']));
            }
            $row++;
        }
        if($type=='direction'){
            $this->setColors($sheet,'A1', 'CCCCCC');
            $this->setColors($sheet, 'B1', '9164cd');
            $this->setColors($sheet, 'C1', 'ff6600');
        }else{
            $this->setColors($sheet, 'A1', 'CCCCCC');
            $this->setColors($sheet, 'B1', 'CCCCCC');
            $this->setColors($sheet, 'C1', '9164cd');
            $this->setColors($sheet, 'D1', 'ff6600');
        }
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), uniqid().'.xlsx');
        $writer->save($temp_file);
        return $temp_file;
	}
	

	 /**
	 * @param array $data
	 */
	public function extractRRC($data) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->setDimensionColumns($sheet, array('A'), 50);
        $this->setDimensionColumns($sheet, array('B', 'C', 'D', 'E'), 20);
        $sheet->getStyle("A1:E1")->applyFromArray($this->addStyleEntete());

        $sheet->setTitle("RRC");
        $this->setValues($sheet, 1, array('A'=>'Risque', 'B'=>'Probabilité' ,'C'=>'Gravité', 'D'=>'Criticité', 'E'=>'Maturité'));
        $row = 2;
        foreach($data as $key=>$value) {
            $sheet->setCellValue('A'.$row, $value['libelle']);
            $sheet->setCellValue('B'.$row, intval($value['probabilite']));
            $sheet->setCellValue('C'.$row, intval($value['gravite']));
            $sheet->setCellValue('D'.$row, intval($value['criticite']));
            $sheet->setCellValue('E'.$row, intval($value['maturite']));
            $row++;
        }
        $sheet->getStyle('A1:E'.($row-1))->applyFromArray($this->addAllBorder());
        $this->setColors($sheet, 'A1', 'CCCCCC');
        $this->setColors($sheet,'B1', 'ff6600');
        $this->setColors($sheet,'C1', '9164cd');
        $this->setColors($sheet,'D1', 'ff6600');
        $this->setColors($sheet,'E1', '9164cd');
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), uniqid().'.xlsx');
        $writer->save($temp_file);
        return $temp_file;
	}
	
	/**
	 * @param array $data
	 */
	public function extractRT($data){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->setDimensionColumns($sheet, array('A'), 50);
        $this->setDimensionColumns($sheet, array('B'), 20);
        $this->setValues($sheet, 1, array('A'=>'Risques', 'B'=>'Ocurrences' ));
        $sheet->getStyle("A1:B1")->applyFromArray($this->addStyleEntete());
        $sheet->setTitle("Risques Transverses");
        $row = 2;
        foreach($data as $key=>$value) {
            $sheet->setCellValue('A'.$row, $value['libelle']);
            $sheet->setCellValue('B'.$row, intval($value['occurence']));
            $row++;
        }
        $sheet->getStyle('A1:B'.($row-1))->applyFromArray($this->addAllBorder());
        $this->setColors($sheet, 'A1', 'CCCCCC');
        $this->setColors($sheet, 'B1', '9164cd');
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), uniqid().'.xlsx');
        $writer->save($temp_file);

        return $temp_file;
	}
	
	public function extractDetailsRTMetier($data,$em){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->setValues($sheet, 1, array('A'=>'Entité', 'B'=>'Sous-entité','C'=>'Activité' ,'D'=>'Criticité'));
        $sheet->setTitle("Detail RT Metier");

        $row = 2;
        foreach($data as $key=>$value) {
            $structure = $em->getRepository(Structure::class)->find($value->getStructreOrSite()->getId());
            $activite  = $em->getRepository(Activite::class)->find($value->getActivite()->getId());
            $criticite = $em->getRepository(Criticite::class)->find($value->getCriticite()->getId());
            $sheet->setCellValue('A'.$row, $structure->getCode());
            $sheet->setCellValue('B'.$row, explode('\\', $structure->getName())[0]);
            $sheet->setCellValue('C'.$row, $activite->getLibelle());
            $sheet->setCellValue('D'.$row, $value->getProbabilite().'*'.$value->getGravite() .'='.($value->getProbabilite()*$value->getGravite()).' ou '.$criticite);
            $row++;
        }
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), uniqid().'.xlsx');
        $writer->save($temp_file);
        return $temp_file;


        $this->setDimensionColumns(array('A','B','C'), 40);
		$this->setDimensionColumns(array('D'), 30);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
				));
		$sheet->getStyle("A1:D1")->applyFromArray($styleEntete);
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
		
		$sheet->getDefaultStyle()->applyFromArray($styleGlobal);
		
		$this->setValues(1, array('A'=>'Entité', 'B'=>'Sous-entité','C'=>'Activité' ,'D'=>'Criticité'));
		$row = 2;
		foreach($data as $key=>$value) {
			$structure = $em->getRepository('App\Entity\Structure')->find($value->getStructreOrSite()->getId());
			$activite  = $em->getRepository('App\Entity\Activite')->find($value->getActivite()->getId());
			$criticite = $em->getRepository('App\Entity\Criticite')->find($value->getCriticite()->getId());
			$sheet->setCellValue('A'.$row, $structure->getCode());
			$sheet->setCellValue('B'.$row, explode('\\', $structure->getName())[0]);
			$sheet->setCellValue('C'.$row, $activite->getLibelle());
			$sheet->setCellValue('D'.$row, $value->getProbabilite().'*'.$value->getGravite() .'='.($value->getProbabilite()*$value->getGravite()).' ou '.$criticite);
			$row++;
		}
		$this->setColors('A1:C1', 'CCCCCC');
		$this->setColors('D1', '9164cd');
		return $this;
		
	}
	
	public function extractDetailsRTEnv($data,$em){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle("Détail RT Environnemental");
        $this->setValues($sheet, 1, array('A'=>'Site', 'B'=>'Activité' ,'C'=>'Criticité'));
        $row = 2;
        foreach($data as $key=>$value) {
            $site = $em->getRepository('App\Entity\Site')->find($value->getStructreOrSite()->getId());
            $activite  = $em->getRepository('App\Entity\Activite')->find($value->getActivite()->getId());
            $criticite = $em->getRepository('App\Entity\Criticite')->find($value->getCriticite()->getId());
            $sheet->setCellValue('A'.$row, $site->getCode());
            $sheet->setCellValue('B'.$row, $activite->getLibelle());
            $sheet->setCellValue('C'.$row, $value->getProbabilite().'*'.$value->getGravite() .'='.($value->getProbabilite()*$value->getGravite()).' ou '.$criticite);
            $row++;
        }
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), uniqid().'.xlsx');
        $writer->save($temp_file);
        return $temp_file;

        
        
        // OLD
        /*$this->setDimensionColumns(array('A','B'), 40);
		$this->setDimensionColumns(array('C'), 30);
		$styleEntete = array(
				'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'ffffff'),
						'size'  => 12,
						'name'  => 'Verdana'
				));
		$sheet->getStyle("A1:D1")->applyFromArray($styleEntete);
		$styleGlobal=array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)));
	
		$sheet->getDefaultStyle()->applyFromArray($styleGlobal);
	
		$this->setValues(1, array('A'=>'Site', 'B'=>'Activité' ,'C'=>'Criticité'));
		$row = 2;
		foreach($data as $key=>$value) {
			$site = $em->getRepository('App\Entity\Site')->find($value->getStructreOrSite()->getId());
			$activite  = $em->getRepository('App\Entity\Activite')->find($value->getActivite()->getId());
			$criticite = $em->getRepository('App\Entity\Criticite')->find($value->getCriticite()->getId());
			$sheet->setCellValue('A'.$row, $site->getCode());
			$sheet->setCellValue('B'.$row, $activite->getLibelle());
			$sheet->setCellValue('C'.$row, $value->getProbabilite().'*'.$value->getGravite() .'='.($value->getProbabilite()*$value->getGravite()).' ou '.$criticite);
			$row++;
		}
		$this->setColors('A1:C1', 'CCCCCC');
		$this->setColors('D1', '9164cd');
		return $this;*/
	
	}

	/**
	 * @param array $data
	 */
	public function extractTPRC($data) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->setDimensionColumns($sheet, array('A', 'B'), 25);
        $this->setValues($sheet, 1, array('A'=>'Année', 'B'=>'% risques testés'));
        $sheet->getStyle("A1:B1")->applyFromArray($this->addStyleEntete());
        $sheet->setTitle("TPRC");
        $row = 2;
        foreach($data as $key=>$value) {
            $sheet->setCellValue('A'.$row, $value['annee']);
            $sheet->setCellValue('B'.$row, $value['risk_total']!=0 ?(($value['risk_test']/$value['risk_total'])*100).'%' :'0%');
            //$sheet->setCellValue('C'.$row, $value['ctrl_total']!=0 ?(($value['ctrl_test']/$value['ctrl_total'])*100).'%' :'0%');
            $row++;
        }
        $sheet->getStyle('A1:B'.($row-1))->applyFromArray($this->addAllBorder());
        $this->setColors($sheet,'A1', 'CCCCCC');
        $this->setColors($sheet,'B1', '9164cd');
        //$this->setColors($sheet,'C1', 'ff6600');
        // Create your Office 2007 Excel (XLSX Format)
        return $this->save($spreadsheet);
	}
	
	/**
	 * @param array $data
	 */
	public function extractCMC($data,$em) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->setDimensionColumns($sheet, array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'), 35);
        $this->setValues($sheet, 1, array('A'=>'Direction', 'B'=>'Département', 'C'=>'Activité','D'=>'Risque', 'E'=>'Code controle', 'F'=>'Libellé controle', 'G'=>'Maturité avant','H'=>'Maturité aprés'));
        $sheet->getStyle("A1:G1")->applyFromArray($this->addStyleEntete());
        $sheet->setTitle("Compar. Maturité des Contrôles");
        $row = 2;
        foreach($data as $key=>$value) {
            $cOr = $em->getRepository('App\Entity\RisqueHasCause')->find($value->getCauseOfRisque()->getId());
            $sheet->setCellValue('A'.$row, $cOr->getRisque()->getDirection() ? $cOr->getRisque()->getDirection()->getCode() : '');
            $sheet->setCellValue('B'.$row, $cOr->getRisque()->getStructreOrSite() ? $cOr->getRisque()->getStructreOrSite()->getCode() : '');
            $sheet->setCellValue('C'.$row, $cOr->getRisque()->getActivite());
            $sheet->setCellValue('D'.$row, $cOr->getRisque()->getMenace());
            $sheet->setCellValue('E'.$row, $value->getCode());
            $sheet->setCellValue('F'.$row, $value);
            $sheet->setCellValue('G'.$row, $value->getMaturiteTheorique());
            $sheet->setCellValue('H'.$row, $value->getMaturiteReel());
            $row++;
        }
        $sheet->getStyle('A1:H'.($row-1))->applyFromArray($this->addAllBorder());
        $this->setColors($sheet,'A1:F1', 'CCCCCC');
        $this->setColors($sheet,'G1', '9164cd');
        $this->setColors($sheet,'H1', 'ff6600');
        // Create your Office 2007 Excel (XLSX Format)
        return $this->save($spreadsheet);
	}
	
	/**
	 * @param array $data
	 */
	public function extractRAV($data,$em) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->setDimensionColumns($sheet, array('A'), 50);
        $this->setDimensionColumns($sheet, array('B', 'C', 'D', 'E', 'F'), 20);
        $this->setValues($sheet, 1, array('A'=> 'Période','B'=>'Risque', 'C'=>'Probabilité' ,'D'=>'Gravité', 'E'=>'Criticité', 'F'=>'Maturité'));
        $sheet->getStyle("A1:B1")->applyFromArray($this->addStyleEntete());
        $sheet->setTitle("Risques Avérés");
        $row = 2;
        foreach($data as $key=>$value) {
            $sheet->setCellValue('A'.$row, date_format($value['debut'], 'm-Y').'  _ '.date_format($value['fin'], 'm-Y'));
            $sheet->setCellValue('B'.$row, $value['menace']);
            $sheet->setCellValue('C'.$row, intval($value['probabilite']));
            $sheet->setCellValue('D'.$row, intval($value['gravite']));
            $sheet->setCellValue('E'.$row, intval($value['gravite'])*intval($value['probabilite']));
            $sheet->setCellValue('F'.$row, intval($value['maturite']));
            $row++;
        }
        $sheet->getStyle('A1:F'.($row-1))->applyFromArray($this->addAllBorder());
        $this->setColors($sheet, 'A1', 'CCCCCC');
        $this->setColors($sheet, 'B1', 'ff6600');
        $this->setColors($sheet, 'C1', '9164cd');
        $this->setColors($sheet, 'D1', 'ff6600');
        $this->setColors($sheet, 'E1', '9164cd');
        $this->setColors($sheet, 'F1', 'ff6600');
        // Create your Office 2007 Excel (XLSX Format)
        return $this->save($spreadsheet);
	}

	/**
	 * @param unknown $data
	 */
	public function extractEICG($data){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Evolution ICG par année");
        $this->setValues($sheet, 1, array('A'=>'Risque'));
        $sheet->mergeCells('A1:A2');
        $this->setColors($sheet, 'A1:A2', 'ff6600');
        $column = 'B';

        $styles = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => Border::BORDER_THIN, 'size'=>12,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            )
        );
        foreach ($data['global'] as $key => $value){
            $n = Coordinate::columnIndexFromString($column)+1;
            $to = Coordinate::stringFromColumnIndex(($n-2)+4);

            $pb = Coordinate::stringFromColumnIndex($n-2+1);
            $gr = Coordinate::stringFromColumnIndex($n-2+2);
            $cr = Coordinate::stringFromColumnIndex($n-2+3);
            $mt = Coordinate::stringFromColumnIndex($n-2+4);

            $sheet->setCellValue($column.'1', "Evaluation ".$key);
            $sheet->getStyle($column.'1')->applyFromArray($styles);
            $sheet->mergeCells($column.'1:'.$to.'1');
            $this->setColors($sheet, $column.'1:'.$to.'1', '33ccff');
            $this->setValues($sheet, 2, array($pb =>'Probabilité', $gr => 'Gravité', $cr=> 'Criticité', $mt=>'Maturité'));
            $sheet->getStyle($pb.'2')->applyFromArray($styles);
            $sheet->getStyle($gr.'2')->applyFromArray($styles);
            $sheet->getStyle($cr.'2')->applyFromArray($styles);
            $sheet->getStyle($mt.'2')->applyFromArray($styles);
            $this->setColors($sheet, $pb.'2'.':'.$mt.'2', 'ffcc00');
            $n = Coordinate::columnIndexFromString($to)+1;
            $column = Coordinate::stringFromColumnIndex($n);
        }
        $row = 3;
        foreach ($data['risque'] as $key => $value){
            $test = false;
            $column = 'A';

            foreach ($value as $cle => $val){
                if($test==false){
                    $sheet->setCellValue($column.$row, $val['libelle']);
                    $sheet->getStyle($column.$row)->applyFromArray($styles);
                    $test = true;
                }
                $column= $this->getColonne($column, 1);
                $sheet->setCellValue($column.$row, intval($val['probabilite']));
                $sheet->getStyle($column.$row)->applyFromArray($styles);
                $column= $this->getColonne($column, 1);
                $sheet->setCellValue($column.$row, intval($val['gravite']));
                $sheet->getStyle($column.$row)->applyFromArray($styles);
                $column= $this->getColonne($column, 1);
                $criticite = intval($val['gravite'])*intval($val['probabilite']);
                $sheet->setCellValue($column.$row, intval(round($criticite)));
                $sheet->getStyle($column.$row)->applyFromArray($styles);
                $color = $this->getColorCriticite($criticite);
                $this->setColors($sheet, $column.$row, $color);
                $column= $this->getColonne($column, 1);
                $sheet->setCellValue($column.$row, intval($val['maturite']));
                $sheet->getStyle($column.$row)->applyFromArray($styles);
            }
            $row++;
        }

        $test = false; $column = 'A';
        foreach ($data['global'] as $key => $value){
            if($test==false){
                $sheet->setCellValue($column.$row, 'CT(Somme des criticités des risques )');
                $this->setColors($sheet, $column.$row, 'e6e6e6');
                $sheet->getStyle($column.$row)->applyFromArray($styles);
                $column= $this->getColonne($column, 1);
                $test = true;
            }
            $sheet->setCellValue($column.$row, $value['ct']);
            $sheet->mergeCells($column.$row.':'.$this->getColonne($column, 3).$row);
            $sheet->getStyle($column.$row.':'.$this->getColonne($column, 3).$row)->applyFromArray($styles);
            $this->setColors($sheet, $column.$row.':'.$this->getColonne($column, 3).$row, 'e6e6e6');
            $column= $this->getColonne($column, 4);
        }$row++;

        $test = false; $column = 'A';
        foreach ($data['global'] as $key => $value){
            if($test==false){
                $sheet->setCellValue($column.$row, 'CMP(Criticité maximale possible )	');
                $this->setColors($sheet, $column.$row, 'e6e6e6');
                $sheet->getStyle($column.$row)->applyFromArray($styles);
                $column= $this->getColonne($column, 1);
                $test = true;
            }
            $sheet->setCellValue($column.$row, $value['cmp']);
            $sheet->mergeCells($column.$row.':'.$this->getColonne($column, 3).$row);
            $sheet->getStyle($column.$row.':'.$this->getColonne($column, 3).$row)->applyFromArray($styles);
            $this->setColors($sheet, $column.$row.':'.$this->getColonne($column, 3).$row, 'e6e6e6');
            $column= $this->getColonne($column, 4);
        }$row++;

        $test = false; $column = 'A';
        foreach ($data['global'] as $key => $value){
            if($test==false){
                $sheet->setCellValue($column.$row, 'ICG(Indice de Criticité Globale)');
                $this->setColors($sheet, $column.$row, 'e6e6e6');
                $sheet->getStyle($column.$row)->applyFromArray($styles);
                $column= $this->getColonne($column, 1);
                $test = true;
            }
            $sheet->setCellValue($column.$row, $value['icg']);
            $sheet->mergeCells($column.$row.':'.$this->getColonne($column, 3).$row);
            $sheet->getStyle($column.$row.':'.$this->getColonne($column, 3).$row)->applyFromArray($styles);
            $this->setColors($sheet, $column.$row.':'.$this->getColonne($column, 3).$row, 'e6e6e6');
            $column= $this->getColonne($column, 4);
        }$row++;
        // Create your Office 2007 Excel (XLSX Format)
        return $this->save($spreadsheet);
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
		$deb = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($colonne)+($compteur-1)+1);
		return $deb;
	}
	
}
