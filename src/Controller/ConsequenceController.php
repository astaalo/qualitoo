<?php
/*
 * edited by @mariteuw
 */
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Consequence;
use App\Annotation\QMLogger;

class ConsequenceController extends BaseController {
	
	/**
	 * @QMLogger(message="Affichage des consequences ")
	 * @Route("/les_consequences", name="les_consequences")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('App\Entity\Consequence')->listAll();
		return array('entities' => $entities);
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des consequences ")
	 * @Route("/liste_des_consequences", name="liste_des_consequences")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('App\Entity\Consequence')->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Creation d'une consequence ")
	 * @Route("/nouvelle_consequence", name="nouvelle_consequence")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Consequence();
		$this->getDoctrine()->getManager()->persist($entity);
		$form = $this->createCreateForm( $entity, 'Consequence');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des données saisies lors de la creation d'une consequnce ")
	 * @Route("/creer_consequence", name="creer_consequence")
	 * @Template()
	 */
	public function createAction(Request $request) {
		$entity = new Consequence();
		$entity->setEntite($this->getUser()->getEntite());
		$form   = $this->createCreateForm($entity, 'Consequence');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('les_consequences'));
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Afficher une consequence ")
	 * @Route("/details_consequence", name="details_consequence", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$consequence = $em->getRepository('App\Entity\Consequence')->find($id);
		return array('entity' => $consequence);
	}
	
	/**
	 * @QMLogger(message="Modification d'une consequence")
	 * @Route ("/{id}/edition_consequence", name="edition_consequence", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Consequence')->find($id);
		$form = $this->createCreateForm($entity, 'Consequence');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'une consequence")
	 * @Route ("/{id}/modifier_consequence", name="modifier_consequence", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Consequence:edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Consequence')->find($id);
		$form = $this->createCreateForm($entity, 'Consequence');
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_consequences'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	
}

