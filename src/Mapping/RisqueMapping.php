<?php
namespace App\Mapping;

use App\Entity\Risque;

class RisqueMapping extends BaseMapping {
	
	/**
	 * @param array $result
	 * @return array
	 */
	public function mapForExportProjet($result, $cartographie) {
		$data = array('carto'=>array(), 'domaine'=>array());
		foreach ($cartographie->getDomaine() as $domaine){
			$data['domaine'][$domaine->getId()]=array('name' => $domaine->getLibelle(), 'column' => null);
		}
		foreach($result as $risque) {
			$this->putProcessusInArch($risque->getArchProcessus(), $data['carto']);
			$this->putActiviteAndMeInArch($risque->getArchProcessus(), $data['carto'], $data['domaine'], $risque);
		}
		return $data;
	}
	
	
	/**
	 * @param array $result
	 * @return array
	 */
	public function mapForExportMetier($result, $cartographie) {
		$data = array('carto'=>array(), 'domaine'=>array());
		foreach ($cartographie->getDomaine() as $domaine) {
			$data['domaine'][$domaine->getId()]=array('name' => $domaine->getLibelle(), 'column' => null);
			foreach($result as $risque) {
				$this->putProcessusInArch($risque->getArchProcessus(), $data['carto']);
				$this->putActiviteAndMeInArch($risque->getArchProcessus(), $data['carto'], $data['domaine'], $risque);
			}
		}
		return $data;
	}
	
	/**
	 * @param array $result
	 * @return array
	 */
	public function mapForExportSST($result,$cartographie) {
		$data = array();
		foreach($result as $risque) {
			$this->putComplementInRisque($risque->getRisqueSST(), $data);
			$this->putCauseInArch($data[$risque->getId()], $risque);
		}
		return $data;
	}
	
	/**
	 * @param array $result
	 * @return array
	 */
	public function mapForExportEnvironnemental($result,$cartographie) {
		$data = array();
		foreach($result as $risque) {
			$this->putComplementInRisque($risque->getRisqueEnvironnemental(), $data);
			$this->putCauseInArch($data[$risque->getId()], $risque);
		}
		return $data;
	}
	

	/**
	 * @param array $result
	 * @return array
	 */
	public function mapForBasicExport($result) {
		$data = array('carto'=>array(), 'domaine'=>array());
		foreach($result as $risque) {
			$this->putProcessusInArch($risque->getArchProcessus(), $data['carto']);
			$this->putActiviteAndMeInArch($risque->getArchProcessus(), $data['carto'], $data['domaine'], $risque);
		}
		return $data;
	}

	/**
	 * @param array $result
	 * @return array
	 */
	public function mapForExport($result) {
		$data = array();
		foreach($result as $risque) {
			$this->putComplementInRisque($risque, $data);
			$this->putCauseInArch($data[$risque->getId()], $risque);
		}
		return $data;
	}
	
	/**
	 * @param \App\Entity\Risque $risque
	 * @param array $data
	 */
	private function putComplementInRisque($risque, &$data) {
		$data[$risque->getRisque()->getId()] = array(
			'site' 			=> sprintf('%s', $risque->getSite()),
			'domaine'		=> sprintf('%s', $risque->getDomaineActivite()),
			'equipement'	=> sprintf('%s', $risque->getEquipement()),
			'theme'			=> sprintf('%s', '--'),
			'domaine_site'	=> sprintf('%s', '--'),
			'lieu'			=> sprintf('%s', $risque->getLieu()),
			'manifestation'	=> sprintf('%s', $risque->getManifestation()),
			'proprietaire'	=> sprintf('%s', $risque->getProprietaire()),
			'code_risque'	=> sprintf('%s', $risque->getRisque()->getCode()),
			'proprietaire'	=> sprintf('%s', $risque->getProprietaire()),
			'risque'		=> sprintf('%s', $risque->getRisque()),
			'probabilite'	=> $risque->getRisque()->getProbabilite(),
			'gravite'		=> $risque->getRisque()->getGravite()
		);
	}
	
	/**
	 * @param array $archRisque
	 * @param array $data
	 */
	private function putProcessusInArch($archRisque, &$data) {
		foreach($archRisque as $key => $value) {
			if(isset($data[$key])) {
				$this->putProcessusInArch($value['children'], $data[$key]['children']);
			} else {
				$data[$key] = $value;
			}
		}
	}

