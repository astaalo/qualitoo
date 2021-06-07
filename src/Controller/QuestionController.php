<?php
/*
 * edited by @mariteuw
 */
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Question;
use App\Annotation\QMLogger;

class QuestionController extends BaseController{
	
	/**
	 * @QMLogger(message="Affichage des questions")
	 * @Route("/les_questions", name="les_questions")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$entity= new Question();
		$entities = $em->getRepository('OrangeMainBundle:Question')->listAll();
		$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
		return array('entities' => $entities);
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des questions")
	 * @Route("/liste_des_questions", name="liste_des_questions")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('OrangeMainBundle:Question')->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Ajout question")
	 * @Route("/nouvelle_question", name="nouvelle_question")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Question();
		$form = $this->createCreateForm( $entity, 'Question');
		
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');
		
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors d'un ajout de question")
	 * @Route("/creer_question", name="creer_question")
	 * @Template()
	 */
	public function createAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('OrangeMainBundle:Question')->listAll();
		$count= count($entities);
		$entity = new Question();
		$form   = $this->createCreateForm($entity, 'Question');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity->setPosition($count+1);
			$em->persist($entity);
			$em->flush();
			
			return $this->redirect($this->generateUrl('les_questions'));
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="DEatils d'une question")
	 * @Route("/{id}/details_question", name="details_question", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$question = $em->getRepository('OrangeMainBundle:Question')->find($id);
		
		$this->denyAccessUnlessGranted('read', $processus, 'Accés non autorisé');
		
		return array('entity' => $question);
	}
	
	/**
	 * @QMLogger(message="Modification d'une question")
	 * @Route ("/{id}/edition_question", name="edition_question", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Question')->find($id);
		$form = $this->createCreateForm($entity, 'Question');
		
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
		
		return array('entity' => $entity, 'form' => $form->createView(),'id'=>$id);
	}
	
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'une question")
	 * @Route ("/{id}/modifier_question", name="modifier_question", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Question:edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Question')->find($id);
		$form = $this->createCreateForm($entity, 'Question');
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('les_questions'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Suppression d'une question")
	 * @Route("/{id}/supprimer_question", name="supprimer_question", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function deleteAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Question')->find($id);
		if(!$entity) {
			throw $this->createNotFoundException('Aucune question trouvée pour cet id : '.$id);
		}
		if($this->getRequest()->getMethod()=='POST') {
			$em->remove($entity);
			$em->flush();
		}
		else {
			return array('entity' => $entity);
		}
	}
	
	/**
	 * @QMLogger(message="Changer ordre des questions")
	 * @Route("/{sens}/changer_position", name="changer_position")
	 * @Template()
	 */
	public function changePositionAction($sens) {
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('OrangeMainBundle:Question')->listAll();
		$entity= new Question();
		
		$this->denyAccessUnlessGranted('changePosition', $entity,'Accés non autorisée');
		
		$count= count($entities);
		if($sens == 'B'){
			foreach ($entities as $key=>$value){
					if($value->getPosition()==$count)
						$value->setPosition(1);
					else{
						$value->setPosition($value->getPosition()+1);
					}
			 $em->persist($value);
			}
		}else{
			foreach ($entities as $key=>$value){
				if($value->getPosition()==1)
						$value->setPosition($count);
				else{
						$value->setPosition($value->getPosition()-1);
				}
			$em->persist($value);
			}
		}
		$em->flush();
		return $this->redirect($this->generateUrl('les_questions'));
	}
	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\Question $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->getLibelle(),
	  			$entity->getCotation(),
	  			$entity->getPosition(),
	  			$this->get('orange_main.status')->generateStatusForQuestion($entity),
	  			$this->get('orange.main.actions')->generateActionsForQuestion($entity)
	  	);
	}
	
}