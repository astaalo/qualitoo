<?php
/*
 * edited by @mariteuw
 */
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Projet;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Processus;
use App\Criteria\ProjetCriteria;
use Symfony\Component\HttpFoundation\Response;
use App\Annotation\QMLogger;
use Doctrine\ORM\QueryBuilder;
use App\Form\ProjetType;

class ProjetController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des projets")
	 * @Route("/les_projets", name="les_projets")
	 * @Template()
	 */
	public function indexAction() {
		$this->get('session')->set('projet_criteria', array());
		return array();
	}
	
	
	/**
	 * @Route("/suivi_projet", name="suivi_projet")
	 * @Template()
	 */
	public function listeAction() {
		$this->get('session')->set('projet_criteria', array());
		return array();
	}
		
	/**
	 * @QMLogger(message="Filtre sur les projets")
	 * @Route("/filtrer_les_projets", name="filtrer_les_projets")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(ProjetCriteria::class, new Projet());
		if($request->getMethod()=='POST') {
			$this->get('session')->set('projet_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('projet_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des projets")
	 * @Route("/liste_des_projets", name="liste_des_projets")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(ProjetCriteria::class, new Projet());
		$this->modifyRequestForForm($request, $this->get('session')->get('projet_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository('App\Entity\Projet')->listAllQueryBuilder($criteria);
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Changement de statut d'un projet")
	 * @Route("/{id}/{statut}/changer_statut", name="changer_statut")
	 * @Template()
	 */
	public function changeStatutAction(Request $request,$id,$statut){
		$em = $this->getDoctrine()->getManager();
		$entity =$em->getRepository('App\Entity\Projet')->find($id);
		if($request->getMethod()=='POST'){
		    $entity->setEtat($statut);
		    $em->persist($entity);
		    $em->flush();
		    return $this->redirect($this->generateUrl('suivi_projet'));
		}
		return array('id'=> $id, 'statut'=>$statut);
	}
	
	/**
	 * @Route("/suivi_liste_des_projets", name="suivi_liste_des_projets")
	 * @Template()
	 */
	public function suivilistAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(ProjetCriteria::class, new Projet());
		$this->modifyRequestForForm($request, $this->get('session')->get('projet_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository('App\Entity\Projet')->listAllQueryBuilder($criteria);
		return $this->paginate($request, $queryBuilder, 'addRowInTableSuivi');
	}
	
	/**
	 * @QMLogger(message="Creation d'un projet")
	 * @Route ("/nouveau_projet", name="nouveau_projet")
	 * @Route ("/{id}/ajout_projet", name="ajout_projet")
	 * @Template()
	 */
	public function newAction($id = null) {
		$entity = new Projet();
		$processus = $id ? $this->getDoctrine()->getManager()->getRepository('App\Entity\Processus')->find($id) : null;
		$entity->setProcessus($processus);
		$form = $this->CreateForm(ProjetType::class, $entity);
		return array('entity' => $entity, 'form' => $form->createView(), 'processus_id' => $id);
	}
	
	/**
	* @QMLogger(message="Envoi des donnees saisies lors de la crearion d'un projet")
	* @Route("/creer_projet", name="creer_projet")
	* @Route("/{id}/ajouter_projet", name="ajouter_projet")
	* @Method("POST")
	* @Template("projet/new.html.twig")
	*/
	public function createAction(Request $request, $id = null) {
		$em = $this->getDoctrine()->getManager();
		$entity = new Projet();
		$processus = $id ? $em->getRepository('App\Entity\Processus')->find($id) : null;
		$entity->setProcessus($processus);
		$form   = $this->createCreateForm($entity, 'Projet');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$entity->setSociete($this->getUser()->getSociete());
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('details_processus', array('id' => $entity->getProcessus()->getId())));
		}
		return array('entity' => $entity, 'form' => $form->createView(), 'processus_id' => $id);
	}
	
	/**
	 * @QMLogger(message="Affichage d'un projet")
	 * @Route("/{id}/details_projet", name="details_projet", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$projet = $em->getRepository('App\Entity\Projet')->find($id);
		return array('entity' => $projet);
	}
	
	/**
	 * @QMLogger(message="Modification d'un projet")
	 * @Route ("/{id}/edition_projet", name="edition_projet", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Projet')->find($id);
		$form = $this->createCreateForm($entity, 'Projet');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi de donnees saisies lors de la modification  des projets")
	 * @Route ("/{id}/modifier_projet", name="modifier_projet", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("projet/edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Projet')->find($id);
		$form = $this->createCreateForm($entity, 'Projet');
		$request = $request;
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$this->getDoctrine()->getRepository('App\Entity\RisqueProjet')->createQueryBuilder('rp')
					->update()
					->set('rp.processus', $entity->getProcessus()->getId())
					->where('IDENTITY(rp.projet) = :projet')->setParameter('projet', $entity->getId())
					->getQuery()->execute();
				$em->flush();
				return $this->redirect($this->generateUrl('les_projets'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @todo retourne la liste des projets pour un processus donné
	 * @Route("/projet_by_processus", name="projet_by_processus")
	 */
	public function listByProcessusAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$projets = $em->getRepository('App\Entity\Projet')->findByProcessusId($request->request->get('id'));
		$output = array(array('id' => "", 'libelle' => 'Choisir un projet ...'));
		foreach ($projets as $projet) {
			$output[] = array('id' => $projet['id'], 'libelle' => $projet['libelle']);
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}

	/**
	 * @todo retourne la liste des projets pour une structure donné
	 * @Route("/projet_by_structure", name="projet_by_structure")
	 */
	public function listByStructureAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$projets = $em->getRepository('App\Entity\Projet')->findByStructureId($request->request->get('id'));
		$output = array(array('id' => "", 'libelle' => 'Choisir un projet ...'));
		foreach ($projets as $projet) {
			$output[] = array('id' => $projet['id'], 'libelle' => $projet['libelle']);
		}
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		return $response->setContent(json_encode($output));
	}

	/**
	 * @QMLogger(message="Suppression d'un projet")
	 * @Route("/{id}/suppression_projet", name="suppression_projet")
	 * @Template()
	 */
	public function deleteAction(Request $request, $id){
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Projet')->find($id);
		if($entity == null)
			$this->createNotFoundException("Ce projet n'existe pas!");
		$this->denyAccessUnlessGranted('delete', $entity, 'Accés non autorisé!');
		if($request->getMethod()=='POST') {
            $charg = $em->getRepository('App\Entity\Chargement')->findByProjet($id);
            $risque = $em->getRepository('App\Entity\RisqueProjet')->findByProjet($id);
            if (count($charg) <= 0 && count($risque) <= 0) {
                $em->remove($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', "Le projet a été supprimé avec succès.");
            } else {
                $this->get('session')->getFlashBag()->add('error', "Veuillez supprimer d'abord les chargements et risques liés à ce projet.");
            }
			return $this->redirect($this->generateUrl('les_projets'));
		}
		return new Response($this->renderView('projet/delete.html.twig', array('entity' => $entity)));
	}
	
	/**
	 * @QMLogger(message="Extractions des projets")
	 * @Route("/exporter_les_projets", name="exporter_les_projets")
	 * @Template()
	 */
	public function exportAction(Request $request) {
		$form = $this->createForm(ProjetCriteria::class, new Projet());
		if($request->getMethod()=='POST') {
			$this->get('session')->set('projet_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('projet_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @param Request $request
	 */
	protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
		parent::setFilter($queryBuilder, array('p.libelle'), $request);
	}
	
	/**
	 * @param \App\Entity\Projet $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->getCode(),
	  			$entity->getLibelle(),
	  			$entity->getUtilisateur()->__toString(),
	  			$this->service_status->generateStatusForProjet($entity),
	  			$this->service_action->generateActionsForProjet($entity)
	  		);
	}
	
	/**
	 * @param \App\Entity\Projet $entity
	 * @return array
	 */
	protected function addRowInTableSuivi($entity) {
		return array(
				$entity->getCode(),
				$entity->getLibelle(),
				$entity->getUtilisateur()->__toString(),
				$this->service_status->generateStatusForProjet($entity),
				$this->service_action->generateActionsForSuiviProjet($entity)
		);
	}
}
