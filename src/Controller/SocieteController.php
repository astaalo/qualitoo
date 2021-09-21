<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Societe;
use App\Form\SocieteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Relance;
use App\Annotation\QMLogger;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class SocieteController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des sociétés")
	 * @Route("/les_societes", name="les_societes")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository(Societe::class)->listAll();
		return array('entities' => $entities);
	}
	
	/**
	 * @QMLogger(message="Chargement ajax société") 
	 * @Route("/liste_des_societes", name="liste_des_societes")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('App\Entity\Societe')->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}

	/**
	 * @QMLogger(message="Nouvelle société")
	 * @Route("/nouvelle_societe", name="nouvelle_societe")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Societe();
		$form   = $this->createCreateForm($entity, SocieteType::class);
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Envoi des données saisies lors de la creation d'une societe")
	 * @Route("/creer_societe", name="creer_societe")
	 * @Template("societe/new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Societe();
		$form   = $this->createCreateForm($entity, SocieteType::class);
		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				$em = $this->getDoctrine()->getManager();
				//$entity->upload();
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_societes'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Affichage d'une societe")
	 * @Route("/{id}/details_societe", name="details_societe", requirements={ "id"=  "\d+"})
	 * @Template()
	 *
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$societe = $em->getRepository('App\Entity\Societe')->find($id);
		return array('entity' => $societe);
	}
	
	/**
	 * @QMLogger(message="Modification d'une societe")
	 * @Route ("/{id}/edition_societe", name="edition_societe", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Societe')->find($id);
		$form = $this->createCreateForm($entity, SocieteType::class);
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des données saisies lors de la modification d'une societe")
	 * @Route ("/{id}/modifier_societe", name="modifier_societe", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("societe/edit.html.twig")
	 */
	public function updateAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Societe')->find($id);
		$form = $this->createCreateForm($entity, SocieteType::class);

		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				$entity->upload();
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_societes'));
				// 				return new JsonResponse(array('type' => 'success', 'text' => 'Le centre a été mis à jour avec succès.'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @param \App\Entity\Societe $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
            // sprintf('<img src="%s" />', $this->get('twig.extension.assets')->getAssetUrl($entity->getWebPath())),
            sprintf('<img src="%s" />', $this->service_assets_extension->getAssetUrl($entity->getWebPath())),
            sprintf('<a href="%s">%s<a/>', $this->generateUrl('details_societe', array('id' => $entity->getId())), $entity->getLibelle()),
            $this->service_status->generateStatusForSociete($entity),
            $this->service_action->generateActionsForSocite($entity)
	  	);
	}
}

