<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Structure;
use App\Form\StructureType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Hierarchie;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\QueryBuilder;
use App\Annotation\QMLogger;

class StructureController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des structures")
	 * @Route("/les_structures", name="les_structures")
	 * @Template()
	 */
	public function indexAction() {
		$this->get('session')->set('structure_criteria', array());
		$em = $this->getDoctrine()->getManager();
		
		$entity= new Structure();
		//$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
		
		return array();
	}
	
	/**
	 * @QMLogger(message="filtrer la liste des structures")
	 * @Route("/filtrer_les_structures", name="filtrer_les_structures")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(new StructureType());
		if($request->getMethod()=='POST') {
			$this->get('session')->set('structure_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('structure_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des structures")
	 * @Route("/liste_des_structures", name="liste_des_structures")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(StructureType::class, new Structure());
		$this->modifyRequestForForm($request, $this->get('session')->get('structure_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository(Structure::class)->listAllQueryBuilder($criteria);
		return $this->paginate($request, $queryBuilder);
	}

	/**
	 * @QMLogger(message="Nouvelle structure")
	 * @Route("/nouvelle_structure", name="nouvelle_structure")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Structure();
		$form   = $this->CreateForm(StructureType::class, $entity);
		
		//$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');
		
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation")
	 * @Route("/creer_structure", name="creer_structure")
	 * @Template("OrangeMainBundle:Structure:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Structure();
		$form   = $this->createCreateForm($entity, 'Structure');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			if($this->getUser()->getSociete()) {
				$entity->setSociete($this->getUser()->getSociete());
			}
			$entity->setName($entity->getName());
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('les_structures'));
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Affichage details d'une structure")
	 * @Route("/{id}/details_structure", name="details_structure", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$structure = $em->getRepository('App\Entity\Structure')->find($id);
		$this->denyAccessUnlessGranted('read', $structure, 'Accés non autorisé');
		return array('entity' => $structure);
	}
	
	
	/**
	 * @QMLogger(message="Modification d'une structure")
	 * @Route ("/{id}/edition_structure", name="edition_structure", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Structure')->find($id);
		$form = $this->createCreateForm($entity, 'Structure');
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'une structure")
	 * @Route ("/{id}/modifier_structure", name="modifier_structure", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Structure:edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Structure')->find($id);
		$form = $this->createCreateForm($entity, 'Structure');
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$entity->setName($entity->getName());
				if($entity->getChildren()->count()>0)
					foreach ($entity->getChildren() as $key=>$value){
						$value -> setName($value->getName());
						$em->persist($value);
					}
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_structures'));
				// 				return new JsonResponse(array('type' => 'success', 'text' => 'Le centre a été mis à jour avec succès.'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Suppression d'une structure")
	 * @Route("/{id}/suppression_structure", name="suppression_structure")
	 * @Template()
	 */
	public function deleteAction(Request $request, $id){
		$em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('App\Entity\Structure')->find($id);
		if($entity == null)
			$this->createNotFoundException("Cette structure n'existe pas!");
		
		//$this->denyAccessUnlessGranted('delete', $entity, 'Accés non autorisé!');
		if($request->getMethod()=='POST') {
			//var_dump($entity); exit();
			//$em->remove($entity);
			//$em->flush();
			$connection = $em->getConnection();
			$statement = $connection->prepare("DELETE FROM structure WHERE id = :id");
			$statement->bindValue('id', $id);
			$statement->execute();
			return new JsonResponse(array('status' => 'success', 'text' => 'La structure a été supprimée avec succès.'));
		}
		return new Response($this->renderView('OrangeMainBundle:Structure:delete.html.twig', array('entity' => $entity)));
	}

	/**
	 * @QMLogger(message="Affichage risques dans modal")
	 * @Route("/structure_for_risque_in_modal", name="structure_for_risque_in_modal")
	 * @Template()
	 */
	public function showForRisqueInModalAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$structure = $em->getRepository('App\Entity\Structure')->findOneByFullname($request->request->get('value'));
		$entity = Hierarchie::createFromStructure($structure);
		$form = $this->createCreateForm($entity, 'Hierarchie');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @Route("/choose_structure_in_modal", name="choose_structure_in_modal")
	 * @Template()
	 */
	public function chooseStructureInModalAction(Request $request) {
		$form = $this->createForm(new ChooseStructureType());
		return array('form' => $form->createView());
	}
	

	/**
	 * @Route("/structure_by_parent", name="structure_by_parent")
	 */
	public function structureByParentAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$arrData = $em->getRepository('App\Entity\Structure')->listByParent($request->request->get('id'))->getQuery()->execute();
		$output = array(0 => array('id' => '', 'libelle' => 'Choisir une entité ...'));
		foreach ($arrData as $data) {
			$output[] = array('id' => $data->getId(), 'libelle' => $data->getName());
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}
	 
	
	
	/**
	 * (non-PHPdoc)
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setFilter()
	 */
	protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
		parent::setFilter($queryBuilder, array('q.code', 'q.libelle'), $request);
	}
	
	/**
	 * @param \App\Entity\Structure $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$entity->getTypeStructure()?$entity->getTypeStructure()->getLibelle():null,
	  			$entity->getParent() ? $entity->getParent()->__toString() : null,
	  			$this->service_action->generateActionsForStructure($entity)
	  	);
	}
}

