<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PlanAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Criteria\PlanActionCriteria;
use App\OrangeMainBundle;
use App\Event\CartoEvent;
use App\Annotation\QMLogger;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class PlanActionController extends BaseController {
	
	/**
	 * @QMLogger(message="Affichage des plans d'action")
	 * @Route("/les_planactions", name="les_planactions")
	 * @Template()
	 */
	public function indexAction() {
		$form = $this->createForm(new PlanActionCriteria());
		$data = $this->get('session')->get('planaction_criteria');
		$this->get('session')->set('planaction_criteria', $data ? $data : array());
		return array('form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Affichage des dispositifs")
	 * @Route("/les_dispositifs", name="les_dispositifs")
	 * @Template()
	 */
	public function dispositifAction() {
		$this->get('session')->set('dispositif_criteria', array());
		return array();
	}
	
	/**
	 * @QMLogger(message="Filtre sur les plans d'action")
	 * @Route("/filtrer_les_planactions", name="filtrer_les_planactions")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(new PlanActionCriteria());
		if($request->getMethod() == 'POST') {
			$this->get('session')->set('planaction_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('planaction_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Filtre sur les dispositifs")
	 * @Route("/filtrer_les_dispositifs", name="filtrer_les_dispositifs")
	 * @Template()
	 */
	public function filterDispositifAction(Request $request) {
		$form = $this->createForm(new PlanActionCriteria());
		if($request->getMethod() == 'POST') {
			$this->get('session')->set('dispositif_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('dispositif_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des plans d'action")
	 * @Route("/liste_des_planactions", name="liste_des_planactions")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new PlanActionCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('planaction_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\PlanAction')->listAllQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des dispositifs")
	 * @Route("/liste_des_dispositifs", name="liste_des_dispositifs")
	 * @Template()
	 */
	public function listDispositifAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new PlanActionCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('dispositif_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\PlanAction')->listAllQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder, 'addRowInTableForDispositif');
	}
	
	/**
	 * @QMLogger(message="Ajout d'un plans d'action")
	 * @Route("/nouveau_planaction", name="nouveau_planaction")
	 * @Route("/{risque_id}/nouveau_planaction", name="nouveau_planaction_de_risque")
	 * @Route("/{controle_id}/saisie_planaction", name="saisie_planaction")
	 * @Template()
	 */
	public function newAction($risque_id = null, $controle_id = null) {
		$entity = new PlanAction();
		$controle = $controle_id ? $this->getDoctrine()->getManager()->getRepository('OrangeMainBundle:Controle')->find($controle_id) : null;
		if($controle) {
			$entity->setControle($controle);
			$entity->setRisque($controle->getRisque());
			$entity->setCauseOfRisque($controle->getCauseOfRisque());
		} elseif($risque_id) {
			$entity->setRisque($this->getDoctrine()->getManager()->getRepository('OrangeMainBundle:Risque')->find($risque_id));
		}
		$form = $this->createCreateForm($entity, 'PlanAction', array(
				'attr' => array(
						'em' => $this->getDoctrine()->getManager(),
						'type_statut' => $this->getMyParameter('types', array(
								'statut',
								'plan_action' 
						)) 
				) 
		));
		
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé!');
		
		return array(
				'entity' => $entity,
				'form' => $form->createView(),
				'risque_id' => $risque_id 
		);
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisie lors d'un ajout de plans d'action")
	 * @Route("/creer_planaction", name="creer_planaction")
	 * @Method("POST")
	 * @Template("OrangeMainBundle:PlanAction:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new PlanAction();
		$em = $this->getDoctrine()->getManager();
		$form = $this->createCreateForm($entity, 'PlanAction', array(
				'attr' => array('em' => $em, 'type_statut' => $this->getMyParameter('types', array('statut', 'plan_action'))) 
			));
		$form->handleRequest($request);
		if($form->isValid()) {
			$dispatcher = $this->container->get('event_dispatcher');
			$event = new CartoEvent($this->container);
			$entity->setStatut($em->getReference('OrangeMainBundle:Statut', $this->getMyParameter('ids', array('statut', 'plan_action', 'non_fait'))));
			$event->setPlanAction($entity);
			$dispatcher->dispatch(OrangeMainBundle::PA_CREATED, $event);
			$entity->getRisque()->setTobeMigrate(true);
			$em->persist($entity);
			$em->flush();
			if($request->request->has('add_another')) {
				$route = $this->generateUrl('nouveau_planaction_de_risque', array('risque_id' => $entity->getRisque()->getId()));
			} elseif($request->request->has('add_and_pass')) {
				if($entity->getRisque()->getEvaluation()->count()) {
					$route = $this->generateUrl('edition_evaluation', array('id' => $entity->getRisque()->getEvaluation()->last()->getId()));
				} else {
					$route = $this->generateUrl('nouvelle_evaluation', array('id' => $entity->getRisque()->getId()));
				}
			} else {
				$route = $this->generateUrl('apercu_risque', array('id' => $entity->getRisque()->getId()));
			}
			return $this->redirect($route);
		}
		return array('entity' => $entity, 'form' => $form->createView(), 'risque_id' => $entity->getRisque()->getId());
	}
	
	/**
	 * @QMLogger(message="Details d'un plans d'action")
	 * @Route("/{id}/details_planaction", name="details_planaction", requirements={ "id"= "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$planaction = $em->getRepository('App\Entity\PlanAction')->find($id);
		if(! $planaction)
			throw $this->createNotFoundException('Aucun controle trouvé pour cet id : ' . $id);
		// $this->denyAccessUnlessGranted('accesOnePa', $planaction, 'Accés non autorisé!');
		return array('entity' => $planaction);
	}
	
	/**
	 * @QMLogger(message="Modification d'un plan d'action")
	 * @Route("/{id}/edition_planaction", name="edition_planaction", requirements={ "id"= "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\PlanAction')->find($id);
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé!');
		$form = $this->createCreateForm($entity, 'PlanAction', array(
				'attr' => array('type_statut' => $this->getMyParameter('types', array('statut', 'plan_action')), 'em' => $em) 
			));
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Mdification d'un plan d'action")
	 * @Route("/{id}/modifier_planaction", name="modifier_planaction", requirements={ "id"= "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:PlanAction:edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\PlanAction')->find($id);
		$form = $this->createCreateForm($entity, 'PlanAction', array(
				'attr' => array('type_statut' => $this->getMyParameter('types', array('statut', 'plan_action')), 'em' => $em) 
			));
		$request = $this->get('request');
		if($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if($form->isValid()) {
				$dispatcher = $this->container->get('event_dispatcher');
				$event = new CartoEvent($this->container);
				$event->setPlanAction($entity);
				$dispatcher->dispatch(OrangeMainBundle::PA_VALIDATED, $event);
				$entity->getRisque()->setTobeMigrate(true);
				$em->persist($entity);
				$em->flush();
				if($request->request->has('edit_another')) {
					$route = $this->generateUrl('nouveau_planaction_de_risque', array('risque_id' => $entity->getRisque()->getId()));
				} elseif($request->request->has('edit_and_pass')) {
					if($entity->getRisque()->getEvaluation()->count()) {
						$route = $this->generateUrl('edition_evaluation', array('id' => $entity->getRisque()->getEvaluation()->last()->getId()));
					} else {
						$route = $this->generateUrl('nouvelle_evaluation', array('id' => $entity->getRisque()->getId()));
					}
				} else {
					$route = $this->generateUrl('apercu_risque', array('id' => $entity->getRisque()->getId()));
				}
				return $this->redirect($route);
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Extraction des plans d'action")
	 * @Route("/exporter_les_planactions", name="exporter_les_planactions")
	 * @Template()
	 */
	public function exportAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new PlanActionCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('planaction_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\PlanAction')->listAllQueryBuilder($form->getData())->getQuery()->getResult();
		$data = $this->get('orange_main.core')->getMapping('PlanAction')->mapForExport($queryBuilder);
		$reporting = $this->get('orange_main.core')->getReporting('PlanAction')->extract($data);
		return $reporting->getResponseAfterSave('php://output', 'PlanActions');
	}
	
	/**
	 * @QMLogger(message="Extraction des dispositifs")
	 * @Route("/exporter_les_dispositifs", name="exporter_les_dispositifs")
	 * @Template()
	 */
	public function exportDispositifAction(Request $request) {
		$form = $this->createForm(new PlanActionCriteria());
		if($request->getMethod() == 'POST') {
			$this->get('session')->set('dispositif_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('dispositif_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Suppression d'un plan d'action")
	 * @Route("/{id}/supprimer_pa", name="supprimer_pa")
	 * @Template()
	 */
	public function deleteAction(Request $request,$id){
		$em = $this->getDoctrine()->getEntityManager();
		$planAction = $em->getRepository('App\Entity\PlanAction')->find($id);
		if(!$planAction)
			throw new EntityNotFoundException("plan d'action n'existe pas!");
		
		$this->denyAccessUnlessGranted('delete', $planAction, 'Accés non autorisé!');
		if($request->getMethod()=='POST'){
			$em->remove($planAction);
			$em->flush();
			return new JsonResponse(array('status' => 'success', 'text' => 'Le plan d\'action a bien été supprimé avec succés'));
			
		}
		return new Response($this->renderView('OrangeMainBundle:PlanAction:delete.html.twig', array('entity' => $planAction)));
	}
	
	/**
	 * @param \App\Entity\PlanAction $entity        	
	 * @return array
	 */
	protected function addRowInTable($entity) {
		return array(
				$entity->getCode(),
				$entity->getRisque() ? $entity->getRisque()->__toString() : null,
				$entity->getLibelle(),
				$entity->getPorteur() ? $entity->getPorteur()->__toString() : null,
				$entity->getDateFin() != null ? $entity->getDateFin()->format('d/m/Y') : '',
				$entity->getStatut() ? $entity->getStatut()->__toString() : null,
				$this->service_action->generateActionsForPlanAction($entity)
			);
	}
	
	/**
	 * @param \App\Entity\PlanAction $entity        	
	 * @return array
	 */
	protected function addRowInTableForDispositif($entity) {
		return array(
				$entity->getCode(),
				$entity->getLibelle(),
				$entity->getDateDebut() != null ? $entity->getDateDebut()->format('d/m/Y') : '',
				$entity->getDateFin() != null ? $entity->getDateFin()->format('d/m/Y') : '',
				$entity->getPorteur() ? $entity->getPorteur()->__toString() : null,
				$entity->getSuperviseur() ? $entity->getSuperviseur()->__toString() : null,
				$this->service_action->generateActionsForPlanAction($entity)
			);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setFilter()
	 */
	protected function setFilter(\Doctrine\ORM\QueryBuilder $queryBuilder, $aColumns, \Symfony\Component\HttpFoundation\Request $request) {
		parent::setFilter($queryBuilder, array('q.libelle'), $request);
	}
}
