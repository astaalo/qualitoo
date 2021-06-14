<?php
namespace App\Controller;

use App\Entity\RisqueHasCause;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CauseType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Cause;
use Symfony\Component\HttpFoundation\Response;
use App\Criteria\CauseCriteria;
use App\Criteria\RisqueHasCauseCriteria;
use Doctrine\ORM\EntityNotFoundException;
use App\Annotation\QMLogger;

class CauseController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des causes")
	 * @Route("/les_causes", name="les_causes")
	 * @Template()
	 */
	public function indexAction() {
		$entity = new Cause();
		$this->get('session')->set('risquehascause_criteria', array());
		//$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisée!');
		return array();
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des causes")
	 * @Route("/liste_des_causes", name="liste_des_causes")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(RisqueHasCauseCriteria::class, new RisqueHasCause());
		$this->modifyRequestForForm($request, $this->get('session')->get('risquehascause_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\RisqueHasCause')->listAllQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Filtre sur la liste des causes")
	 * @Route("/filtrer_les_causes", name="filtrer_les_causes")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(RisqueHasCauseCriteria::class, new RisqueHasCause());
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risquehascause_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('risquehascause_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @Route("/cause_by_risque", name="cause_by_risque")
	 */
	public function listByRisqueAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$risques = $em->getRepository('App\Entity\Cause')->getByRisqueId($request->request->get('id'));
		$output = array(array('id' => "", 'libelle' => 'Choisir une cause ...'));
		foreach ($risques as $risque) {
			$output[] = array('id' => $risque['id'], 'libelle' => $risque['libelle']);
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}

	/**
	 * @QMLogger(message="Creation d'une nouvelle cause")
	 * @Route("/nouvelle_cause", name="nouvelle_cause")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Cause();
		$form   = $this->createCreateForm($entity, 'Cause');
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisée!');
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation de la cause")
	 * @Route("/creer_Cause", name="creer_cause")
	 * @Template()
	 */
	public function createAction(Request $request) {
		$entity = new Cause();
		$form   = $this->createCreateForm($entity, 'Cause');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity->getRisque()->setTobeMigrate(true);
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add('success', "La cause a été ajoutée avec succés.");
			return $this->redirect($this->generateUrl('les_causes'));
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Affichage d'une cause")
	 * @Route("/{id}/details_cause", name="details_cause", requirements={ "id"=  "\d+"})
	 * @Template() 
	 * 
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$entity = new Cause();
		$cause = $em->getRepository('App\Entity\RisqueHasCause')->find($id);
		if(!$cause)
			throw new EntityNotFoundException('Entité avec ce id non trouvé ');
			
		//$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisée!');
		return array('entity' => $cause);
	}
	
	/**
	 * @QMLogger(message="Modification d'une cause")
	 * @Route ("/{id}/edition_cause", name="edition_cause", requirements={ "id"=  "\d+"})
	 * @Route ("/{id}/{page}/edition_cause", name="edition_cause", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id, $page = 0) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Cause')->find($id);
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisée!');
		$form = $this->createCreateForm($entity, 'Cause');
		return array('entity' => $entity, 'form' => $form->createView(), 'page' => $page);
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modfication de la cause")
	 * @Route ("/{id}/{page}/modifier_cause", name="modifier_cause", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Cause:edit.html.twig")
	 */
	public function updateAction($id, $page) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Cause')->find($id);
		$form = $this->createCreateForm($entity, 'Cause');
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$risques_of_cause = $entity->getRisqueHasCause();
				foreach($risques_of_cause as $roc) {
					$roc->getRisque()->setTobeMigrate(true);
					$em->persist($roc->getRisque());
				}
				$em->persist($entity);
				$em->flush();
				if($page) {
					return new JsonResponse(array('status' => 'success', 'text' => 'La cause a bien été mise à jour'));
				} else {
					$this->get('session')->getFlashBag()->add('success', 'La cause a bien été mise à jour');
				}
			}
		}
		return array('entity' => $entity, 'form' => $form->createView(), 'page' => $page);
	}
	
	/**
	 * @QMLogger(message="Extraction de la liste des causes ")
	 * @Route("/exporter_les_causes", name="exporter_les_causes")
	 * @Template()
	 */
	public function exportAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new CauseCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('risquehascause_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Cause')->listAllQueryBuilder($form->getData());
		$data = $this->get('orange_main.core')->getMapping('Cause')->mapForBasicExport($queryBuilder->getQuery()->getResult());
		$reporting = $this->get('orange_main.core')->getReporting('Cause')->extractByCause($data);
		$reporting->getResponseAfterSave('php://output', 'Extraction des causes');
	}
	
	/**
	 * @param \App\Entity\RisqueHasCause $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
  			$entity->getRisque()->getMenace()?$entity->getRisque()->getMenace()->__toString():null,
  			$entity->__toString(),
  			$entity->getCause()->getFamille() ? $entity->getCause()->getFamille()->getLibelle() : null,
  			$entity->getGrille() ? $entity->getGrille()->getNote()->__toString() : '<span style="color: red;">Aucune évaluation</span>',
  			$this->service_action->generateActionsForCause($entity)
  		);
	}
}