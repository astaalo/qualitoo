<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Risque;
use App\Criteria\RisqueCriteria;
use App\Annotation\QMLogger;

class RestitutionController extends BaseController {


	/**
	 * @QMLogger(message="Matrice de restitution")
	 * @Route("/les_matrices", name="les_matrices")
	 * @Template()
	 */
	public function indexAction() {
		$this->get('session')->set('restitution_criteria', array());
		return $this->redirect($this->generateUrl('la_restitution', array('carto' => $this->getMyParameter('ids', array('carto', 'metier')), 'type' => 4)));
	}
	
	/**
	 * @QMLogger(message="Filtre sur la matrice")
	 * @Route("/filtrer_la_restitution", name="filtrer_la_restitution")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$entity = new Risque();
		$form = $this->createForm($this->get('orange_main.core')->getCriteria('Restitution'), $entity);
		$this->modifyRequestForForm($request, array(), $form);
		$this->get('session')->set('restitution_criteria', $request->request->get($form->getName()));
		return $this->redirect($this->generateUrl('la_restitution', array('id' => $entity->getCartographie()->getId())));
	}
	
	/**
	 * @QMLogger(message="Affichage matrice par carto")
	 * @Route("/{carto}/{type}/la_restitution", name="la_restitution")
	 * @Template()
	 */
	public function matriceAction(Request $request, $carto, $type) {
		$em = $this->getDoctrine()->getManager();
		$probabiteKPIs = $graviteKPIs = false;
		$entity = new Risque();
		$form = $this->createForm(RisqueCriteria::class, new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', array());
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse($this->generateUrl('la_restitution', array('carto'=>$carto, 'type'=>$type)));
		} 	elseif($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$this->get('session')->set('risque_criteria', array('cartographie' => $carto));
		}
		//$this->denyAccessUnlessGranted('matrice', $entity, 'Accés non autorisée!');
		$data = $this->get('session')->get('risque_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		$em->getRepository(Risque::class)->getMatrice($form->getData(), $type, $probabiteKPIs, $graviteKPIs);
		$entities = $this->get('orange_main.core')->getMapping('Risque')->mapForMatrice($probabiteKPIs, $graviteKPIs, $type, $form->getData());
		return array('entities' => $entities, 'form' => $form->createView(), 'carto'=>$carto, 'type' => $type);
	}
	
	/**
	 * @Route("/save_canvas", name="save_canvas")
	 * @Template()
	 */
	public function saveCanvasAction(Request $request) {
// 		$data = $this->getDoctrine()->getManager()->createQuery($this->get('session')->get('restitution_query'))
// 			->setParameters($this->get('session')->get('restitution_parameters'))
// 			->getArrayResult();
		$data = array();
		$url = $this->get('orange_main.core')->getReporting('Restitution')->extractMatriceSimple($request->request->get('image'), $data, $this->get('kernel')->getRootDir());
		//$reporting->getResponseAfterSave('php://output', 'Matrice de restitution');
		return new JsonResponse(array('url' => $url));
	}
}
