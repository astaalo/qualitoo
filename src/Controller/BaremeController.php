<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Annotation\QMLogger;

class BaremeController extends BaseController {
	/**
	 * @QMLogger(message="Affichage des baremes")
	 * @Route("/les_baremes", name="les_baremes")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('OrangeMainBundle:TypeGrille')->findByTypeEvaluationId($this->getMyParameter('ids', array('type_evaluation', 'cause')));
		return array('entities' => $entities);
	}
	
	/**
	 * @QMLogger(message="Affichage des baremes")
	 * @Route ("/{id}/edition_bareme", name="edition_bareme", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:TypeGrille')->find($id);
		if(!$entity || $entity->getTypeEvaluation()->getId()!=$this->getMyParameter('ids', array('type_evaluation', 'cause'))) {
			$this->createAccessDeniedException("Vous n'avez pas le droit de modifier ce barême");
		}
		$form = $this->createCreateForm($entity, 'TypeGrille', array('attr' => array('em' => $em)));
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Modification d'un bareme")
	 * @Route ("/{id}/modifier_bareme", name="modifier_bareme", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Bareme:edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:TypeGrille')->find($id);
		$form = $this->createCreateForm($entity, 'TypeGrille', array('attr' => array('em' => $em)));
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_baremes'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Suppression d'un bareme")
	 * @Route("/{id}/suppression_bareme", name="suppression_bareme", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function deleteAction($id) {
		return array();
	}
	
}
