<?php 
namespace App\Controller;

use App\Form\HistoryEtatRisqueType;
use App\MainBundle\OrangeMainBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Risque;
use App\Entity\Structure;
use Symfony\Component\HttpFoundation\Response;
use App\Criteria\RisqueCriteria;
use App\Entity\Projet;
use App\Form\RisqueType;
use App\Entity\RisqueMetier;
use App\Form\RisqueMetierType;
use App\Entity\Cartographie;
use App\Entity\RisqueProjet;
use App\Form\RisqueProjetType;
use App\Entity\HistoryEtatRisque;
use App\Form\RisqueEnvironnementalType;
use App\Entity\RisqueSST;
use App\Form\RisqueSSTType;
use App\Entity\RisqueEnvironnemental;
//use App\OrangeMainBundle;
use App\Event\CartoEvent;
use App\Annotation\QMLogger;

class RisqueController extends BaseController {
	
	/**
	 * @Route("/{kw}/search_word", name="search_word")
	 * @Template()
	 */
	public function searchWordAction($kw){
		$em=$this->getDoctrine()->getManager();
		$qb = $em->getRepository('App\Entity\Risque')->createQueryBuilder('r');
		$qb->innerJoin('r.menace','m')
 		   ->innerJoin('r.cartographie', 'c')
		   ->innerJoin('r.causeOfRisque','cOfRis');
// 		   ->leftJoin('cOfRis.controle','ctrls');
		$qb->select('r.id, m.libelle menace, c.libelle carto, r.dateSaisie, count( cOfRis.id) nbCauz')
		   ->where('m.libelle like :kw')->setParameter('kw','%'.$kw.'%')->groupBy('r.id');
		$risques= $qb->getQuery()->getArrayResult();
		return array('risques'=> $risques);
	}
	
	/**
	 * @QMLogger(message="Affichage des risques")
	 * @Route("/les_risques", name="les_risques")
	 * @Template()
	 */
	public function indexAction(Request $request) {
		$position=$this->get('session')->get('risque_criteria')['cartographie'];
		$entity=new Risque();
		if($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$this->get('session')->set('risque_criteria', array('cartographie' => $this->getMyParameter('ids', array('carto', 'metier'))));
		}
		$data = $this->get('session')->get('risque_criteria');
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		$this->modifyRequestForForm($request, $data, $form);
		$entity->setCartographie($form->getData()->getCartographie());
		
		$this->denyAccessUnlessGranted('read', $entity,'Accés non autorisé!');
		
		return array('form' => $form->createView(),'position'=>intval($position));
	}
	
	/**
	 * @QMLogger(message="transfert des risques")
	 * @Route("/transferer_risque", name="transferer_risque")
	 * @Template()
	 */
	public function  transfertAction(Request $request){
		$entity = new RisqueMetier();
		$risque=new Risque();
		$carto=$this->getDoctrine()->getManager()->find(Cartographie::class, $this->getMyParameter('ids', array('carto', 'metier')));
		$risque->setCartographie($carto);
		$entity->setRisque($risque);
		$type = RisqueMetierType::class;
		$form   = $this->createForm($type, $entity);
		return array('form'=>$form->createView(), 'entity'=>$entity);
	}
	
