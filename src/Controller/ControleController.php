<?php

namespace App\Controller;

use App\Form\ExecutionType;
use App\MainBundle\OrangeMainBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Controle;
use App\Entity\Quiz;
use App\Form\ControleType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\PlanAction;
use App\Entity\Execution;
use App\Criteria\ControleCriteria;
use App\Event\CartoEvent;
//use App\OrangeMainBundle;
use App\Form\QuizType;
use App\Annotation\QMLogger;

class ControleController extends BaseController {
	
	/**
	 * @QMLogger(message="Affichage de la liste des controles")
	 * @Route("/les_controles", name="les_controles")
	 * @Template()
	 */
	public function indexAction() {
		$form = $this->createForm(ControleCriteria::class);
		$data = $this->get('session')->get('controle_criteria');
		$data ['cartographie'] = 1;
		$data = $this->get('session')->set('controle_criteria', $data);
		return array ('form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Chargement ajax de la liste des controles")
	 * @Route("/liste_des_controles", name="liste_des_controles")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine ()->getManager ();
		$form = $this->createForm(ControleCriteria::class);
		$this->modifyRequestForForm($request, $this->get('session')->get('controle_criteria'), $form);
		$queryBuilder = $em->getRepository(Controle::class)->listAllQueryBuilder($form->getData ());
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Filtre sur la liste des controles")
	 * @Route("/filtrer_les_controles", name="filtrer_les_controles")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(ControleCriteria::class);
		if ($request->getMethod () == 'POST') {
			$this->get('session')->set('controle_criteria', $request->request->get($form->getName ()));
			return new JsonResponse ();
			// return $this->redirect($this->generateUrl('les_controles'));
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('controle_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Creation d'un nouveau controle")
	 * @Route("/nouveau_controle", name="nouveau_controle")
	 * @Route("/{risque_id}/nouveau_controle_de_risque", name="nouveau_controle_de_risque")
	 * @Route("/{pa_id}/a_mettre_en_dispositif", name="a_mettre_en_dispositif")
	 * @Template()
	 */
	public function newAction($risque_id = null, $pa_id = null) {
		$entity = new Controle ();
		$em = $this->getDoctrine ()->getManager ();
		if ($risque_id) {
			$risque = $em->getRepository('App\Entity\Risque')->find($risque_id);
			$entity->setRisque($risque);
		}
		if ($pa_id) {
			$planaction = $em->getRepository('App\Entity\PlanAction')->find($pa_id);
			$entity->setPlanAction($planaction);
			$entity->setRisque($planaction->getRisque ());
			$entity->setCauseOfRisque($planaction->getCauseOfRisque ());
		}
		
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé!');
		
		$form = $this->createCreateForm($entity, ControleType::class, array('attr' => array (
						'em' => $em 
				) 
		));
		return array (
				'entity' => $entity,
				'form' => $form->createView () 
		);
	}
	
	/**
	 * @QMLogger(message="Envoi données saisies lors de creation d'un controle")
	 * @Route("/creer_controle", name="creer_controle")
	 * @Template("controle/new.html.twig")
	 */
	public function createAction(Request $request) {
		$em = $this->getDoctrine ()->getManager ();
		$entity = new Controle ();
		$form = $this->createForm(ControleType::class, $entity, array ('attr' => array ('em' => $em)));
		$form->handleRequest($request);
		if ($form->isValid ()) {
			$dispatcher = $this->container->get('event_dispatcher');
			$event = $this->cartoEvent;
			$event->setControle($entity);
			$dispatcher->dispatch(OrangeMainBundle::CTRL_CREATED, $event);
			$entity->getRisque()->setTobeMigrate(true);
			$em->persist($entity);
			$em->flush ();
            $this->get('session')->getFlashBag()->add('success', "Contrôle ajouté avec succés.");

            if ($request->request->has('add_another')) {
				$route = $this->generateUrl('nouveau_controle_de_risque', array (
						'risque_id' => $entity->getRisque ()->getId () 
				));
			} elseif ($request->request->has('add_and_pass')) {
				if ($entity->getRisque ()->getPlanAction ()->count ()) {
					$route = $this->generateUrl('edition_planaction', array (
							'id' => $entity->getRisque ()->getPlanAction ()->first ()->getId () 
					));
				} else {
					$route = $this->generateUrl('nouveau_planaction_de_risque', array (
							'risque_id' => $entity->getRisque ()->getId () 
					));
				}
			} else {
				$route = $this->generateUrl('apercu_risque', array (
						'id' => $entity->getRisque ()->getId () 
				));
			}
			return $this->redirect($route);
		}
		return array (
				'entity' => $entity,
				'form' => $form->createView () 
		);
	}
	
	/**
	 * @QMLogger(message="Affichage d'un controle ")
	 * @Route("/{id}/details_controle", name="details_controle", requirements={ "id"= "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine ()->getManager ();
		$controle = $em->getRepository('App\Entity\Controle')->find($id);
		
		if (! $controle)
			throw $this->createNotFoundException('Aucun controle trouvé pour cet id : ' . $id);
		
		$this->denyAccessUnlessGranted('accesOneCtrl', $controle, 'Accés non autorisé!');
		return array('entity' => $controle);
	}
	
	/**
	 * @QMLogger(message="Suppression d'un controle")
	 * @Route ("/{id}/supprimer_controle", name="supprimer_controle", requirements={ "id"= "\d+"})
	 * @Template()
	 */
	public function supprimeAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Controle')->find($id);
		if (! $entity) {
			throw $this->createNotFoundException('Aucun controle trouvé pour cet id : ' . $id);
		}
		$this->denyAccessUnlessGranted('delete', $entity, 'Accés non autorisé!');
		$risque_id = $entity->getRisque()->getId ();
		if ($request->getMethod () == 'POST') {
			$entity->getRisque()->setTobeMigrate(true);
			$em->remove($entity);
			$em->flush ();
			return $this->redirect($this->generateUrl('details_risque', array ('id' => $risque_id)));
			//return new Response($this->redirect($this->generateUrl('details_risque', array ('id' => $risque_id))));
		}
		return array ('entity' => $entity);
	}
	
	/**
	 * * @QMLogger(message="Envoi données saisies lors de modification d'un controle")
	 * @Route ("/{id}/edition_controle", name="edition_controle", requirements={ "id"= "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine ()->getManager ();
		$entity = $em->getRepository('App\Entity\Controle')->find($id);
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé!');
		$form = $this->createCreateForm($entity, ControleType::class, array('attr' => array ('em' => $em)));
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Modification d'un controle")
	 * @Route ("/{id}/modifier_controle", name="modifier_controle", requirements={ "id"= "\d+"})
	 * @Method("POST")
	 * @Template("controle/edit.html.twig")
	 */
	public function updateAction($id, Request $request) {
		$em = $this->getDoctrine ()->getManager ();
		$entity = $em->getRepository('App\Entity\Controle')->find($id);
		$form = $this->createCreateForm($entity, ControleType::class, array('attr' => array ('em' => $em)));
		if ($request->getMethod () == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid ()) {
				$dispatcher = $this->container->get('event_dispatcher');
				$event = $this->cartoEvent;
				$event->setControle($entity);
				$dispatcher->dispatch(OrangeMainBundle::CTRL_VALIDATED, $event);
				$entity->getRisque()->setTobeMigrate(true);
				$em->persist($entity);
				$em->flush ();
                $this->get('session')->getFlashBag()->add('success', "Contrôle modifié avec succés.");
				if ($request->request->has('edit_another')) {
					if (null != $controle = $entity->nextControle ()) {
						$route = $this->generateUrl('edition_controle', array (
								'id' => $controle->getId () 
						));
					} else {
						$route = $this->generateUrl('nouveau_controle_de_risque', array (
								'risque_id' => $entity->getRisque ()->getId () 
						));
					}
				} elseif ($request->request->has('edit_and_pass')) {
					if ($entity->getRisque ()->getPlanAction ()->count ()) {
						$route = $this->generateUrl('edition_planaction', array (
								'id' => $entity->getRisque ()->getPlanAction ()->first ()->getId () 
						));
					} else {
						$route = $this->generateUrl('nouveau_planaction_de_risque', array (
								'risque_id' => $entity->getRisque ()->getId () 
						));
					}
				} else {
					$route = $this->generateUrl('apercu_risque', array (
							'id' => $entity->getRisque ()->getId () 
					));
				}
				return $this->redirect($route);
			}
		}
		return array ('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @Route("/pas_by_periodicite", name="pas_by_periodicite")
	 * @Template()
	 */
	public function listPasByPeriodiciteAction(Request $request) {
		$em = $this->getDoctrine ()->getManager ();
		$arrData = $em->getRepository('App\Entity\Pas')->listByPeriodicite($request->request->get('id'));
		$output = array(0 => array ('id' => null, 'libelle' => 'Choisir un pas ...'));
		foreach($arrData as $data){
			$output [] = array('id' => $data ['id'], 'libelle' => $data ['valeur']);
		}
		$response = new Response ();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}
	
	/**
	 * @QMLogger(message="Evaluation d'un controle")
	 * @Route ("/{id}/evaluation_controle", name="evaluation_controle", requirements={"id"= "\d+"})
	 * @Template()
	 */
	public function evaluationAction(Request $request, $id) {
		$em = $this->getDoctrine ()->getManager ();
		$controle = $em->getRepository('App\Entity\Controle')->find($id);
		if (! $controle) {
			throw $this->createNotFoundException('Aucun controle trouvé pour cet id : ' . $id);
		}
		$questions = $em->getRepository('App\Entity\Question')->findAll ();
		$user = $this->getUser ();
		$quiz = new Quiz ();
		$quiz->setControle($controle);
		$quiz->setTesteur($user);
		$quiz->setValidateur($user);
		$quiz->loadQuestions($questions);
		
		$this->denyAccessUnlessGranted('evaluer', $controle, 'Accés non autorisé!');
		
		$form = $this->createCreateForm($quiz, QuizType::class, array ('attr' => array ('em' => $em)));
		if ($request->getMethod () == 'POST') {
			$form->handleRequest($request);
			if ($form->isvalid ()) {
				$note = $quiz->getRapport ();
				$maturite = $em->getRepository('App\Entity\Maturite')->findOneByValeur($note);
				$quiz->setMaturite($maturite);
				$controle->setMaturiteReel($maturite);
				$controle->getRisque()->setTobeMigrate(true);
				$em->persist($quiz);
				$em->flush ();
				return $this->redirect($this->generateUrl('details_controle', array('id' => $controle->getId())));
			}
		}
		return array ('quiz' => $quiz, 'form' => $form->createView());
	}
	
	/**
	 * @Route ("/{id}/test_controle", name="test_controle", requirements={"id"= "\d+"})
	 * @Template()
	 */
	public function executionAction($id) {
		$em = $this->getDoctrine ()->getManager ();
		$entity = new Execution ();
		$controle = $em->getRepository('App\Entity\Controle')->find($id);
		$form = $this->createCreateForm($entity, ExecutionType::class);
		return array ('entity' => $controle, 'form' => $form->createView());
	}
	
	/**
	 * @Route ("/{id}/tester_controle", name="tester_controle", requirements={ "id"= "\d+"})
	 */
	public function executeAction(Request $request, $id) {
		$em = $this->getDoctrine ()->getManager ();
		$entity = new Execution ();
		$controle = $em->getRepository('App\Entity\Controle')->find($id);
		$entity->setControle($em->getReference(Controle::class, $id));
		$entity->setExecuteur($this->getUser ());
		$form = $this->createCreateForm($entity, ExecutionType::class);
		$form->bind($request);
		$entity->upload();
		if ($form->isValid()) {
			$controle->getRisque()->setTobeMigrate(true);
			$em->persist($entity);
			$em->flush ();
			return new JsonResponse(array('url' => $this->generateUrl('details_controle', array('id' => $id))));
		}
		return $this->render('OrangeMainBundle:Controle:Execution.html.twig', array (
				'entity' => $controle, 'form' => $form->createView () 
			), new Response(null, 303));
	}
	
	/**
	 * @QMLogger(message="Extraction de la liste des controles")
	 * @Route("/exporter_les_controles", name="exporter_les_controles")
	 * @Template()
	 */
	public function exportAction(Request $request) {
		$em = $this->getDoctrine ()->getManager ();
		$form = $this->createForm(ControleCriteria::class);
		$this->modifyRequestForForm($request, $this->get('session')->get('controle_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Controle')->listAllQueryBuilder($form->getData ())->getQuery ()->getResult ();
		$traitements = $em->getRepository('App\Entity\Traitement')->findAll ();
		$data = $this->orange_main_core->getMapping('Controle')->mapForExport($queryBuilder, $traitements);
		$reporting = $this->orange_main_core->getReporting('Controle')->extract($data);
        return $reporting->getResponseAfterSave('php://output', 'Controles');
		//return $this->redirect($this->generateUrl('les_controles'));
	}
	
	/**
	 * @Route("/grille_by_carto", name="grille_by_carto")
	 */
	public function grilleBycartoAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$arrData = $em->getRepository('App\Entity\Grille')->getGrilleByCartoForMaturiteSSTE($request->request->get('id'))->getQuery()->execute();
		$output = array(0 => array('id' => '', 'libelle' => 'Choisir la maturité ...'));
		foreach ($arrData as $data) {
			$output[] = array('id' => $data->getId(), 'libelle' => $data->getLibelle());
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}
	
	/**
	 *
	 * @param \App\Entity\Controle $entity        	
	 * @return array
	 */
	protected function addRowInTable($entity) {
		return array(
				$entity->getCode(),
				$entity->getRisque() ? $entity->getRisque()->__toString() : '',
				$entity->getCauseOfRisque() ? $entity->getCauseOfRisque()->__toString() : '',
				$entity->getDescription(),
				$this->service_action->generateActionsForControle($entity)
			);
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setFilter()
	 */
	protected function setFilter(\Doctrine\ORM\QueryBuilder $queryBuilder, $aColumns, \Symfony\Component\HttpFoundation\Request $request) {
		parent::setFilter($queryBuilder, array('q.description'), $request);
	}
}
