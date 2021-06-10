<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Avancement;
use App\Annotation\QMLogger;

class AvancementController extends BaseController {

	/**
	 * @QMLogger(message="Affichage de la liste des avancements")
	 * @Route("/les_avancements", name="les_avancements")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('App\Entity\Avancement')->listAll();
		return array('entities' => $entities);
	}
	
	/**
	 * @QMLogger(message="Chargement ajax de la liste des avancements")
	 * @Route("/liste_des_avancements", name="liste_des_avancements")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('App\Entity\Avancement')->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}

	/**
	 * @QMLogger(message="Creation d'un avancement")
	 * @Route("/{id}/nouveau_avancement", name="nouveau_avancement")
	 * @Template()
	 */
	public function newAction($id) {
		$entity = new Avancement();
		$em = $this->getDoctrine()->getManager();
		$pa = $em->getRepository('App\Entity\PlanAction')->find($id);
		$entity->setPlanAction($pa);
		$form   = $this->createCreateForm($entity, 'Avancement');
		return array('entity' => $entity, 'form' => $form->createView(), 'id'=>$id);
	}

	/**
	 * @Route("/{id}/creer_avancement", name="creer_avancement")
	 * @method({"GET", "POST"})
	 * @Template()
	 */
	public function createAction(Request $request,$id) {
		$entity = new Avancement();
		$em = $this->getDoctrine()->getManager();
		$form   = $this->createCreateForm($entity, 'Avancement');
		$pa = $em->getRepository('App\Entity\PlanAction')->find($id);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity->setPlanAction($pa);
			$entity->setActeur($this->getUser());
			$entity->setEtat(1);
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('dashboard'));
		}
		return array('entity' => $entity, 'form' => $form->createView(), 'id'=>$id);
	}
	
	/**
	 * @QMLogger(message="Affichage d'un avancement")
	 * @Route("/{id}/details_avancement", name="details_avancement", requirements={ "id"=  "\d+"})
	 * @Template() 
	 * 
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$cause = $em->getRepository('App\Entity\Avancement')->find($id);
		return array('entitie' => $cause);
	}
	
	/**
	 * @QMLogger(message="Modification d'un avancement")
	 * @Route ("/{id}/edition_avancement", name="edition_avancement", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Avancement')->find($id);
		$form = $this->createCreateForm($entity, 'Avancement');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @Route ("/{id}/modifier_avancement", name="modifier_avancement", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Avancement:edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Avancement')->find($id);
		$form = $this->createCreateForm($entity, 'Avancement');
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('dashboard'));
// 				return new JsonResponse(array('type' => 'success', 'text' => 'Le centre a été mis à jour avec succès.'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Suppression d'un avancement")
	 * @Route("/{id}/supprimer_avancement", name="supprimer_avancement", requirements={ "id"=  "\d+"}) 
	 * @Template()
	 */
	public function deleteAction($id) { 
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Avancement')->find($id);
		if(!$entity) {
			throw $this->createNotFoundException('Aucun avancement trouvé pour cet id : '.$id);
		}
		if($this->getRequest()->getMethod()=='POST') {
// 			if($entity->isDeletable()) {
				$em->remove($entity);
				$em->flush();
// 				return new JsonResponse(array('type' => 'notice', 'text' => "L'action a été supprimée avec succés."));
		}
		else {
				return array('entity' => $entity);
			}
	}
	
	/**
	 * @param \App\Entity\Entite $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$entity->getParent() ? $entity->getParent()->getName() : null,
	  			$this->service_status->generateStatusForUtilisateur($entity),
	  			$this->service_action->generateActionsForUtilisateur($entity)
	  	);
	}
}