	/**
	 * @QMLogger(message="effecturer transfert des riques")
	 * @Route("/effectuer_transferer_risque", name="effectuer_transferer_risque")
	 * @Template()
	 */
	public function doTransfertAction(Request $request){
		$criteria=null;
		$entity = new RisqueMetier();
		$carto=$this->getDoctrine()->getManager()->find(Cartographie::class, $this->getMyParameter('ids', array('carto', 'metier')));
		$risque=new Risque();
		$risque->setCartographie($carto);
		$entity->setRisque($risque);
		$type = RisqueMetierType::class;
		$form   = $this->createForm($type, $entity);
		$em=$this->getDoctrine()->getManager();
		$datas=array();
		if($request->getMethod()=='POST'){
			$dispatcher = $this->container->get('event_dispatcher');
			$event = $this->cartoEvent;
			$form->handleRequest($request);
			if(strlen($entity->getRisque()->toTransferts)>0) {
				$datas=explode(',',str_replace(" ", "",$entity->getRisque()->toTransferts));
			} else {
				$datas=array_column($em->getRepository('App\Entity\Risque')->listToTransfert($criteria)->select('r.id')->getQuery()->getArrayResult(),'id');
			}
			// recupere les rp a transferer
			$qb = $em->createQueryBuilder('m')->select('m');
			$qb->from('OrangeMainBundle:RisqueProjet', 'm')->join('m.risque','r')->where($qb->expr()->in('r.id',$datas));
			$result = $qb->getQuery()->getResult();
			foreach ($result as $value){
				$value->getRisque()->setEtat($this->getMyParameter('states', array('risque', 'transfere')));
				$riskMetier=$value->transfertToRisqueMetier($entity,$this->getUser(),$carto);
				$riskMetier->getRisque()->setEtat($this->getMyParameter('states', array('risque', 'valide')));
				$em->persist($value);
				$event->setRisque($riskMetier->getRisque());
				$dispatcher->dispatch(OrangeMainBundle::RISQUE_CREATED,$event);
				$dispatcher->dispatch(OrangeMainBundle::RISQUE_VALIDATED,$event);
				$em->persist($riskMetier->getRisque());
				$em->persist($riskMetier);
				$em->flush();
			}
			$this->get('session')->getFlashBag()->add('success', "Le transfert s'est déroulé avec succés.");
			return new Response($this->redirect($this->generateUrl('les_risques_a_transferer')));
		}
		return new Response($this->renderView('risque/transfert.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	
	
	/**
	 * @QMLogger(message="Risques a tester")
	 * @Route("/les_risques_a_tester", name="les_risques_a_tester")
	 * @Template()
	 */
	public function risqueATesterAction() {
		$position=$this->get('session')->get('risque_criteria')['cartographie'];
		if($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$this->get('session')->set('risque_criteria', array('cartographie' => $this->getMyParameter('ids', array('carto', 'metier'))));
		}
		$data = $this->get('session')->get('risque_criteria');
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		$this->modifyRequestForForm($request, $data, $form);
		return array('form' => $form->createView(),'position'=>intval($position));
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des risques a tester")
	 * @Route("/liste_des_risques_a_tester", name="liste_des_risques_a_tester")
	 * @Template()
	 */
	public function listATesterAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(RisqueCriteria::class, new Risque());
		$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Risque')->listValidQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder,'addRowInValidTableWithChecked');
	}
	

	/**
	 * @QMLogger(message="Filtre sur les risques")
	 * @Route("/filtrer_les_risques", name="filtrer_les_risques")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(RisqueCriteria::class, new Risque());
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @Route("/{carto}/{link}/choix_carto", name="choix_carto")
	 */
	public function choixCartoAction($carto, $link){
		$this->get('session')->set('risque_criteria', array('cartographie' =>$carto));
		return $this->redirect($this->generateUrl($link));
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des risques")
	 * @Route("/liste_des_risques", name="liste_des_risques")
	 * @Template()
	 */
	public function listAction(Request $request) {
		//exit('ok');
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(RisqueCriteria::class, new Risque());
		$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Risque')->listValidQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @Route("/menu_nouveau_risque", name="menu_nouveau_risque")
	 * @Template("risque/menu_new_risque.html.twig")
	 */
	public function menuAction() {
		return array();
	}

	/**
	 * @QMLogger(message="Nouveau risque")
	 * @Route("/{cartographie_id}/nouveau_risque", name="nouveau_risque")
	 * @Template()
	 */
	public function newAction($cartographie_id) {
		if($cartographie_id==1) {
			$entity = new RisqueMetier();
			if($this->getUser()->hasRole("ROLE_RESPONSABLE_ONLY")){
				$entity->setDirection($this->getUser()->getStructure()->getDirection());
				$entity->setStructure($this->getUser()->getStructure());
			}
			$type = RisqueMetierType::class;
			$view='risque/new_risque_metier.html.twig';
 		} elseif ($cartographie_id==2) {
			$entity = new RisqueProjet();
			if($this->getUser()->hasRole("ROLE_RESPONSABLE_ONLY")){
				$entity->setDirection($this->getUser()->getStructure()->getDirection());
				$entity->setStructure($this->getUser()->getStructure());
			}
			$type = RisqueProjetType::class;
			$view   ='risque/new_risque_projet.html.twig';
		} elseif($cartographie_id==3) {
			$entity = new RisqueSST();
			$type = RisqueSSTType::class;
			$view   ='risque/new_risque_sst.html.twig';
		} elseif($cartographie_id==4) {
			$entity = new RisqueEnvironnemental();
			$type = RisqueEnvironnementalType::class;
			$view   ='risque/new_risque_environnemental.html.twig';
		}
		$risque = new Risque();
		$risque->setCartographie($this->getDoctrine()->getManager()->getRepository('App\Entity\Cartographie')->find($cartographie_id));
		$entity->setRisque($risque);
		
		$this->denyAccessUnlessGranted('create', $entity->getRisque(),'Accés non autorisé!');
		
		$form   = $this->createForm($type, $entity);
		return $this->render($view, array('entity' => $entity, 'form' => $form->createView(), 'cartographie_id'=>$cartographie_id));
	}

	/**
	 * @QMLogger(message="Envoi des données saisies lors de la creation d'un risque")
	 * @Route("/{cartographie_id}/ajout_risque", name="ajout_risque")
	 * @Template()
	 */
	public function createAction(Request $request,$cartographie_id,EventDispatcherInterface $eventdispatcher) {
	$em = $this->getDoctrine()->getManager();
	if($cartographie_id==1) {
			$entity = new RisqueMetier();
			if($this->getUser()->hasRole("ROLE_RESPONSABLE_ONLY")){
				$entity->setDirection($this->getUser()->getStructure()->getDirection());
				$entity->setStructure($this->getUser()->getStructure());
			}
			$type = RisqueMetierType::class;
			$view='risque/new_risque_metier.html.twig';
 		} elseif ($cartographie_id==2) {
			$entity = new RisqueProjet();
	 		if($this->getUser()->hasRole("ROLE_RESPONSABLE_ONLY")){
					$entity->setDirection($this->getUser()->getStructure()->getDirection());
					$entity->setStructure($this->getUser()->getStructure());
			}
			$type = RisqueProjetType::class;
			$view   ='risque/new_risque_projet.html.twig';
		} elseif($cartographie_id==3) {
			$entity = new RisqueSST();
			$type = RisqueSSTType::class;
			$view   ='risque/new_risque_sst.html.twig';
		} elseif($cartographie_id==4) {
			$entity = new RisqueEnvironnemental();
			$type = RisqueEnvironnementalType::class;
			$view   ='risque/new_risque_environnemental.html.twig';
		}
		$risque = new Risque();
    	$cartographie = $this->getDoctrine()->getRepository('App\Entity\Cartographie')->find($cartographie_id);
    	$risque->setCartographie($cartographie);
    	$entity->setRisque($risque);
		$form   = $this->createForm($type, $entity);
    	$form->handleRequest($request);
    	
    	if($form->isValid()) {
            //$dispatcher = $this->container->get('event_dispatcher');
            $dispatcher = $eventdispatcher;
    		$entity->getRisque()->setUtilisateur($this->getUser());
    		$entity->getRisque()->setSociete($this->getUser()->getSociete());
			$entity->getRisque()->setCartographie($cartographie);
			// Génération évènement création de Risque
			$event=$this->cartoEvent;
    		$event->setRisque($entity->getRisque());
			$dispatcher->dispatch(OrangeMainBundle::RISQUE_CREATED,$event);
			// Migration vers la base MongoDB
			$entity->getRisque()->setTobeMigrate(true);
    		// Mise à jour menace dans carto
			$menace = $entity->getRisque()->getMenace();
			$menace_has_profil = $this->getDoctrine()->getRepository('App\Entity\Menace')->menaceHasProfilRisque($cartographie_id);
			if(!$menace_has_profil) {
				$menace->addCartographie($cartographie);
				$em->persist($menace);
			}
			// Sauvegarde de la base de données
			$em->persist($entity->getRisque());
    		$em->persist($entity);
    		$em->flush();
    		$this->get('session')->getFlashBag()->add('success', "Le risque a été ajouté avec succès.");
    		return $this->redirect($this->generateUrl('nouveau_controle_de_risque', array('risque_id' => $entity->getRisque()->getId())));
    	}
    	return $this->render($view, array('entity' => $entity, 'form' => $form->createView(), 'cartographie_id'=>$cartographie_id));
	}

	/**
	 * @QMLogger(message="Risques a transferer")
	 * @Route("/les_risques_a_transferer", name="les_risques_a_transferer")
	 * @Template("risque/transferedRisques.html.twig")
	 */
	public function transferedRisquesAction(Request $request) {
		$position=$this->get('session')->get('risque_criteria')['cartographie'];
		if($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$position=$this->getMyParameter('ids', array('carto', 'metier'));
			$this->get('session')->set('risque_criteria', array('cartographie' => $this->getMyParameter('ids', array('carto', 'metier'))));
		}
		$data = $this->get('session')->get('risque_criteria');
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		$this->modifyRequestForForm($request, $data, $form);
		return array('form' => $form->createView(), 'position'=>intval($position));
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des risques a transferer")
	 * @Route("/liste_des_risques_a_transferer", name="liste_des_risques_a_transferer")
	 */
	public function listTransferedRisquesAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(RisqueCriteria::class, new Risque());
		$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Risque')->listToTransfert($form->getData());
		return $this->paginate($request, $queryBuilder, 'addRowInTansferedRisqueTable');
	}
	
	/**
	 * @QMLogger(message="Risques rejetes")
	 * @Route("/les_risques_rejetes", name="les_risques_rejetes")
	 * @Template("risque/rejectedRisques.html.twig")
	 */
	public function rejectedRisquesAction(Request $request) {
		$position=$this->get('session')->get('risque_criteria')['cartographie'];
		if($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$position=$this->getMyParameter('ids', array('carto', 'metier'));
			$this->get('session')->set('risque_criteria', array('cartographie' => $this->getMyParameter('ids', array('carto', 'metier'))));
		}
		$data = $this->get('session')->get('risque_criteria');
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		$this->modifyRequestForForm($request, $data, $form);
		return array('form' => $form->createView(), 'position'=>intval($position));
	}

	/**
	 * * @QMLogger(message="Risques a valider")
	 * @Route("/risques_a_valider", name="risques_a_valider")
	 * @Template("risque/unValidatedRisques.html.twig")
	 */
	public function unValidatedRisquesAction(Request $request) {
		$position=$this->get('session')->get('risque_criteria')['cartographie'];
		if($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$this->get('session')->set('risque_criteria', array('cartographie' => $this->getMyParameter('ids', array('carto', 'metier'))));
		}
		$data = $this->get('session')->get('risque_criteria');
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		$this->modifyRequestForForm($request, $data, $form);
		return array('form' => $form->createView(), 'position'=>intval($position));
		//return array('form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Risques a completer")
	 * @Route("/risques_a_completer", name="risques_a_completer")
	 * @Template("risque/unCompletedRisques.html.twig")
	 */
	public function unCompletedRisquesAction(Request $request) {
		$position=$this->get('session')->get('risque_criteria')['cartographie'];
		if($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$this->get('session')->set('risque_criteria', array('cartographie' => $this->getMyParameter('ids', array('carto', 'metier'))));
		}
		$data = $this->get('session')->get('risque_criteria');
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		$this->modifyRequestForForm($request, $data, $form);
		return array('form' => $form->createView(), 'position'=>intval($position));
	}
	

	/**
	 * @QMLogger(message="Chargement ajax des risques non validés")
	 * @Route("/liste_des_risques_a_valider", name="liste_risques_a_valider")
	 */
	public function listUnValidatedRisquesAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(RisqueCriteria::class, new Risque());
		$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Risque')->listUnValidatedRisquesQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder, 'addRowInUnvalidatedRisqueTable');
	}

	/**
	 * @QMLogger(message="Chargement ajax des risques a completer") 
	 * @Route("/liste_risques_a_completer", name="liste_risques_a_completer")
	 */
	public function listUncompletedRisquesAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(RisqueCriteria::class, new Risque());
		$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Risque')->listUncompletedRisquesQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder, 'addRowInUncompletedRisqueTable');
	}

	
	/**
	 * @QMLogger(message="Chargement ajax des risques rejetes")
	 * @Route("/liste_risques_rejetes", name="liste_risques_rejetes")
	 */
	public function listRejectedRisquesAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(RisqueCriteria::class, new Risque());
		$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Risque')->listRejectedRisquesQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder, 'addRowInRejectedRisqueTable');
	}
	

	/**
	 * @QMLogger(message="Validation d'un risque")
	 * @Route("/{id}/validation_risque", name="validation_risque")
	 * @Template()
	 */
	public function validationAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$risque = $em->getRepository('App\Entity\Risque')->find($id);
		$entity=null;
		if(!$risque) 
			throw $this->createNotFoundException('Aucun risque trouvé pour cet id : '.$id);
		
		$cartographie_id=$risque->getCartographie()->getId();
		if($cartographie_id==1) {
			$entity = $em->getRepository('App\Entity\RisqueMetier')->findOneBy(array('risque'=>$risque));
			$form   = $this->createForm(new RisqueMetierType(true), $entity);
			$view   ='risque/validation_metier.html.twig';
		} elseif ($cartographie_id==2) {
			$entity = $em->getRepository('App\Entity\RisqueProjet')->findOneBy(array('risque'=>$risque));
			$form   = $this->createForm(RisqueProjetType::class, $entity);
			$view   ='risque/validation_projet.html.twig';
		} elseif($cartographie_id==3) {
			$entity = $em->getRepository('App\Entity\RisqueSST')->findOneBy(array('risque'=>$risque));
			$form   = $this->createForm(RisqueSSTType::class, $entity);
			$view   ='risque/validation_sst.html.twig';
		} elseif($cartographie_id==4) {
			$entity = $em->getRepository('App\Entity\RisqueEnvironnemental')->findOneBy(array('risque'=>$risque));
			$form   = $this->createForm(RisqueEnvironnementalType::class, $entity);
			$view   ='risque/validation_environnemental.html.twig';
		} else {
			$entity = new Risque();
			$form= RisqueType::class;
		}

		$this->denyAccessUnlessGranted('validate', $risque,'Accés non autorisé!');

		// set to true to activate entity validation
		$entity->getRisque()->setHasToBeValidated($this->getMyParameter('states',array('risque','a_valider')));
		if($request->getMethod()=='POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				$dispatcher = $this->container->get('event_dispatcher');
				$em = $this->getDoctrine()->getManager();
				$entity->getRisque()->setEtat($this->getMyParameter('states',array('risque', 'en_cours')));
				$entity->getRisque()->setValidateur($this->getUser());
				$entity->getRisque()->setDateValidation(new \DateTime("NOW"));
				foreach ($entity->getRisque()->getCauseOfRisque() as $rha){
					$cause = $rha->getCause();
					$cause->setEtat(true);
					$lib = $cause->getLibelle();
					$qb = $em->getRepository('App\Entity\Risque')->createQueryBuilder('r');
					$qb->innerJoin('r.menace','m')
					->innerJoin('r.cartographie', 'c')
					->innerJoin('r.causeOfRisque','cOfRis');
					// 		   ->leftJoin('cOfRis.controle','ctrls');
					$qb->select('r.id, m.libelle menace, c.libelle carto, r.dateSaisie, count( cOfRis.id) nbCauz')
					->where('m.libelle like :kw')->setParameter('kw','%'.$lib.'%')->groupBy('r.id');
				}
				$entity->getRisque()->setTobeMigrate(true);
				$em->persist($entity);
				$event = $this->cartoEvent;
				$event->setRisque($entity->getRisque());
				$dispatcher->dispatch(OrangeMainBundle::RISQUE_VALIDATED,$event);
				$this->service_status->logEtatRisque($risque,$this->getUser(),"Validation de la fiche de risque!");
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "Le risque mis à jour avec succés. Merci de passer à la maitrise de ce risque.");
				if($risque->getControle()->count()) {
					return $this->redirect($this->generateUrl('edition_controle', array('id' => $risque->getControle()->first()->getId())));
				} else {
					return $this->redirect($this->generateUrl('nouveau_controle_de_risque', array('risque_id' => $id)));
				}
			}
		}
		return $this->render($view, array('entity' => $entity, 'form' => $form->createView(), 'id' => $id));
	}


