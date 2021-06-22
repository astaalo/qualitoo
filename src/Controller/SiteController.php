<?php
namespace App\Controller;

use App\Form\SiteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Site;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\QueryBuilder;
use App\Annotation\QMLogger;

class SiteController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des sites")
	 * @Route("/les_sites", name="les_sites")
	 * @Template()
	 */
	public function indexAction() {
		$entity= new Site();
		//$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
		return array();
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des sites")
	 * @Route("/liste_des_sites", name="liste_des_sites")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository(Site::class)->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Ajout d'un site")
	 * @Route("/nouveau_site", name="nouveau_site")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Site();
		$form   = $this->createCreateForm($entity, 'Site');
		
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');
		
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Envoi des donnees lors de la creation d'un site")
	 * @Route("/creer_site", name="creer_site")
	 * @Template("site/new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Site();
		$form   = $this->createCreateForm($entity, 'Site');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$entity->setSociete($this->getUser()->getStructure()->getSociete());
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			return new JsonResponse(array('status' => 'success', 'text' => 'Le site a bien été ajouté avec succés'));
		}
		return new Response($this->renderView('site/new.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	
	/**
	 * @QMLogger(message="Détails d'un site")
	 * @Route("/{id}/details_site", name="details_site", requirements={ "id"=  "\d+"})
	 * @Template()
	 *
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$site = $em->getRepository('App\Entity\Site')->find($id);
		
		$this->denyAccessUnlessGranted('read', $site, 'Accés non autorisé');
		
		return array('entity' => $site);
	}
	
	/**
	 * @QMLogger(message="Modification d'un site")
	 * @Route ("/{id}/edition_site", name="edition_site", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Site')->find($id);
		$form = $this->CreateForm(SiteType::class, $entity);
		
		//$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
		
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des données lors d'une modification d'un site")
	 * @Route ("/{id}/modifier_site", name="modifier_site", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("site/edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Site')->find($id);
		$form = $this->createCreateForm($entity, 'Site');
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return new JsonResponse(array('status' => 'success', 'text' => 'Le site a bien été mis à jour'));
			}
		}
		return new Response($this->renderView('site/edit.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}

    /**
     * @Route("/{id}/activer_desactiver_site", name="activer_desactiver_site", requirements={ "id"=  "\d+"})
     * @Template()
     */
    public function activeDesactiveAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('App\Entity\Site')->find($id);

        //$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
        return ['entity' => $entity];
    }

    /**
     * @QMLogger(message="Activer site")
     * @Route("/{id}/activer_site", name="activer_site", requirements={ "id"=  "\d+"})
     * @Template()
     */
    public function activateAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('App\Entity\Site')->find($id);

        //$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');

        $entity->setEtat(true);
        $em->persist($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "Le site a été bien activé avec succés");
        return $this->redirect($this->generateUrl('les_sites'));
    }
	 
	/**
	 * @QMLogger(message="Désactiver site")
	 * @Route("/{id}/desactiver_site", name="desactiver_site", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function desactivateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Site')->find($id);
		
		//$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
		
		$entity->setEtat(false);
		$em->persist($entity);
		$em->flush();
		$this->get('session')->getFlashBag()->add('notice', "Le site a été bien désactivé avec succés");
		return $this->redirect($this->generateUrl('les_sites'));
	}
	  
	/**
	 * @todo ajoute un filtre
	 * @param Request $request
	 */
	protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
		parent::setFilter($queryBuilder, array('s.libelle'), $request);
	}
	
	/**
	 * @param \App\Entity\Site $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->getLibelle(),
	  			$this->service_status->generateStatusForEntity($entity),
	  			$this->service_action->generateActionsForSite($entity)
	  		);
	}
}

