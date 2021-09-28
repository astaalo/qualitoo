<?php
namespace App\Controller;

use App\Entity\Processus;
use App\Form\ProcessusType;
use App\Annotation\QMLogger;
use Doctrine\ORM\QueryBuilder;
use App\Repository\ProcessusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProcessusController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des processus")
	 * @Route("/les_processus", name="les_processus")
	 * @Template()
	 */
	public function indexAction() {
		$entity= new Processus();
		$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
		if(!$this->get('session')->get('processus_criteria')) {
			$this->get('session')->set('processus_criteria', array());
		}
		return array();
	}
	
	/**
	 * @QMLogger(message="Filtre sur les processus")
	 * @Route("/filtrer_les_processus", name="filtrer_les_processus")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$processus = new Processus();
		$form = $this->createForm(ProcessusType::class, $processus);
		if($request->getMethod()=='POST') {
			$this->get('session')->set('processus_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('processus_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des processus")
	 * @Route("/liste_des_processus", name="liste_des_processus")
	 * @Template()
	 */
	public function listAction(Request $request, ProcessusRepository $processusRepo) {
		$processus = new Processus();
		$form = $this->createForm(ProcessusType::class, $processus);
		$this->modifyRequestForForm($request, $this->get('session')->get('processus_criteria'), $form);
		$process = $form->getData();
		$queryBuilder = $processusRepo->listAll($process);
		//dd($queryBuilder);
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Affichage d'un processus")
	 * @Route("/{id}/details_processus", name="details_processus", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$processus = $em->getRepository('App\Entity\Processus')->find($id);
		$this->denyAccessUnlessGranted('read', $processus, 'Accés non autorisé');
		return array('entitie' => $processus);
	}
	
	/**
	 * @QMLogger(message="Suppression d'un processus")
	 * @Route("/{id}/supprimer_processus", name="supprimer_processus", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function deleteAction(Request $request, $id){
		$em = $this->getDoctrine()->getManager();
		$processus = $em->getRepository('App\Entity\Processus')->find($id);
		//$activites=$processus->getActivite();
		if (! $processus) {
			throw $this->createNotFoundException('Aucun processus trouvé pour cet id : ' . $id);
		}
		$this->denyAccessUnlessGranted('delete', $processus, 'Accés non autorisé');
		if ($request->getMethod () == 'POST') {
			/*if(count($activites) > 0 || count($processus->getChildren()) > 0 || count($processus->getProjet()) > 0) {
				$this->get('session')->getFlashBag()->add('error', "Le processus ne peut pas etre supprimé. Il est lié à des activités ou sous-processus ou projets.");
			} else {
				$em->remove($processus);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "Le processus a été supprimé avec succés.");
			}*/
			return $this->redirect($this->generateUrl('les_processus'));
		}
		return array ('entity' => $processus);
	}

	/**
	 * @QMLogger(message="Creation d'un processus")
	 * @Route("/{id}/ajout_processus", name="ajout_processus", requirements={ "id"=  "\d+"})
	 * @Route("/nouveau_processus", name="nouveau_processus")
	 * @Template()
	 */
	public function newAction($id = null) {
		$entity = new Processus();
		if($id) {
			$processus = $this->getDoctrine()->getManager()->getRepository(Processus::class)->find($id);
			$entity->setParent($processus);
		}
		$form   = $this->createForm(ProcessusType::class, $entity);
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un processus")
	 * @Route("/{id}/ajouter_processus", name="ajouter_processus")
	 * @Route("/creer_processus", name="creer_processus")
	 * @Template("processus/new.html.twig")
	 */
	public function createAction(Request $request, $id = null) {
		$entity = new Processus();
		$form   = $this->createCreateForm($entity, ProcessusType::class);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
            //$entity->setLibelleSansCarSpecial($this->replaceSpecialChars($entity->getLibelle()));
			$em->persist($entity);
			$em->flush();$this->get('session')->getFlashBag()->add('success', "Processus ajouté avec succés.");
            if($entity->getParent()) {
				return $this->redirect($this->generateUrl('details_processus', array('id' => $entity->getParent()->getId())));
			} else{
				return $this->redirect($this->generateUrl('les_processus'));
			}	
		}
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}
	
	/**
	 * @QMLogger(message="Modification d'un processus")
	 * @Route ("/{id}/edition_processus", name="edition_processus", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Processus')->find($id);
		$form = $this->CreateForm(ProcessusType::class, $entity);
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'un processus")
	 * @Route ("/{id}/modifier_processus", name="modifier_processus", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("processus/edit.html.twig")
	 */
	public function updateAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Processus')->find($id);
		$form = $this->createCreateForm($entity, ProcessusType::class);
		$this->denyAccessUnlessGranted('update', $this->getUser(),'Accés non autorisé!');
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				/*$entity->setName($entity->getName());
				if($entity->getChildren()->count()>0)
					foreach ($entity->getChildren() as $key=>$value){
						$value -> setName($value->getName());
						$em->persist($value);
					}*/
				$em->persist($entity);
				$em->flush();
				//$this->get('session')->getFlashBag()->add('success', "La modification s'est effectuée avec succès.");
				return $this->redirect($this->generateUrl('les_processus'));
				//return new JsonResponse(array('type' => 'success', 'text' => 'Le centre a été mis à jour avec succès.'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	  
	/**
	 * (non-PHPdoc)
	 * @see \App\Controller\BaseController::setFilter()
	 */
	protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
		parent::setFilter($queryBuilder, array('p.code', 'p.libelle'), $request);
	}
	
	/**
	 * @param \App\Entity\Processus $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
			$entity->getLibelle(),
			$entity->getDescription(),
			$entity->getTypeProcessus()->getLibelle(),
			$entity->getStructure()->getLibelle(),
			$this->service_action->generateActionsForProcessus($entity)
		);
	}
}

