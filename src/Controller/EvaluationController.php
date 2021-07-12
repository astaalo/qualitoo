<?php
namespace App\Controller;

use App\Form\EvaluationType;
use App\MainBundle\OrangeMainBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Evaluation;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Criteria\EvaluationCriteria;
use App\Entity\Risque;
use App\Annotation\QMLogger;

class EvaluationController extends BaseController{
	
	/**
	 * @QMLogger(message="Affichage des evaluations")
	 * @Route("/les_evaluations", name="les_evaluations")
	 * @Template()
	 */
	public function indexAction() {
		$this->get('session')->set('evaluation_criteria', array());
		return array();
	}
	
	/**
	 * @QMLogger(message="Filtre sur les evaluations")
	 * @Route("/filtrer_les_evaluations", name="filtrer_les_evaluations")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(EvaluationCriteria::class);
		if($request->getMethod()=='POST') {
			$this->get('session')->set('evaluation_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('evaluation_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des evaluations")
	 * @Route("/liste_des_evaluations", name="liste_des_evaluations")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(EvaluationCriteria::class, new Evaluation());
		$this->modifyRequestForForm($request, $this->get('session')->get('evaluation_criteria'), $form);
		$queryBuilder = $em->getRepository(Evaluation::class)->listQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Creation evaluation")
	 * @Route("/{id}/nouvelle_evaluation", name="nouvelle_evaluation")
	 * @Template()
	 */
	public function newAction($id) {
	    //dd($this->getMyParameter('ids', ['type_evaluation'])['cause']);
		$em = $this->getDoctrine()->getManager();
		$risque = $em->getRepository('App\Entity\Risque')->find($id);
		$entity = new Evaluation();
		$form = $this->createCreateForm($entity->newEvaluation($risque), EvaluationType::class, array('attr' => array('em' => $em)));
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'une evaluation")
	 * @Route("/{id}/creer_evaluation", name="creer_evaluation")
	 * @Method("POST")
	 * @Template("evaluation/new.html.twig")
	 */
	public function createAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$risque = $em->getRepository('App\Entity\Risque')->find($id);
		$entity = new Evaluation();
		$entity->setRisque($risque);
		$entity->setEvaluateur($this->getUser());
		$form   = $this->createCreateForm($entity->newEvaluation($risque), EvaluationType::class, array('attr' => array('em' => $em)));
		if($request->getMethod()=='POST'){
			$form->handleRequest($request);
			$dispatcher = $this->container->get('event_dispatcher');
			$event = $this->cartoEvent;
			$entity->cleanUselessImpact();
			$entity->computeProbabilite();
			$entity->computeGravite();
			$entity->setCriticite($em->getRepository('App\Entity\Criticite')->findByProbabiliteAndGravite($entity->getProbabilite(), $entity->getGravite()));
			$entity->cloneEvaluation();
			$event->setEvaluation($entity);
			$risque->setTobeMigrate(true);
			$em->persist($entity);
			$dispatcher->dispatch(OrangeMainBundle::EVALUATION_CREATED,$event);
			$em->flush();
			$this->get('session')->getFlashBag()->add('success', "Le risque a été évalué avec succès. L'identification du risque est maintenant complet.");
			return $this->redirect($this->generateUrl('details_risque', array('id' => $entity->getRisque()->getId())));
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Affichage d'une evaluation")
	 * @Route("/{id}/details_evaluation", name="details_evaluation", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$evaluation = $em->getRepository('App\Entity\Evaluation')->find($id);
		return array('entity' => $evaluation);
	}
	
	/**
	 * @QMLogger(message="Modifiation d'une evaluation")
	 * @Route ("/{id}/edition_evaluation", name="edition_evaluation", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Evaluation')->find($id);
		$form = $this->createCreateForm($entity->completeDomaine(), EvaluationType::class, array('attr' => array('em' => $em)));
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'une evaluation")
	 * @Route ("/{id}/modifier_evaluation", name="modifier_evaluation", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("evaluation/edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Evaluation')->find($id);
		$form = $this->createCreateForm($entity, EvaluationType::class, array('attr' => array('em' => $em)));
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				$dispatcher = $this->container->get('event_dispatcher');
				$event = $this->cartoEvent;
				$entity->cleanUselessImpact();
				$entity->computeProbabilite();
				$entity->computeGravite();
				$entity->setCriticite($em->getRepository('App\Entity\Criticite')->findByProbabiliteAndGravite($entity->getProbabilite(), $entity->getGravite()));
				$entity->setDateEvaluation(new \DateTime('NOW'));
				$entity->cloneEvaluation();
				$event->setEvaluation($entity);
				$entity->getRisque()->setTobeMigrate(true);
				$em->persist($entity);
				$dispatcher->dispatch(OrangeMainBundle::EVALUATION_VALIDATED,$event);
				if($entity->getTransfered()==true)
					$entity->getRisque()->setEtat($this->getMyParameter('states', array('risque', 'rejete')));
				$flash = null;
				if($entity->getRisque()->isPending()) {
					$entity->getRisque()->setEtat($this->getMyParameter('states', array('risque', 'valide')));
					$entity->setValidateur($this->getUser());
					$flash = "L'évaluation du risque a été mise à jour avec succès. Le risque est maintenant validé.";
					$route = $this->generateUrl('details_risque', array('id' => $entity->getRisque()->getId()));
				} elseif($entity->getRisque()->isValidated()) {
					$flash = "L'évaluation du risque mise à jour avec succès.";
					$route = $this->generateUrl('les_evaluations');
				} elseif($entity->getRisque()->isRejected()) {
					$flash = "Le risque existe deja, l'agregation a ete fait avec succés.";
					$route = $this->generateUrl('details_risque', array('id' => $entity->getRisque()->getId()));
				} else {
					$flash = "L'évaluation du risque mise à jour avec succès. L'identification du risque est maintenant complet.";
					$route = $this->generateUrl('details_risque', array('id' => $entity->getRisque()->getId()));
				}
				try {
					$em->persist($entity->getRisque());
					$em->flush();
					$this->addFlash('success', $flash);
				} catch(\Doctrine\DBAL\DBALException $e) {
					$this->addFlash('error', "Une erreur inattendue est survenue durant l'évaluation. Merci de réessayer sinon contacter l'administrateur.");
					$route = $this->generateUrl('details_risque', array('id' => $entity->getRisque()->getId()));
				}
				return $this->redirect($route);
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	
	/**
	 * @QMLogger(message="Extraction des evaluations")
	 * @Route("/exporter_les_evaluations", name="exporter_les_evaluations")
	 * @Template()
	 */
	public function exportAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(EvaluationCriteria::class);
		$this->modifyRequestForForm($request, $this->get('session')->get('evaluation_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Evaluation')->listQueryBuilder($form->getData());
		$data = $this->orange_main_core->getMapping('Evaluation')->mapForBasicExport($queryBuilder->getQuery()->getResult());
		$reporting = $this->orange_main_core->getReporting('Risque')->extractByRisque($data);
		$reporting->getResponseAfterSave('php://output', 'Cartographie des risques');
	}
	
	/**
	 * @param \App\Entity\Evaluation $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->getRisque()->getCode(),
	  			$entity->getRisque()->getMenace() ? $entity->getRisque()->getMenace()->__toString() : 'Non renseigné',
	  			$entity->getEvaluateur() ? $entity->getEvaluateur()->__toString() : 'Non renseigné',
	  			$entity->getProbabilite(),
	  			$entity->getGravite(),
	  			$entity->getCriticite() ? $entity->getCriticite()->getNiveau() : 'Non renseigné',
	  			$this->service_action->generateActionsForEvaluation($entity)
	  		);
	}
	
}
