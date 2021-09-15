<?php
namespace App\Controller;

use App\Annotation\QMLogger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use App\Form\UtilisateurFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\QueryBuilder;
use App\Repository\UtilisateurRepository;


/**
 * Ce controller (UtilisateurController) est de celui qui gere l'entité Utilisateur
 * Il regroupe notemment :
 * 		- la methode qui liste l'ensemble des enregistrements (liste_utilisateurs)
 * 		- la methode qui genere la suppression d'un enregistrement (supprimer_utilisateur)
 */
class UtilisateurController extends BaseController {
	
	/**
	 * @QMLogger(message="Liste des utilisateurs")
	 * @Route("/les_utilisateurs", name="les_utilisateurs")
	 * @Template()
	 */
	public function indexAction() {
		$entity = $this->getUser();
		$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
		if(!$this->get('session')->get('structure_criteria')) {
			$this->get('session')->set('structure_criteria', array());
		}
		return array();
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des utilisateurs")
	 * @Route("/liste_des_utilisateurs", name="liste_des_utilisateurs")
	 * @Template()
	 */
	public function listAction(Request $request, UtilisateurRepository $userRepo) {
		$utilisateur = new Utilisateur();
		$form = $this->createForm(UtilisateurFormType::class, $utilisateur);
		$this->modifyRequestForForm($request, $this->get('session')->get('utilisateur_criteria'), $form);
		$user = $form->getData();
		$queryBuilder = $userRepo->listAll($user);
		//dd($queryBuilder);
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Modification utilisateur")
	 * @Route ("/{id}/edition_utilisateur", name="edition_utilisateur", requirements={ "id"= "\d+"})
	 * @Template("bundles/FOSUserBundle/Registration/edit.html.twig")
	 */
	public function editAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('App\Entity\Utilisateur')->find($id);
		$form = $this->createForm(UtilisateurFormType::class, $user);
		$this->denyAccessUnlessGranted('update', $this->getUser(),'Accés non autorisé!');
		$form->setData($user);
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				$em->persist($user);
				$em->flush();
 				$this->get('session')->getFlashBag()->add('success', "La mise à jour de l'utilisateur s'est effectuée avec succès.");
				return $this->redirect($this->generateUrl('les_utilisateurs'));
			}
		}
		return array('user' => $user, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Creation d'un utilisateur")
	 * @Route("/{id}/ajout_utilisateur", name="ajout_utilisateur", requirements={ "id"=  "\d+"})
	 * @Route("/nouveau_utilisateur", name="nouveau_utilisateur")
	 * @Template()
	 */
	public function newAction($id = null) {
		$entity = new Utilisateur();
		if($id) {
			$user = $this->getDoctrine()->getManager()->getRepository(Utilisateur::class)->find($id);
			$entity->setParent($user);
		}
		$form   = $this->createForm(UtilisateurFormType::class, $entity);
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}
	

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un user")
	 * @Route("/{id}/ajouter_utilisateur", name="ajouter_utilisateur")
	 * @Route("/creer_utilisateur", name="creer_utilisateur")
	 * @Template("utilisateur/new.html.twig")
	 */
	public function createAction(Request $request, $id = null) {
		$entity = new Utilisateur();
		$form   = $this->createCreateForm($entity, UtilisateurFormType::class);
		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($entity);
				$em->flush();$this->get('session')->getFlashBag()->add('success', "Utilisateur ajouté avec succés.");
				if($entity->getParent()) {
					return $this->redirect($this->generateUrl('details_utilisateur', array('id' => $entity->getParent()->getId())));
				} else{
					return $this->redirect($this->generateUrl('les_utilisateurs'));
				}
			}	
		}
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}
	
	/**
	 * @QMLogger(message="Détails d'un utilisateur")
	 * @Route("/{id}/details_utilisateur", name="details_utilisateur", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$utilisateur = $em->getRepository('App\Entity\Utilisateur')->find($id);
		$this->denyAccessUnlessGranted('read', $this->getUser(),'Accés non autorisé!');
		return array('entity' => $utilisateur);
	}
	 
	/**
	 * @QMLogger(message="Changement de société")
	 * @Route("/{id}/changement_societe", name="changement_societe", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function changeSocieteAction($id) {
		$em = $this->getDoctrine()->getManager();
		$this->get('session')->set('entite_id', $id);
		$entite = $em->getRepository('App\Entity\Societe')->find($id);
		$utilisateur = $this->getUser();
		$utilisateur->setSociete($entite);
		$em->persist($utilisateur);
		$em->flush();
		return new JsonResponse();
	}
	 
	/**
	 * @QMLogger(message="Activer un utilisateur")
	 * @Route("/{id}/activer_utilisateur", name="activer_utilisateur", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function activateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$utilisateur = $em->getRepository('App\Entity\Utilisateur')->find($id);
		$this->denyAccessUnlessGranted('activate', $this->getUser(),'Accés non autorisé!');
		$utilisateur->setEnabled(true);
		$em->persist($utilisateur);
		$em->flush();
		$this->get('session')->getFlashBag()->add('success', "L'utilisateur a été bien activé");
		return $this->redirect($this->generateUrl('les_utilisateurs'));
	}
	 
	/**
	 * @QMLogger(message="Désactiver utilisateur")
	 * @Route("/{id}/desactiver_utilisateur", name="desactiver_utilisateur", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function desactivateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$utilisateur = $em->getRepository('App\Entity\Utilisateur')->find($id);
		$this->denyAccessUnlessGranted('desactivate', $this->getUser(),'Accés non autorisé!');
		$utilisateur->setEnabled(false);
		$em->persist($utilisateur);
		$em->flush();
		$this->get('session')->getFlashBag()->add('success', "L'utilisateur a été bien désactivé");
		return $this->redirect($this->generateUrl('les_utilisateurs'));
	}
	  
	/**
	 * @todo ajoute un filtre
	 * @param Request $request
	 */
	protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
		parent::setFilter($queryBuilder, array('q.nom', 'q.prenom'), $request);
		if($request->query->has('sSearch') && $request->query->get('sSearch')!="") {
			$search = '%'.$request->query->get('sSearch').'%';
			$queryBuilder->orWhere("CONCAT(q.prenom,' ',q.nom) LIKE :search")->setParameter('search', $search);
			$queryBuilder->orWhere("CONCAT(q.nom,' ',q.prenom) LIKE :search")->setParameter('search', $search);
		}
	}

	/**
	 * @param \App\Entity\Utilisateur $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			sprintf('<a href="%s">%s<a/>', /*$this->generateUrl('details_utilisateur', array('id' => $entity->getId()))*/'#', $entity->__toString()),
	  			$entity->getMatricule(),
	  			$entity->getStructure() ? $entity->getStructure()->getName() : null,
	  			$entity->getProfil(),
	  			$this->service_status->generateStatusForUtilisateur($entity),
	  			$this->service_action->generateActionsForUtilisateur($entity)
	  	);
	}
}
