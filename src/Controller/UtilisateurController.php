<?php
namespace App\Controller;

use App\Annotation\QMLogger;
use App\Repository\UtilisateurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use App\Form\UtilisateurFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\QueryBuilder;


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
		//$this->denyAccessUnlessGranted('read', $entity,'Accés non autorisé!');
		return array ();
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des utilisateurs")
	 * @Route("/liste_des_utilisateurs", name="liste_des_utilisateurs")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine ()->getManager ();
		$queryBuilder = $em->getRepository (Utilisateur::class)->listAllQueryBuilder ( $this->getUser () );
		dd($queryBuilder);
		return $this->paginate ( $request, $queryBuilder );
	}


    /**
     * @Route("/BO/datatable_listent", name="list")
     * @QMLogger(message="Liste du datatable entreprise")
     */
    public function list(UtilisateurRepository $userRepo, Request $request)
    {
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length'); // Rows display per page
        $columnIndex = $request->get('order')[0]['column']; // Column index
        $columnName = $request->get('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->get('order')[0]['dir']; // asc or desc
        $searchValue = $request->get("search")['value']; // Search value
        // FILTRE
        $filtreLocalite = (!empty($_GET['filter_localite'])) ? $_GET['filter_localite'] : null;
        $filtreSecteur =  (!empty($_GET['filter_secteur'])) ? $_GET['filter_secteur'] : null;
        // SAVE DT FIELD IN SESSION
        $this->saveVariableDatatable($draw, $start, $rowperpage,$columnIndex, $columnName, $columnSortOrder, $searchValue, $filtreLocalite, $filtreSecteur);

        $dt = $userRepo->entrepriseDatatable($searchValue, $columnName, $columnSortOrder, $start, $rowperpage, $filtreLocalite, $filtreSecteur);// REPOSITORY
        $totalRecordwithFilter = $userRepo->countFilteredEnt($searchValue, $filtreLocalite, $filtreSecteur);// TOTAL FILTRE
        $totalRecords = $userRepo->countAllEnt();// TOTAL DONNEES
        $data = array();
        foreach ($dt as $row) {
            $data[] = $row;
        }
        ## Response
        $response = array(
            // "searc" => json_decode($request->getContent(), true),
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );
        return $this->json($response,200,[],['groups'=> 'e:read']);
    }
	
	/**
	 * @QMLogger(message="Modification utilisateur")
	 * @Route ("/{id}/edition_utilisateur", name="edition_utilisateur", requirements={ "id"= "\d+"})
	 * @Template("FOSUserBundle:Registration:edit.html.twig")
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);
		$form = $this->createForm(new UtilisateurFormType(), $user);
		$this->denyAccessUnlessGranted('update', $user,'Accés non autorisé!');
		$form->setData($user);
		if ($this->get('request')->getMethod() == 'POST') {
			$form->bind($this->get('request'));
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
	 * @QMLogger(message="Détails d'un utilisateur")
	 * @Route("/{id}/details_utilisateur", name="details_utilisateur", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$utilisateur = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);
		$this->denyAccessUnlessGranted('read', $utilisateur,'Accés non autorisé!');
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
		$entite = $em->getRepository('OrangeMainBundle:Societe')->find($id);
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
		$utilisateur = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);
		$this->denyAccessUnlessGranted('activate', $utilisateur,'Accés non autorisé!');
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
		$utilisateur = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);
		$this->denyAccessUnlessGranted('desactivate', $utilisateur,'Accés non autorisé!');
		$utilisateur->setEnabled(false);
		$em->persist($utilisateur);
		$em->flush();
		$this->get('session')->getFlashBag()->add('notice', "L'utilisateur a été bien désactivé");
		return $this->redirect($this->generateUrl('les_utilisateurs'));
	}
	  
	/**
	 * @todo ajoute un filtre
	 * @param sfWebRequest $request
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
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\Utilisateur $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			sprintf('<a href="%s">%s<a/>', /*$this->generateUrl('details_utilisateur', array('id' => $entity->getId()))*/'#', $entity->__toString()),
	  			$entity->getMatricule(),
	  			$entity->getStructure() ? $entity->getStructure()->getName() : null,
	  			$entity->getProfil(),
	  			$this->get('orange_main.status')->generateStatusForUtilisateur($entity),
	  			$this->get('orange.main.actions')->generateActionsForUtilisateur($entity)
	  	);
	}
}

