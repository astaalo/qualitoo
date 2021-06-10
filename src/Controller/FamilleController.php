<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FamilleType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Famille;
use App\Annotation\QMLogger;

class FamilleController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des familles")
	 * @Route("/les_familles", name="les_familles")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('App\Entity\Famille')->listAll();
		return array('entities' => $entities);
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des familles")
	 * @Route("/liste_des_familles", name="liste_des_familles")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('App\Entity\Famille')->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}

	/**
	 * @QMLogger(message="CReation d'une famille")
	 * @Route("/nouvelle_famille", name="nouvelle_famille")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Famille();
		$form   = $this->createCreateForm($entity, 'Famille');
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'une famille")
	 * @Route("/creer_famille", name="creer_famille")
	 * @Template()
	 */
	public function createAction(Request $request) {
		$entity = new Famille();
		$form   = $this->createCreateForm($entity, 'Famille');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('les_familles'));
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Details d'un famille")
	 * @Route("/{id}/details_famille", name="details_famille", requirements={ "id"=  "\d+"})
	 * @Template()
	 *
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$famille = $em->getRepository('App\Entity\Famille')->find($id);
		return array('entity' => $famille);
	}
	
	/**
	 * @QMLogger(message="Modification d'une famille")
	 * @Route ("/{id}/edition_famille", name="edition_famille", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Famille')->find($id);
		$form = $this->createCreateForm($entity, 'Famille');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'une famille")
	 * @Route ("/{id}/modifier_famille", name="modifier_famille", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Famille:edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Famille')->find($id);
		$form = $this->createCreateForm($entity, 'Famille');
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_familles'));
// 				return new JsonResponse(array('type' => 'success', 'text' => 'Le centre a été mis à jour avec succès.'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Suppression d'une famille")
	 * @Route("/{id}/supprimer_famille", name="supprimer_famille", requirements={ "id"=  "\d+"}) 
	 */
	public function deleteAction($id){
// 		exit;
		return array();
	}
	
	/**
	 * @param \App\Entity\Entite $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$entity->getParent() ? $entity->getParent()->getLibelle() : null,
	  			$this->service_action->generateActionsForFamille($entity)
	  	);
	}
}
