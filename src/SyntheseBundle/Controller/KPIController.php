<?php
namespace App\SyntheseBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Criteria\RisqueCriteria;
use App\Entity\Risque;
use App\Entity\Controle;
use Blameable\Fixture\Document\Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Annotation\QMLogger;

class KPIController extends \App\Controller\BaseController {

	/**
	 * @QMLogger(message="KPI: Repartition et comparaison criticite")
	 * @Method({"GET","POST"})
	 * @Route("/{carto}/{type}/rcc", name="rcc")
	 * @Template("KPI/RepartitionCriticite.html.twig")
	 */
	public function repartitionComparaisonCriticiteAction(Request $request,$carto,$type) {
		$dm = $this->container->get('doctrine_mongodb')->getManager();
		$graphe= array('Maturité'=>array() , 'Criticité'=>array());
		$choixRepo	= ($type  == 0)
					?  'Direction'
					: (($type  == 1) ? ($carto <=2  ? 'Departement' : 'Site' )
									 : ($type == 2  ? ($carto !=2 ? 'Activite': 'Projet') : 'Equipement'));
		
		$this->denyAccessUnlessGranted('rcc', new Risque(), 'Accés non autorisé!');
					
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', array());
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse($this->generateUrl('rcc', array('carto'=>$carto, 'type'=>$type)));
		} 	elseif($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$this->get('session')->set('risque_criteria', array('cartographie' => $carto));
		}
		$data = $this->get('session')->get('risque_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		if($type<=1 && $carto<=2) {
		    $gravites= $dm->getRepository('OrangeSyntheseBundle:Risque')->getGraviteByRisqueStructure($form->getData(),$type)->execute();
			$matuProb = $dm->getRepository('OrangeSyntheseBundle:Risque')->getMaturiteProbabiliteByRisqueStructure($form->getData(), $type)->getQuery()->execute()->toArray();
			$kpis = $this->orange_main_core->getMapping('Risque', true)->mapForTableauCriticiteAndGraviteByStructure($gravites,$matuProb,$type, $form->getData());
		} elseif ($type==1 && $carto>2) {
			$gravites= $dm->getRepository('OrangeSyntheseBundle:Risque')->getGraviteByRisqueSite($form->getData())->execute();
			$matuProb= $dm->getRepository('OrangeSyntheseBundle:Risque')->getMaturiteProbabiliteBySite($form->getData())->getQuery()->execute()->toArray();
			$kpis = $this->orange_main_core->getMapping('Risque', true)->mapForTableauCriticiteAndGraviteBySite($gravites,$matuProb);
		} elseif ($type>=2) {
			$kpis  = $dm->getRepository('OrangeSyntheseBundle:Risque')->getMaturiteGraviteByType($form->getData(),$type)->getQuery()->execute();
		}
		$i=0;
		foreach ($kpis as $key=>$value) {
			$graphe['Maturité'][$i]=intval($value['maturite']);
			$graphe['Criticité'][$i]=(isset($value['criticite'])) ? intval($value['criticite']) : 0;
			$i++;
		}
		$this->get('session')->set('export', array('kpis' => serialize($kpis), 'type'=>strtolower($choixRepo), 'source'=>'rcc'));
		return array('carto'=> $carto, 'type'=>$type, 'libelle'=>$choixRepo, 'form'=>$form->createView(), 'kpis'=>$kpis, 'graphe'=>$graphe);
	}
	
	
	/**
	 * @QMLogger(message="KPI: Repartition  criticite par risque")
	 * @Method({"GET","POST"})
	 * @Route("/{carto}/rrc", name="rrc")
	 * @Template("KPI/RepartitionRisqueCriticite.html.twig")
	 */
	public function repartitionRisqueCriticiteAction(Request $request,$carto) {
		$dm = $this->container->get('doctrine_mongodb')->getManager();
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		$this->denyAccessUnlessGranted('rrc', new Risque(), 'Accés non autorisé!');
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', array());
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse($this->generateUrl('rrc', array('carto'=>$carto)));
		} elseif ($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$this->get('session')->set('risque_criteria', array('cartographie' => $carto));
		}
		$data = $this->get('session')->get('risque_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		$req = $dm->getRepository('OrangeSyntheseBundle:Risque')->getMaturiteGraviteProbabilteByRisque($form->getData())->getQuery()->execute();
		$kpis = $this->orange_main_core->getMapping('Risque', true)->mapForTableauRisqueCriticite($req);
		$this->get('session')->set('export', array('kpis' => serialize($kpis), 'type'=>'Risque','source'=>'rrc'));
		return array('carto'=> $carto, 'kpis'=>$kpis, 'form'=>$form->createView());
	}
	
	/**
	 * @QMLogger(message="KPI: Risques transverses")
	 * @Method({"GET","POST"})
	 * @Route("/{carto}/rt", name="rt")
	 * @Template("KPI/RisqueTransverses.html.twig")
	 */
	public function risqueTransversesAction(Request $request,$carto){
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		
		$this->denyAccessUnlessGranted('rt', new Risque(), 'Accés non autorisé!');
		
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', array());
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse($this->generateUrl('rt', array('carto'=>$carto)));
		}elseif($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
				$this->get('session')->set('risque_criteria', array('cartographie' => $carto));
		}
		$data = $this->get('session')->get('risque_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		$kpis=$this->getDoctrine()->getRepository(Risque::class)->risqueTransverses($form->getData())->getQuery()->getArrayResult();
		$this->get('session')->set('export', array('kpis' => serialize($kpis), 'type'=>'Risque','source'=>'rt'));
		return array('carto'=> $carto, 'kpis'=>$kpis, 'form'=>$form->createView());
	}
	
	/**
	 * @QMLogger(message="KPI: Details d'un risque transverse")
	 * @Method({"GET","POST"})
	 * @Route("/{menace_id}/{occurence}/{carto}/details_rt", name="details_rt")
	 * @Template("KPI/details_rt.html.twig")
	 */
	public function detailsRTAction(Request $request,$menace_id,$occurence,$carto){
		$entity = $this->getDoctrine()->getRepository(Menace::class)->find($menace_id);
		
		$this->denyAccessUnlessGranted('drt', new Risque(), 'Accés non autorisé!');
		
		$risques   = $this->getDoctrine()->getRepository(Risque::class)->getGraviteByMenaceStructure($entity,$carto)->getQuery()->execute();
		$cartographie=$this->getDoctrine()->getRepository(Cartographie::class)->find($carto);
		if($carto<=2)
		    $this->get('session')->set('export', array('kpis' => serialize($risques), 'type'=>'Risque','source'=>'details_metier_rt'));
		else
			$this->get('session')->set('export', array('kpis' => serialize($risques), 'type'=>'Risque','source'=>'details_env_rt'));
		return array('entity'=>$entity, 'occurence'=>$occurence, 'risques'=>$risques, 'carto'=>$carto, 'cartographie'=>$cartographie);
	}

	/**
	 * @QMLogger(message="KPI: Comparaison des controles")
	 * @Method({"GET","POST"})
	 * @Route("/{carto}/cmc", name="cmc")
	 * @Template("KPI/compareControle.html.twig")
	 */
	public function compareControleAction(Request $request,$carto){
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		
		$this->denyAccessUnlessGranted('cmc', new Risque(), 'Accés non autorisé!');
		
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', array());
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse($this->generateUrl('cmc', array('carto'=>$carto)));
		}elseif($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('controle_criteria'))==0) {
				$this->get('session')->set('risque_criteria', array('cartographie' => $carto));
		}
		$data = $this->get('session')->get('risque_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		return array('carto'=> $carto, 'form'=>$form->createView());
	}
	
	/**
	 * @QMLogger(message="KPI: Chargement ajax pour la comparaison des controles")
	 * @Route("/liste_des_controles_for_kpis", name="liste_des_controles_for_kpis")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(RisqueCriteria::class);
		$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
		$queryBuilder = $em->getRepository(Controle::class)->getControles($form->getData());
		$this->get('session')->set('export', array('kpis' => serialize($queryBuilder->getQuery()->execute()), 'type'=>'Risque','source'=>'cmc'));
		return $this->paginate($request, $queryBuilder, 'addRowInTableControle');
	}
	
	/**
	 * @QMLogger(message="KPI: Taux de prise en charge des risques")
	 * @Method({"GET","POST"})
	 * @Route("/{carto}/tprc", name="tprc")
	 * @Template("KPI/tauxPriseChargeRisqueControle.html.twig")
	 */
	public function tauxPriseChargeRisqueControleAction(Request $request,$carto){
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		
		$this->denyAccessUnlessGranted('tprc', new Risque(), 'Accés non autorisé!');
		
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', array());
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse($this->generateUrl('tprc', array('carto'=>$carto)));
		}elseif($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
				$this->get('session')->set('risque_criteria', array('cartographie' => $carto));
		}
		
		$data = $this->get('session')->get('risque_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		$reqTotalRisques = $this->getDoctrine()->getRepository(Risque::class)-> getMenacesTotalByYear($form->getData())->getQuery()->getArrayResult();
		$reqTestedRisques = $this->getDoctrine()->getRepository(Quiz::class)-> getRisquesTesterByYear($form->getData())->getQuery()->getArrayResult();
		$kpis =  $this->orange_main_core->getMapping('Risque')->mapForTableauPriseEnCharge($reqTotalRisques, $reqTestedRisques);
		$this->get('session')->set('export', array('kpis' => serialize($kpis), 'type'=>'Risque','source'=>'tprc'));
		return array('carto'=>$carto, 'kpis'=>$kpis, 'form'=>$form->createView());
	}
	/**
	 * @Method({"GET","POST"})
	 * @Route("/{carto}/rav", name="rav")
	 * @Template("KPI/RisquesAveres.html.twig")
	 */
	public function risquesAveresAction(Request $request,$carto){
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		
		$this->denyAccessUnlessGranted('rav', new Risque(), 'Accés non autorisé!');
		
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', array());
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse($this->generateUrl('rav', array('carto'=>$carto)));
		}elseif($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
				$this->get('session')->set('risque_criteria', array('cartographie' => $carto));
		}
		$data = $this->get('session')->get('risque_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		$kpis = $this->getDoctrine()->getRepository(Menace::class)->getRisquesAveresByPeriode($form->getData())->getQuery()->execute();
		$this->get('session')->set('export', array('kpis' => serialize($kpis), 'type'=>'Risque','source'=>'rav'));
		return array('carto'=>$carto, 'kpis'=>$kpis, 'form'=>$form->createView());
	}
	
	/**
	 * @QMLogger(message="KPI: Evolution ICG")
	 * @Method({"GET","POST"})
	 * @Route("/{carto}/eicg", name="eicg")
	 * @Template("KPI/evolutionICG.html.twig")
	 */
	public function evolutionICGAction(Request $request,$carto) {
		$dm = $this->container->get('doctrine_mongodb')->getManager();
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		
		$this->denyAccessUnlessGranted('eicg', new Risque(), 'Accés non autorisé!');
		
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', array());
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse($this->generateUrl('eicg', array('carto'=>$carto)));
		}elseif($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
				$this->get('session')->set('risque_criteria', array('cartographie' => $carto));
		}
		$data = $this->get('session')->get('risque_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		$req = $dm->getRepository('OrangeSyntheseBundle:Evaluation')->getMaturiteGraviteProbabiliteByRisqueByYear($form->getData())->execute();
		$map = function($value) { 
			$value['id'] = $value['mId'];unset($value['mId']);$value['maturite']=(int)$value['maturite'];
			$value['criticite']=(int)$value['probabilite']*(int)$value['gravite'];
			return $value; 
		};
		$req = array_map($map, $req->toArray());
		$kpis =  $this->orange_main_core->getMapping('Risque', true)->mapForTableauICGByYear($req);
		$graphe = $this->orange_main_core->getMapping('Risque', true)->mapForGrapheICGByYear($req);
		$this->get('session')->set('export', array('kpis' => serialize($kpis), 'type'=>'Risque','source'=>'eicg'));
		return array('carto'=>$carto, 'kpis'=>$kpis, 'form'=>$form->createView(), 'graphe'=>$graphe);
	}
	
	/**
	 * @QMLogger(message="KPI: Extraction ")
	 * @Route("/export_pour_kpi", name="export_pour_kpi")
	 * @Template()
	 */
	public function exportAction(Request $request){
		$em=$this->getDoctrine();
		
		$this->denyAccessUnlessGranted('export_kpi', new Risque(), 'Accés non autorisé!');
		
		$session = $this->get('session')->get('export');
		$data=unserialize($session['kpis']);
		$type=isset($session['type'])?$session['type']:null;
		$source=$session['source'];
		if($source=='rcc')
			$reporting = $this->orange_main_core->getReporting('Kpi')->extractRCC($data, $type);
		elseif($source=='rrc')
			$reporting = $this->orange_main_core->getReporting('Kpi')->extractRRC($data);
		elseif($source=='rt')
			$reporting = $this->orange_main_core->getReporting('Kpi')->extractRT($data);
		elseif($source=='details_metier_rt')
			$reporting = $this->orange_main_core->getReporting('Kpi')->extractDetailsRTMetier($data,$em);
		elseif($source=='details_env_rt')
			$reporting = $this->orange_main_core->getReporting('Kpi')->extractDetailsRTEnv($data,$em);
		elseif($source=='tprc')
			$reporting = $this->orange_main_core->getReporting('Kpi')->extractTPRC($data,$em);
		elseif($source=='cmc')
			$reporting = $this->orange_main_core->getReporting('Kpi')->extractCMC($data,$em);
		elseif($source=='rav')
			$reporting = $this->orange_main_core->getReporting('Kpi')->extractRAV($data,$em);
		elseif($source=='eicg')
			$reporting = $this->orange_main_core->getReporting('Kpi')->extractEICG($data);
		
		$reporting->getResponseAfterSave('php://output', 'Extractions Kpis');
		return $this->redirect($this->generateUrl('les_risques'));
	}
	
	/**
	 * @QMLogger(message="KPI: Filtres sur KPI")
	 * @Route("/filtres_pour_kpi", name="filtres_pour_kpi")
	 * @Template()
	 */
	public function filterAction(Request $request,$link,$carto,$type) {
		$form = $this->createForm(RisqueCriteria::class);
		if($request->getMethod()=='POST') {
				$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
				return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\Controle $entity
	 * @return array
	 */
	protected function addRowInTableControle($entity) {
		if($entity->getRisque()->getCartographie()->getId()<=2){
				return array(
						$entity->getRisque()->getDirection()->__toString(),
						$entity->getRisque()->getStructreOrSite()->__toString(),
						$entity->getRisque()->getActivite()->__toString(),
						$entity->getRisque()->__toString(),
						'<a href="'.$this->generateUrl('details_controle', array('id'=>$entity->getId())).'">controle</a>',
						$entity->getMaturiteTheorique()->getValeur().'<b><=></b>'.$entity->getMaturiteTheorique()->getLibelle(),
						$entity->getMaturiteReel()
				);
		}else{
				return array(
						$entity->getRisque()->getStructreOrSite()->__toString(),
						$entity->getRisque()->getActivite()->__toString(),
						$entity->getRisque()->__toString(),
						'<a href="'.$this->generateUrl('details_controle', array('id'=>$entity->getId())).'">controle</a>',
						$entity->getMaturiteTheorique()?$entity->getMaturiteTheorique()->getValeur():'non renseigné',
						$entity->getMaturiteReel()?$entity->getMaturiteReel()->getValeur():'non renseigné'
				);
		}
	}
}
