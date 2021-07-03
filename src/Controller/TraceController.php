<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Trace;
use App\Form\TraceType;
use Symfony\Component\HttpFoundation\JsonResponse;

class TraceController extends BaseController
{
	/**
	 * @Route("/les_traces", name="les_traces")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('App\Entity\Trace')->listAll();
		return array('entities' => $entities);
	}
	
	/**
	 * @Route("/liste_des_traces", name="liste_des_traces")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('App\Entity\Trace')->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @Route("/nouvelle_trace", name="nouvelle_trace")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Trace();
		$form   = $this->createCreateForm($entity, 'Trace');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @Route("/creer_trace", name="creer_trace")
	 * @Template()
	 */
	public function createAction(Request $request) {
		$entity = new Trace();
		$form   = $this->createCreateForm($entity, 'Trace');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('les_traces'));
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @Route("/details_trace", name="details_trace", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$trace = $em->getRepository('App\Entity\Trace')->find($id);
		return array('entity' => $trace);
	}
	
	/**
	 * @Route ("/{id}/edition_trace", name="edition_trace", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Trace')->find($id);
		$form = $this->createCreateForm($entity, 'Trace');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @Route ("/{id}/modifier_trace", name="modifier_trace", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Trace:edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Trace')->find($id);
		$form = $this->createCreateForm($entity, 'Trace');
		$request = $request;
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_traces'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	
}
