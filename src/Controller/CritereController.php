<?php
namespace App\Controller;

use App\Entity\TypeGrille;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Critere;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Cartographie;
use App\Annotation\QMLogger;

class CritereController extends BaseController {
	
	/**
	 * @QMLogger(message="Affichage des criteres ")
	 * @Route("/les_criteres", name="les_criteres")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$entity = new Critere();
		$entities = $em->getRepository(TypeGrille::class)->findByTypeEvaluationId($this->getMyParameter('ids', array('type_evaluation', 'impact')));
		
		//$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisée!');
		
		return array('entities' => $entities);
	}
	
	/**
	 * @QMLogger(message="Creation d'un nouveau critere ")
	 * @Route("/{id}/nouveau_critere", name="nouveau_critere")
	 * @Template()
	 */
	public function newAction($id) {
		$em = $this->getDoctrine()->getManager();
		$cartographie = $em->getRepository('OrangeMainBundle:Cartographie')->find($id);
		$entity = new Critere();
		
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisée!');
		
		$form   = $this->createCreateForm($entity->init($cartographie, 4), 'Critere', array('attr' => array('em' => $em)));
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un critere")
	 * @Route("/creer_critere", name="creer_critere")
	 * @Template("OrangeMainBundle:Critere:new.html.twig")
	 */
	public function createAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$entity = new Critere();
		$form   = $this->createCreateForm($entity, 'Critere', array('attr' => array('em' => $em)));
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('les_criteres'));
		}
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $entity->getCartographie()->getId());
	}
	
	/**
	 * @QMLogger(message="Affichage d'un critere")
	 * @Route("/{id}/details_critere", name="details_critere", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Critere')->find($id);
		
		$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisée!');
		
		return array('entity' => $entity);
	}
	
	/**
	 * @QMLogger(message="Modification d'un critere ")
	 * @Route ("/{id}/edition_critere", name="edition_critere", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Critere')->find($id);
		$form = $this->createCreateForm($entity, 'Critere', array('attr' => array('em' => $em)));
		
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisée!');
		
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'un critere")
	 * @Route ("/{id}/modifier_critere", name="modifier_critere")
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Critere:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Critere')->find($id);
		$form = $this->createCreateForm($entity, 'Critere', array('attr' => array('em' => $em)));
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_criteres'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Chargement ajax criteres par domaine ")
	 * @Route("/critere_by_domaines", name="critere_by_domaines")
	 */
	public function listByDomainesAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$ids = json_decode($request->request->get('ids'));
		$criteres = $em->getRepository('OrangeMainBundle:Critere')->findByDomaines($ids);
		$output = array(array('id' => "", 'libelle' => 'Choisir une critère ...'));
		foreach ($criteres as $critere) {
			$output[] = array('id' => $critere['id'], 'libelle' => $critere['libelle']);
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}
	
	/**
	 * @QMLogger(message="Suppression d'un critere ")
	 * @Route("/{id}/suppression_critere", name="suppression_critere", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function deleteAction(Request $request,$id) {
		$em = $this->getDoctrine()->getManager();
		
		/** @var Critere $entity */
		$entity = $em->getRepository('OrangeMainBundle:Critere')->find($id);
		
		if(!$entity){
			$this->get('session')->getFlashBag()->add('error', "Le critère n'existe pas");
			return $this->redirect($this->generateUrl('les_criteres'));
		}else{
			if($request->getMethod()=='POST')
			{
				if($entity->getImpact()->count()==0){
					$em->remove($entity);
					$em->flush();
					$this->get('session')->getFlashBag()->add('success', "Le critere a été supprimé avec succés.");
				}elseif ($entity->getImpact()->count()>0)
					$this->get('session')->getFlashBag()->add('error', "Le critère est déja utilisé pour une évaluation");
				
				return new Response($this->redirect($this->generateUrl('les_criteres')));
			}
		}
		return array('entity' => $entity);
	}
	
}