	/**
	 * @param array $archRisque
	 * @param array $data
	 * @param array $domaine
	 * @param \App\Entity\Risque $risque
	 */
	private function putActiviteAndMeInArch($archRisque, &$data, &$domaine, $risque) {
        foreach($archRisque as $key => $value) {
			if(count($value['children'])==0) {
				$activiteId = $risque->getActivite()->getId();
				$risqueId = $risque->getId();
				if(!isset($data[$key]['activite'][$activiteId])) {
					$data[$key]['activite'][$activiteId] = array('name'=>$risque->getActivite()->getLibelle(), 'code'=>$risque->getActivite()->getCode() ,'risque'=>array());
				}
				$data[$key]['activite'][$activiteId]['risque'][$risqueId] = array(
					'name'=> $risque->getMenace() ? $risque->getMenace()->getLibelle() : null, 'cause'=>array(), 'impact'=>array(),'probabilite'=>$risque->getProbabilite(), 'gravite'=>$risque->getGravite(), 'code'=>$risque->getCode()
				);
				$this->putCauseInArch($data[$key]['activite'][$activiteId]['risque'][$risqueId], $risque);
				$this->putImpactInArch($data[$key]['activite'][$activiteId]['risque'][$risqueId], $domaine, $risque);
				break;
			}
			$this->putActiviteAndMeInArch($value['children'], $data[$key]['children'], $domaine, $risque);
		}
	}

	/**
	 * @param array $data
	 * @param \App\Entity\Risque $risque
	 */
	private function putCauseInArch(&$data, $risque) {	
		foreach($risque->getCauseOfRisque() as $cor) {
			$causeId = $cor->getCause()->getId();
			$controle = $cor->getControle();
			$pa = $cor->getPlanAction();
			if(!isset($data['cause'][$causeId])) {
				$data['cause'][$causeId] = array(
					'name'=>$cor->getCause()->getLibelle(), 
					'famille'=> '-', 'probabilite' => $cor->getProbabilite(),
					'controle'=>array(), 'pa'=>array(),
					'mode' => $cor->getModeFonctionnement() ? $cor->getModeFonctionnement()->__toString() : ''
				);
				if($controle) {
					foreach ($controle as $ctrl){
						$data['cause'][$causeId]['controle'][$ctrl->getId()] = array(
							'id'=>$ctrl->getId(),
							'code'=>$ctrl->getCode(), 'name'=>$ctrl->getDescription(), 'description'=>$ctrl->getDescription(),
							'type'=>sprintf($ctrl->getTypeControle()), 'methode'=>sprintf($ctrl->getMethodeControle()),
							'pa'=>$ctrl->getToPlanAction()
						);
					}
				}
				if($pa) {
					foreach ($pa as $p){
						$data['cause'][$causeId]['pa'][$p->getId()] = array(
							'code'=>$p->getCode(), 'name'=>$p->getLibelle(), 'description'=>$p->getDescription(), 'porteur'=>sprintf($p->getPorteur()), 
							'statut'=>sprintf($p->getStatut()), 'date_debut' => $p->getDateDebut()?$p->getDateDebut()->format('d-m-Y'):'', 'date_fin' => $p->getDateFin()?$p->getDateFin()->format('d-m-Y'):'', 'avancement' => $p->getAvancementInText(),
							'ctrl'=>$p->getToControle()
						);
					}
				}
			}
		}
	}

	/**
	 * @param array $data
	 * @param array $domaine
	 * @param \App\Entity\Risque $risque
	 */
	private function putImpactInArch(&$data, &$domaine, $risque) {
		foreach($risque->getImpactOfRisque() as $ior) {
			$impactId = $ior->getImpact()->getId();
			$domaineId = $ior->getImpact() && $ior->getImpact()->getCritere()? $ior->getImpact()->getCritere()->getDomaine()->getId():null;
			if(!isset($domaine[$domaineId])&&$domaineId!=null) {
				$domaine[$domaineId] = array('name' => $ior->getDomaine()->getLibelle(), 'column' => null);
			}
			if(!isset($data['impact'][$impactId])) {
				$data['impact'][$impactId] = array('domaine' => array());
			}
			$data['impact'][$impactId]['domaine'][$domaineId] = $ior->getGrille() ? $ior->getGrille()->getValeur() : null;
		}
	}
	
	/****** MAPPING FOR KPIS********/
	
	public function calculGraviteByTop(&$arrData){
		$moyByRisqueStructure= array();
		foreach ($arrData as $key => $value){
			if(! isset($moyByRisqueStructure [$value['id']])) {
				$moyByRisqueStructure [$value['id']] = array('menace'=>$value['menace'],'libelle'=> $value['libelle'],'count'=> 1 ,'somme'=> intval($value['gravite']), 'gravite' =>0);
			}
			else{
				$moyByRisqueStructure [$value['id']]['count'] = intval($moyByRisqueStructure [$value['id']]['count'])+1;
				$moyByRisqueStructure [$value['id']]['somme'] = intval($moyByRisqueStructure [$value['id']]['somme']) + intval($value['gravite']);
			}
		}
		
		foreach ($moyByRisqueStructure as $key => $value){
			$moyByRisqueStructure[$key]['gravite'] = round($value['somme'] /$value['count']);
		}
		
		$moyByStructure = array();
		foreach ($moyByRisqueStructure as $cle => $value){
			if(! isset($moyByStructure [$cle])){
				$moyByStructure [$cle] = array('libelle'=> $value['libelle'],'count'=> 1 ,'somme'=> intval($value['gravite']), 'gravite'=>0);
			}
			else{
				$moyByStructure [$value['id']]['count'] += 1;
				$moyByStructure [$value['id']]['somme'] += intval($value['gravite']);
			}
		}
		foreach ($moyByStructure as $key => $value){
			$moyByStructure[$key]['gravite'] = round($value['somme'] /$value['count']);
		}
		return $moyByStructure;
	}
	