	/**
	 * @QMLogger(message="Rejeter un risque")
	 * @Route("/{id}/rejet_risque", name="rejet_risque")
	 * @Template("historyEtatRisque/new.html.twig")
	 */
	public function rejetAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$etat = $this->getMyParameter('states',array('risque','rejete'));
		/**
		 *
		 * @var Risque $risque
		 */
		$risque = $em->getRepository('App\Entity\Risque')->find($id);

		if(!$risque) {
			throw $this->createNotFoundException('Aucun risque trouvé pour cet id : '.$id);
		}
		$this->denyAccessUnlessGranted('rejet', $risque, 'Accés Non Autorisé!');
		$entity = new HistoryEtatRisque();
		$form   = $this->createCreateForm($entity, HistoryEtatRisqueType::class);
		if($request->getMethod()=='POST'){
			$form->handleRequest($request);
			if($form->isValid()){
				$risque->setEtat($etat);
				$risque->setTobeMigrate(true);
				$entity->setEtat($etat);
				$risque->setValidateur($this->getUser());
				$entity->setUtilisateur($this->getUser());
				$risque->setDateValidation(new \DateTime("NOW"));
				$entity->setRisque($risque);
				$risque->setLastHistory($entity);
				$em->persist($entity);
				$em->persist($risque);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "Le risque a été rejeté. Une notification sera envoyé au concerné.");
				return new Response($this->redirect($this->generateUrl('les_risques_rejetes')));
			}else{
				return new Response($this->renderView('OrangeMainBundle:HistoryEtatRisque:new.html.twig', array('form'=>$form->createView(), 'id'=>$id)), 303);
			}
		}
		return array('form'=>$form->createView(), 'id'=>$id);
	}


	/**
	 * @QMLogger(message="Suppression d'une cause de risque")
	 * @Route("/{id}/supprimer_cause_du_risque", name="supprimer_cause_du_risque")
	 * @Template()
	 */
	public function supprimeCauseOfRisqueAction(Request $request, $id){
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\RisqueHasCause')->find($id);

		if(!$entity)
			throw $this->createNotFoundException('Aucun cause trouvé pour cet id : '.$id);

		if($request->getMethod()=='POST'){
			$entity->getRisque()->setTobeMigrate(true);
			$em->remove($entity);
			$em->flush();
			return new JsonResponse(array('response' => 'La cause a été supprimée avec succès.'));
		}
		return new Response($this->renderView('risque/supprimeCauseOfRisque.html.twig', array('entity' => $entity)));
	}


	/**
	 * @QMLogger(message="Suppression d'un risque")
	 * @Route("/{id}/suppression_risque", name="suppression_risque")
	 * @Template()
	 */
	public function deleteAction(Request $request, $id){
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Risque')->find($id);
		if($entity == null)
			$this->createNotFoundException("Ce risque n'existe pas!");

		$this->denyAccessUnlessGranted('delete', $entity, 'Accés non autorisé!');
		if($request->getMethod()=='POST') {
			// $dm = $this->get('doctrine_mongodb')->getManager();
			// $rm = $dm->createQueryBuilder('OrangeSyntheseBundle:Risque')->remove()->field('risque')->equals($entity->getId())->getQuery()->execute();
			$em->remove($entity);
			$em->flush();
			return new JsonResponse(array('status' => 'success', 'text' => 'Le risque a été supprimé avec succès.'));
		}
		return new Response($this->renderView('risque/delete.html.twig', array('entity' => $entity)));
	}


	/**
	 * @QMLogger(message="Affichage d'un risque")
	 * @Route("/{id}/details_risque", name="details_risque", requirements={ "id"= "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Risque')->find($id);
		if($entity && $entity->isValidated()==false) {
			return $this->redirect($this->generateUrl('apercu_risque', array('id' => $entity->getId())));
		} elseif($entity==null || $entity->isValidated()==false) {
			$this->createNotFoundException("Le risque n'existe pas dans la base ou bien n'est pas validé");
		}
		$this->denyAccessUnlessGranted('accesOneRisque', $entity, 'Accés Non Autorisé!');
		return array('entity' => $entity);
	}


	/**
	 * @QMLogger(message="Affichage d'un risque")
	 * @Route("/{id}/apercu_risque", name="apercu_risque", requirements={ "id"= "\d+"})
	 * @Template()
	 */
	public function previewAction($id) {
		$em = $this->getDoctrine()->getManager();
		$risque = $em->getRepository('App\Entity\Risque')->find($id);
		if($risque==null) {
			$this->createNotFoundException("Le risque n'existe pas dans la base");
		}

		$this->denyAccessUnlessGranted('accesOneRisque', $risque, 'Accés Non Autorisé!');

		return array('entity' => $risque);
	}


	/**
	 * @QMLogger(message="Modification d'un risque")
	 * @Route ("/{id}/edition_risque", name="edition_risque", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$risque=$em->getRepository('App\Entity\Risque')->find($id);
		$cartographie_id=$risque->getCartographie()->getId();
		if($cartographie_id==1){
			$entity = $em->getRepository('App\Entity\RisqueMetier')->findOneBy(array('risque'=>$id));
			$form   = $this->createForm(RisqueMetierType::class, $entity);
			$view   ='risque/new_risque_metier.html.twig';
		} elseif ($cartographie_id==2){
			$entity = $em->getRepository('App\Entity\RisqueProjet')->findOneBy(array('risque'=>$id));
			$form   = $this->createForm(RisqueProjetType::class, $entity);
			$view   ='risque/new_risque_projet.html.twig';
		} elseif($cartographie_id==3) {
			$entity = $em->getRepository('App\Entity\RisqueSST')->findOneBy(array('risque'=>$id));
			$form   = $this->createForm(RisqueSSTType::class, $entity);
			$view   ='risque/new_risque_sst.html.twig';
		} elseif($cartographie_id==4){
			$entity = $em->getRepository('App\Entity\RisqueEnvironnemental')->findOneBy(array('risque'=>$id));
			$form   = $this->createForm(RisqueEnvironnementalType::class, $entity);
			$view   ='risque/new_risque_environnemental.html.twig';
		} else {
			$entity = new Risque();
			$form = RisqueType::class;
		}
		$this->denyAccessUnlessGranted('update', $risque, 'Accés Non Autorisé!');
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$risque->setDateSaisie(new \DateTime('NOW'));
			$risque->setTobeMigrate(true);
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add('success', "Le risque ` $entity ` a été modifié avec succés.");
			if($risque->isValidated()) {
				return $this->redirect($this->generateUrl('details_risque', array('id' => $risque->getId())));
			} elseif($risque->getControle()->count()) {
				return $this->redirect($this->generateUrl('edition_controle', array('id' => $risque->getControle()->first()->getId())));
			} else {
				return $this->redirect($this->generateUrl('nouveau_controle_de_risque', array('risque_id' => $id)));
			}
		}
		if (!$entity){
            $this->get('session')->getFlashBag()->add('error', "Type de risque introuvable !");
            return $this->redirect($request->headers->get('referer'));
        }
		return $this->render($view, array('entity' => $entity, 'form' => $form->createView(), 'id'=>$id ));
	}
	
	
	/**
	 * @QMLogger(message="Affichage d'une cause d'un risque")
	 * @Route("/{id}/cause_of_risque", name="cause_of_risque")
	 * @Template()
	 */
	public function causeOfRisquesAction(Request $request,$id){
		$entity = $this->getDoctrine()->getRepository('App\Entity\Risque')->find($id);
		return array('entity' => $entity);
	}

	
	/**
	 * @QMLogger(message="Extraction des risques")
	 * @Route("/exporter_les_risques", name="exporter_les_risques")
	 * @Template()
	 */
	public function exportAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(RisqueCriteria::class, new Risque());
		$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
		$criteria = $this->get('session')->get('risque_criteria');
		$queryBuilder = $em->getRepository('App\Entity\Risque')->listValidQueryBuilder($form->getData())->getQuery()->getResult();
		if($criteria['cartographie']==$this->getMyParameter('ids', array('carto', 'metier'))) {
			$data = $this->orange_main_core->getMapping('Risque')->mapForExportMetier($queryBuilder, $form->getData()->getCartographie());
		} elseif($criteria['cartographie']==$this->getMyParameter('ids', array('carto', 'projet'))) {
 			$data = $this->orange_main_core->getMapping('Risque')->mapForExportProjet($queryBuilder, $form->getData()->getCartographie());
		} elseif($criteria['cartographie']==$this->getMyParameter('ids', array('carto', 'sst'))) {
			$data = $this->orange_main_core->getMapping('Risque')->mapForExportSST($queryBuilder, $form->getData()->getCartographie());
		} elseif($criteria['cartographie']==$this->getMyParameter('ids', array('carto', 'environnement'))) {
			$data = $this->orange_main_core->getMapping('Risque')->mapForExportEnvironnemental($queryBuilder, $form->getData()->getCartographie());
		}
		$reporting = $this->orange_main_core->getReporting('Risque')->extract($data, $criteria['cartographie'], $this->getUser()->getSociete());
		return $reporting->getResponseAfterSave('php://output', 'Cartographie des risques ');
	}

	/**
	 * @param \App\Entity\Risque $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
		$data = array($entity->getCode());
        $carto = $this->getMyParameter('ids')['carto'];
		if($entity->getCartographie()->getId()<=2) {
				$data[] = $entity->isPhysical()==false && $entity->getRisqueData($carto) && $entity->getRisqueData($carto)->getProcessus()
						? $entity->getRisqueData($carto)->getProcessus()->__toString() : '';
		}
		$data[] = $entity->getStructreOrSite($carto) ? $entity->getStructreOrSite($carto)->__toString() : '';
		$data[] = $entity->getActivite() ? $entity->getActivite()->__toString() : '';
		$data[] = $entity->getMenace() ? $entity->getMenace()->__toString() : 'Non renseigné';
		$data[] = '<a class="actionLink"  href="#myModal" data-toggle="modal" data-target="#myModal" modal-url="'.$this->generateUrl('cause_of_risque', array('id' => $entity->getId())).'">'.$entity->getCauseOfRisque()->count().'</a>';
		$data[] = $entity->getDateSaisie() ? $entity->getDateSaisie()->format('d/m/Y') : '';
		$data[] = $this->service_action->generateActionsForRisque($entity);
		return $data;
	}
	
	/**
	 * @param \App\Entity\Risque $entity
	 * @return array
	 */
	protected function addRowInValidTableWithChecked($entity) {
		return array(
				'<td  width="35" ><input type="checkbox" name="toTransferts[]" class="chkbox"  value="'.$entity->getId().'"/></td>',
				$entity->getCode(),
				$entity->getActivite()?$entity->getActivite()->__toString():'',
				$entity->getRisqueData($this->getMyParameter('ids')['carto'])?$entity->getRisqueData($this->getMyParameter('ids')['carto'])->getProcessus()->__toString():'',
				$entity->getMenace() ? $entity->getMenace()->__toString() : 'Non renseigné',
				$entity->getDateSaisie()->format('d/m/Y'),
				$this->service_action->generateActionsForRisque($entity)
		);
	}
	
	/**
	 * @param \App\Entity\Risque $entity
	 * @return array
	 */
	protected function addRowInTansferedRisqueTable($entity) {
		return array(
				'<td  width="35" ><input type="checkbox" name="toTransferts[]" class="chkbox"  value="'.$entity->getId().'"/></td>',
				$entity->getCode(),
				$entity->getRisqueProjet()?$entity->getRisqueProjet()->getProjet()->__toString():'',
				$entity->getRisqueData($this->getMyParameter('ids')['carto'])?$entity->getRisqueData($this->getMyParameter('ids')['carto'])->getProcessus()->__toString():'',
				$entity->getMenace() ? $entity->getMenace()->__toString() : 'Non renseigné',
				//$entity->getUtilisateur() ? $entity->getUtilisateur()->__toString() : null,
				$entity->getDateSaisie()->format('d/m/Y'),
				$this->service_action->generateActionsForRisque($entity)
		);
	}
	
	
	/**
	 * @param \App\Entity\Risque $entity
	 * @return array
	 */
	protected function addRowInAveredRisqueTable($entity) {
	    $carto = $this->getMyParameter('ids')['carto'];
		return array(
				'<td  width="35" ><input type="checkbox" name="avered[]" class="chkbox"  value="'.$entity->getId().'" id="check'.$entity->getId().'"/></td>',
				$entity->getCode(),
				$entity->getRisqueData($carto)?($entity->getRisqueData($carto)->getActivite()?$entity->getRisqueData($carto)->getActivite()->__toString():''):'',
				$entity->getRisqueData($carto)?($entity->getRisqueData($carto)->getProcessus()?$entity->getRisqueData($carto)->getProcessus()->__toString():''):'',
				$entity->getMenace() ? $entity->getMenace()->__toString() : 'Non renseigné',
				//$entity->getUtilisateur() ? $entity->getUtilisateur()->__toString() : null,
				$entity->getDateSaisie()->format('d/m/Y'),
				$this->service_action->generateActionsForRisque($entity)
		);
	}

	/**
	 * @param \App\Entity\Risque $entity
	 * @return array
	 */
	protected function addRowInUnvalidatedRisqueTable($entity) {
		return array(
				$entity->getCartographie()->getDescription(),
				$entity->getMenace() ? $entity->getMenace()->__toString() : ($entity->getIdentification()?$entity->getIdentification()->getLibelle():'Non renseigné'),
				$entity->getUtilisateur() ? $entity->getUtilisateur()->__toString() : null,
				$entity->getDateSaisie() ? $entity->getDateSaisie()->format('d/m/Y') : null,
				$this->service_action->generateActionsForUnvalidatedRisque($entity)
		);
	}

	/**
	 * @param \App\Entity\Risque $entity
	 * @return array
	 */
	protected function addRowInUncompletedRisqueTable($entity) {
		return array(
				$entity->getMenace() ? $entity->getMenace()->__toString() : ($entity->getIdentification()?$entity->getIdentification()->getLibelle():'Non renseigné' ),
				$entity->getCartographie() ? $entity->getCartographie()->getDescription() : 'Non renseigné',
				$entity->getUtilisateur()->__toString(),
				$entity->getDateSaisie() ? $entity->getDateSaisie()->format('d/m/Y') : null,
				$this->service_action->generateActionsForUncompletedRisque($entity)
		);
	}

	/**
	 * @param \App\Entity\Risque $entity
	 * @return array
	 */
	protected function addRowInRejectedRisqueTable($entity) {
		return array(
				$entity->getMenace() ? $entity->getMenace()->__toString() : $entity->getIdentification()->getLibelle(),
				$entity->getCartographie() ? $entity->getCartographie()->getDescription() : 'Non renseigné',
				$entity->getValidateur()->__toString(),
				$entity->getDateValidation()->format('d/m/Y'), 
				$entity->getLastHistory() ? $entity->getLastHistory()->getComment() : "aucun motif renseigné",
				$this->service_action->generateActionsForRejetedRisque($entity)
			);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setFilter()
	 */
	protected function setFilter(\Doctrine\ORM\QueryBuilder $queryBuilder, $aColumns, \Symfony\Component\HttpFoundation\Request $request) {
		$alias = $queryBuilder  ->innerJoin('r.causeOfRisque', 'cOfRis')
								->leftJoin('cOfRis.cause', 'cos')
								->leftJoin('cOfRis.planAction','plas')
								->leftJoin('cOfRis.controle','ctrls');
		parent::setFilter($queryBuilder, array('m.libelle', 'plas.libelle','ctrls.description','plas.description','ctrls.description','cos.libelle'), $request);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setOrder()
	 */
	protected function setOrder(\Doctrine\ORM\QueryBuilder $queryBuilder, $aColumns, \Symfony\Component\HttpFoundation\Request $request) {
		parent::setOrder($queryBuilder, array(null, null, null, null, 'r.dateSaisie'), $request);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setOrder()
	 */
	protected function setOrderForUncompleted(\Doctrine\ORM\QueryBuilder $queryBuilder, $aColumns, \Symfony\Component\HttpFoundation\Request $request) {
		parent::setOrder($queryBuilder, array(null, null, null, 'r.dateSaisie'), $request);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setOrder()
	 */
	protected function setOrderForRejected(\Doctrine\ORM\QueryBuilder $queryBuilder, $aColumns, \Symfony\Component\HttpFoundation\Request $request) {
		parent::setOrder($queryBuilder, array(null, null, null, 'r.dateSaisie'), $request);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setOrder()
	 */
	protected function setOrderForUnValidated(\Doctrine\ORM\QueryBuilder $queryBuilder, $aColumns, \Symfony\Component\HttpFoundation\Request $request) {
		parent::setOrder($queryBuilder, array(null, null, null, 'r.dateSaisie'), $request);
	}

	/**
	 * @todo retourne la liste des activity pour un processus donné
	 * @Route("/get_activities_by_processus", name="get_activities_by_processus")
	 */
	public function getActivitiesByProcessusAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$activities = $em->getRepository('App\Entity\Activite')->findByProcessusId($request->request->get('id'));
		$output = array(array('id' => "", 'libelle' => 'Choisir une activité ...'));
		foreach ($activities as $activity) {
			$output[] = array('id' => $activity['id'], 'libelle' => $activity['libelle']);
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}

	/**
	 * @todo retourne la liste des activity pour un processus donné
	 * @Route("/get_activities_by_structure", name="get_activities_by_structure")
	 */
	public function getActivitiesByStructureAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$activities = $em->getRepository('App\Entity\Activite')->findByStructureId($request->request->get('id'));
		$output = array(array('id' => "", 'libelle' => 'Choisir une activité ...'));
		foreach ($activities as $activity) {
			$output[] = array('id' => $activity['id'], 'libelle' => $activity['libelle']);
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}

	/**
	 * @todo retourne la liste des projets pour un processus donné
	 * @Route("/get_projet_by_processus", name="get_projet_by_processus")
	 */
	public function getProjetByProcessusAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$projets = $em->getRepository('App\Entity\Projet')->findByProcessusId($request->request->get('id'));
		$output = array(array('id' => "", 'libelle' => 'Choisir un projet ...'));
		foreach ($projets as $projet) {
			$output[] = array('id' => $projet['id'], 'libelle' => $projet['libelle']);
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}

	/**
	 * @todo retourne la liste des processus pour une structure donnée
	 * @Route("/get_processus_by_structure", name="get_processus_by_structure")
	 */
	public function getProcessusByStructureAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$processuses = $em->getRepository('App\Entity\Processus')->findByStructureId($request->request->get('id'));
		$output = array(array('id' => "", 'libelle' => 'Choisir un processus ...'));
		foreach ($processuses as $processus) {
			$libelle = ($processus['type'] == 1) ? '[MP] - '.$processus['libelle']
											   : ($processus['type'] == 2 ? '[P] - '.$processus['libelle'] : '[SP] - '.$processus['libelle']);
			$output[] = array('id' => $processus['id'], 'libelle' => $libelle);
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}

	/**
	 * @todo retourne la liste des processus pour une structure donnée
	 * @Route("/get_responsable_by_structure", name="get_responsable_by_structure")
	 */
	public function getResponsableByStructureAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$structure = $em->getRepository('App\Entity\Structure')->find($request->request->get('id'));

		if(!$structure) {
			throw $this->createNotFoundException('Aucune structure trouvée pour cet id : '.$request->request->get('id'));
		}

		return new Response($structure->getManager() == null ? 'Aucun' : $structure->getManager());
	}

	/**
	 * @Route("/get_responsable_by_site", name="get_responsable_by_site")
	 */
	public function getResponsableBySiteAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$site = $em->getRepository('App\Entity\Site')->find($request->request->get('id'));
	
		if(!$site) {
			throw $this->createNotFoundException('Aucune structure trouvée pour cet id : '.$request->request->get('id'));
		}
	
		return new Response($site->getResponsable() == null ? 'Aucun' : $site->getResponsable());
	}
}
