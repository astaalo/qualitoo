<?php
namespace App\Controller;

use App\Form\HierarchieType;
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
use App\Repository\StructureRepository;

class StructureController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des structures")
	 * @Route("/les_structures", name="les_structures")
	 * @Template()
	 */
	public function indexAction() {
		$entity= new Structure();
		$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
		if(!$this->get('session')->get('structure_criteria')) {
			$this->get('session')->set('structure_criteria', array());
		}
		return array();
	}
	
	/**
	 * @QMLogger(message="filtrer la liste des structures")
	 * @Route("/filtrer_les_structures", name="filtrer_les_structures")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(StructureType::class);
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
	public function listAction(Request $request,StructureRepository $structureRepo) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(StructureType::class, new Structure());
		$this->modifyRequestForForm($request, $this->get('session')->get('structure_criteria'), $form);
		$criteria = $form->getData();
		//dd($criteria);
		$queryBuilder = $structureRepo->listAll($criteria);
		//dd($queryBuilder);
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
		
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');
		
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation")
	 * @Route("/creer_structure", name="creer_structure")
	 * @Template("structure/new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Structure();
		$form   = $this->createCreateForm($entity, StructureType::class);
		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_structures'));
			}
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
		$form = $this->createCreateForm($entity, StructureType::class);
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'une structure")
	 * @Route ("/{id}/modifier_structure", name="modifier_structure", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("structure/edit.html.twig")
	 */
	public function updateAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Structure')->find($id);
		$form = $this->createCreateForm($entity, StructureType::class);
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
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
				//return new JsonResponse(array('type' => 'success', 'text' => 'Le centre a été mis à jour avec succès.'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Suppression d'un structure")
	 * @Route("/{id}/supprimer_structure", name="supprimer_structure", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function deleteAction(Request $request, $id){
		$em = $this->getDoctrine()->getManager();
		$structure = $em->getRepository('App\Entity\Structure')->find($id);
		//$activites=$structure->getActivite();
		if (! $structure) {
			throw $this->createNotFoundException('Aucun structure trouvé pour cet id : ' . $id);
		}
		
		$this->denyAccessUnlessGranted('delete', $structure, 'Accés non autorisé');
		if ($request->getMethod () == 'POST') {
			/*if(count($activites) > 0 || count($structure->getChildren()) > 0 || count($structure->getProjet()) > 0) {
				$this->get('session')->getFlashBag()->add('error', "Le structure ne peut pas etre supprimé. Il est lié à des activités ou sous-structure ou projets.");
			} else {
				$em->remove($structure);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "Le structure a été supprimé avec succés.");
			}*/
			$em->remove($structure);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "La structure a été supprimé avec succés.");
			return $this->redirect($this->generateUrl('les_structure'));
		}
		return array ('entity' => $structure);
	}

	/**
	 * @Route("/structure_by_parent", name="structure_by_parent")
	 */
		/*public function structureByParentAction(Request $request) {
			$em = $this->getDoctrine()->getManager();
			$arrData = $em->getRepository('App\Entity\Structure')->listByParent($request->request->get('id'))->getQuery()->execute();
			$output = array(0 => array('id' => '', 'libelle' => 'Choisir une entité ...'));
			foreach ($arrData as $data) {
				$output[] = array('id' => $data->getId(), 'libelle' => $data->getName());
			}
			$response = new Response();
			$response->headers->set('Content-Type', 'application/json');
			return $response->setContent(json_encode($output));
		}*/
	 
	
	/**
	 * (non-PHPdoc)
	 * @see \App\BaseController::setFilter()
	 */
	//protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
	//	parent::setFilter($queryBuilder, array('q.code', 'q.libelle'), $request);
	//}
	
	
	/**
	 * @param \App\Entity\Structure $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$entity->getTypeStructure()?$entity->getTypeStructure()->getLibelle():null,
	  			$entity->getParent() ? $entity->getParent()->__toString() : null,
	  			$this->service_action->generateActionsForStructures($entity)
	  	);
	}
}