	/**
	 * 
	 * @param unknown $gravites
	 * @param unknown $maturProb
	 * @param unknown $type
	 * @param Risque $criteria
	 * @return number|mixed
	 */
	public function mapForTableauCriticiteAndGraviteByStructure(&$gravites, &$maturProb,$type,$criteria){
		if($type==1){
			foreach ($maturProb as $cle => $value){
				if(!isset($maturProb[$cle]['direction'])){
					$maturProb[$cle]['direction'] = explode('\\', $value['libelle'])[0];
				}
			}
		}
		$arrGraviteByStructure = $this->calculGraviteByTop($gravites);
		foreach ($arrGraviteByStructure as $key => $tab){
			foreach ($maturProb as $cle => $value) {
				if($key==$value['id']){
					$maturProb[$cle]['gravite'] = $arrGraviteByStructure[$key]['gravite'];
					$maturProb[$cle]['criticite']=intval($arrGraviteByStructure[$key]['gravite']) * intval($value['probabilite']);
				}
			}
		}
		return $maturProb;
	}
	
	/**
	 * @param array $gravites
	 * @param array $matuProb
	 * @param array $sites
	 * @return number|mixed|unknown
	 */ 
	public function mapForTableauCriticiteAndGraviteBySite(&$gravites,&$maturProb){
		$arrGraviteByStite = $this->calculGraviteByTop($gravites);
		foreach ($arrGraviteByStite as $key => $tab){
			foreach ($maturProb as $cle => $value){
				$maturProb[$cle]['criticite']=0;
				if($key==$value['id']) {
					$maturProb[$cle]['gravite'] = $arrGraviteByStite[$key]['gravite'];
					$maturProb[$cle]['criticite']=intval($tab['gravite']) * intval($value['probabilite']);
				}
			}
		}
		return $maturProb;
	}

	/**
	 * @param unknown $data
	 */
	public function mapForTableauRisqueCriticite(&$data){
		$tableau =array();
		foreach ($data as $value) {
			if(!isset($tableau[$value['id']])){
				$tableau[$value['id']]=array('id'=> $value['id'], 'libelle'=> $value['libelle'] , 'gravite'=>0 , 'probabilite'=>0, 'maturite'=>0);
			}
		}
		foreach ($tableau as $cle => $tab) {
			$probas = array();
			$gravites = array();
			$probas = array();
			$maturites = array();
			$i=0;
			foreach($data as $value) {
				$probas = array();
				$gravites = array();
				$probas = array();
				$maturites = array();
				$i=0;
				foreach($data as $value) {
					if($value['id']==$cle) {
						$probas   [$i] = intval($value['probabilite']);
						$gravites [$i] = intval($value['gravite']);
						$maturites[$i] = intval($value['maturite']);
						$i++;
					}
				}
				$tableau[$cle]['gravite']      =	count($gravites)>0  ? intval( array_sum($gravites) / count($gravites)) :0;
				$tableau[$cle]['probabilite']  =	count($probas)>0    ? intval(array_sum($probas) / count($probas)) 	   :0;
				$tableau[$cle]['maturite']     =	count($maturites)>0 ? intval(array_sum($maturites) / count($maturites)):0;
				$tableau[$cle]['criticite']    =	intval($tableau[$cle]['probabilite'])*intval($tableau[$cle]['gravite']);
			}
		}
		return $tableau;
	}

	/**
	 * 
	 * @param array $reqTotalRisques
	 * @param array $reqTestedRisques
	 * @param array $reqTotalControles
	 * @param array $reqTestedControles
	 */
	public function mapForTableauPriseEnCharge($reqTotalRisques, $reqTestedRisques){
		$tableauKpis = array();
		$annees = array_unique(array_merge($this->array_column($reqTotalRisques, 'annee'), $this->array_column($reqTestedRisques, 'annee')));
		$i=0;
		foreach ($annees as $an){
			if($an!=null){
				$tableauKpis[$i]['annee'] = intval($an);
				$tableauKpis[$i]['risk_total'] = 0;
				$tableauKpis[$i]['risk_test'] = 0;
				$i++;
			}
		}
		foreach ($tableauKpis as $key => $kpi){
	        foreach ($reqTotalRisques as $value) {
			     if($value['annee']==$kpi['annee']){
			     	$tableauKpis[$key]['risk_total'] = intval($value['nombre']);
			     	break;
			     }
		    }
		    foreach($reqTestedRisques as $value) {
		    	if($value['annee']==$kpi['annee']) {
		    		$tableauKpis[$key]['risk_test'] = intval($value['nb']);
		    		break;
		    	}
		    }
		}
		return $tableauKpis;			   
	}

	
	/**
	 * @param array $data
	 */
	public function mapForTableauICGByYear($data){
		$tableau = array('risque'=>array(), 'global'=>array());
		$arrMenace = array();
		foreach ($data as $key => $value){
			if(!isset($tableau['risque'][$value['id']])){
				$tableau['risque'][$value['id']] = array();
			}
			if(!isset($arrMenace[$value['id']])){
				$arrMenace[$value['id']] = $value['menace'];
			}
			if(!isset($tableau['global'][$value['annee']])){
				$tableau['global'][$value['annee']] = array();
			}
		}
		foreach ($tableau['global'] as $key => $value){
			foreach ($tableau['risque'] as $cle => $valeur) {
				if(!isset($tableau['risque'][$cle][$key])) {
					$tableau['risque'][$cle][$key] = array('libelle'=>$arrMenace[$cle], 'probabilite'=>0, 'gravite'=>0,'criticite'=>0, 'maturite'=>0);
				}
			}
		}
		foreach ($tableau['risque'] as $cle => $tab){
			foreach ($data as $key => $value){
				if($cle == $value['id']){
					$tableau['risque'][$cle][$value['annee']]['libelle']     = $value['menace'];
					$tableau['risque'][$cle][$value['annee']]['probabilite'] = intval($value['probabilite']);
					$tableau['risque'][$cle][$value['annee']]['gravite']     = intval($value['gravite']);
					$tableau['risque'][$cle][$value['annee']]['criticite']   = intval($value['probabilite'])*intval($value['gravite']);
					$tableau['risque'][$cle][$value['annee']]['maturite']    = intval($value['maturite']);
				}
			}
		}
		foreach ($tableau['global'] as $cle => $tab){
			$nbRisk = 0;
			$totalCriticite =0;
			foreach ($data as $key => $value){
				if($cle == $value['annee']){
					$nbRisk += 1;
					$totalCriticite += intval($value['criticite']);
			   	}
		   }
		   $cmp = 16*$nbRisk;
		   $tableau['global'][$cle]['ct'] = $totalCriticite;
		   $tableau['global'][$cle]['cmp'] = $cmp;
		   $tableau['global'][$cle]['icg'] = ($cmp==0)?0:round(($totalCriticite/$cmp)*16, 1);
		}
		return $tableau;
	}
	
	/**
	 * @param array $data
	 */
	public function mapForGrapheICGByYear($data){
		$tableau = array('years'=>array(), 'risques'=>array());
		foreach ($data as $key => $value){
			if(!isset($tableau['years'][$value['annee']])){
				$tableau['years'][$value['annee']] = array();
			}
			if(!isset($tableau['risques'][$value['id']])){
				$tableau['risques'][$value['id']] = $value['menace'];
			}
		}
		foreach ($tableau['years'] as $key => $value) {
			foreach ($tableau['risques'] as $cle => $valeur) {
				$tableau['years'][$key][]=0;
				foreach ($tableau['years'] as $cle => $tab) {
					$aide = false; $i=0;
					foreach ($data as $key => $value){
						if($cle == $value['annee']){
							$tableau['years'][$cle][$i] = intval($value['criticite']);
							$i++;
							$aide = true;
						}
					}
				}
			}
		}
		return $tableau;
	}
	
	/**
	 * @param \App\Entity\Risque $criteria
	 * @return array
	 */
	public function mapForMatrice($probabiteKPIs, $graviteKPIs, $type, $criteria) { 
		if($type<=1 && $criteria->getCartographie()->getId() <= 2) {
			$kpis = $this->mapForTableauCriticiteAndGraviteByStructure($graviteKPIs, $probabiteKPIs,$type, $criteria);
		} elseif ($type==1 && $criteria->getCartographie()->getId() > 2) {
			$kpis = $this->mapForTableauCriticiteAndGraviteBySite($graviteKPIs, $probabiteKPIs);
		} elseif($type >= 2) {
			$kpis  = $probabiteKPIs;
		}
		return $kpis;
	}
	
	/**
	 * Methode equivalent a array_column
	 * @param unknown $array
	 * @param unknown $key
	 */
	public function array_column($array, $key, $condition=null){
		$arrColumn = array();
		$i=0;
		foreach ($array as $k => $value){
			$arrColumn[$i] = $value[$key];
			$i++;
		}
		return $arrColumn;
	}
}
