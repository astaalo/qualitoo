<?php
namespace  App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\DomaineImpact;
use App\Entity\DomaineActivite;
use App\Entity\DomaineSite;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Cartographie;
use App\Annotation\QMLogger;

class DomaineController extends BaseController {
	
	/**
	 * @QMLogger(message="Affichage des domaines ")
	 * @Route("/les_domaines", name="les_domaines")
	 * @Template()
	 */
	public function indexAction() {
		$entity = new DomaineActivite();
		$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisée!');
		return array();
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des domaines d'impact")
	 * @Route("/liste_des_domaines_dimpact", name="liste_des_domaines_dimpact")
	 * @Template()
	 */
	public function listForImpactAction(Request $request){
		$em = $this->getDoctrine()->getManager();
    	$queryBuilder = $em->getRepository('OrangeMainBundle:DomaineImpact')->listAllQueryBuilder();
    	return $this->paginate($request, $queryBuilder, 'addRowInTableForImpact');
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des domaines d'activites")
	 * @Route("/liste_des_domaines_dactivite", name="liste_des_domaines_dactivite")
	 * @Template()
	 */
	public function listForActiviteAction(Request $request){
		$em = $this->getDoctrine()->getManager();
    	$queryBuilder = $em->getRepository('OrangeMainBundle:DomaineActivite')->listAllQueryBuilder();
    	return $this->paginate($request, $queryBuilder, 'addRowInTableForActivite');
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des domaines de site")
	 * @Route("/liste_des_domaines_site", name="liste_des_domaines_site")
	 * @Template()
	 */
	public function listForSiteAction(Request $request){
		$em = $this->getDoctrine()->getManager();
    	$queryBuilder = $em->getRepository('OrangeMainBundle:DomaineSite')->listAllQueryBuilder();
    	return $this->paginate($request, $queryBuilder, 'addRowInTableForSite');
	}
	
	/**
	 * @QMLogger(message="Creation d'un domaine d'impact")
	* @Route("/{id}/nouveau_domaine_impact", name="nouveau_domaine_impact")
	* @Template()
	*/
	public function newForImpactAction($id) {
		$em = $this->getDoctrine()->getManager();
		$cartographie = $em->getRepository('OrangeMainBundle:Cartographie')->find($id);
		$entity = new DomaineImpact();
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisée!');
		$form = $this->createCreateForm($entity->setCartographie($cartographie),'DomaineImpact');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Creation d'un domaine d'activite")
	* @Route("/nouveau_domaine_activite", name="nouveau_domaine_activite")
	* @Template()
	*/
	public function newForActiviteAction(){
		$entity = new DomaineActivite();
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisée!');
		$form = $this->createCreateForm($entity,'DomaineActivite');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Creation d'un domaine de site")
	* @Route("/nouveau_domaine_site", name="nouveau_domaine_site")
	* @Template()
	*/
	public function newForSiteAction() {
		$entity = new DomaineSite();
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisée!');
		$form = $this->createCreateForm($entity,'DomaineSite');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un domaine d'impact")
	 * @Route("/creer_domaine_impact", name="creer_domaine_impact")
	 * @Template("OrangeMainBundle:Domaine:newForImpact.html.twig")
	 */
	public function createForImpactAction(Request $request){
		$entity = new DomaineImpact();
		$form = $this->createCreateForm($entity,'DomaineImpact');
		$form->handleRequest($request);
		if($form->isValid()){
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			$this->addFlash('success', 'Le domaine a été créé avec succès');
			return new JsonResponse(array('status' => 'success', 'text' => 'Le domaine a été créé avec succès'));
		}
		return new Response($this->renderView('OrangeMainBundle:Domaine:newForImpact.html.twig', array('entity' => $entity, 'form' =>$form->createView())), 303);
	}

	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un domaine d'activite")
	 * @Route("/creer_domaine_activite", name="creer_domaine_activite")
	 * @Template("OrangeMainBundle:Domaine:newForActivite.html.twig")
	 */
	public function createForActiviteAction(Request $request) {
		$entity = new DomaineActivite();
		$form   = $this->createCreateForm($entity, 'DomaineActivite');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			return new JsonResponse(array('status' => 'success', 'text' => 'Le domaine a été créé avec succès'));
		}
		return new Response($this->renderView('OrangeMainBundle:Domaine:newForActivite.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un domaine de site")
	 * @Route("/creer_domaine_site", name="creer_domaine_site")
	 * @Template("OrangeMainBundle:Domaine:newForSite.html.twig")
	 */
	public function createForSiteAction(Request $request) {
		$entity = new DomaineSite();
		$form   = $this->createCreateForm($entity, 'DomaineSite');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			return new JsonResponse(array('status' => 'success', 'text' => 'Le domaine a été créé avec succès'));
		}
		return new Response($this->renderView('OrangeMainBundle:Domaine:newForSite.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	
	/**
	 * @QMLogger(message="Details d'un domaine ")
	 * @Route("/{id}/details_domaine", name="details_domaine", requirements={"id"= "\d+"})
	 * @Template()
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$domaine = $em->getRepository('OrangeMainBundle:Domaine')->find($id);
		
		$this->denyAccessUnlessGranted('read', $domaine, 'Accés non autorisée!');
		
		return array('entity' => $domaine);
	}
	
	/**
	 * @QMLogger(message="Modification d'un domaine d'impact")
	 * @Route("/{id}/edition_domaine_dimpact", name="edition_domaine_dimpact", requirements={"id"= "\d+"})
	 * @Template()
	 */
	public function editForImpactAction($id){
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:DomaineImpact')->find($id);
		$form = $this->createCreateform($entity, 'DomaineImpact');
		
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisée!');
		
		return array('entity' => $entity, 'form' =>$form->createView());
	}
	
	/**
	 * @QMLogger(message="Modification d'un domaine d'activtie")
	 * @Route("/{id}/edition_domaine_dactivite", name="edition_domaine_dactivite", requirements={"id"= "\d+"})
	 * @Template()
	 */
	public function editForActiviteAction($id){
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:DomaineActivite')->find($id);
		$form = $this->createCreateform($entity, 'DomaineActivite');

		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisée!');
		
		return array('entity' => $entity, 'form' =>$form->createView());
	}
	
	/**
	 * @QMLogger(message="Modification d'un domaine de site")
	 * @Route("/{id}/edition_domaine_site", name="edition_domaine_site", requirements={"id"= "\d+"})
	 * @Template()
	 */
	public function editForSiteAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:DomaineSite')->find($id);

		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisée!');
		
		$form = $this->createCreateform($entity, 'DomaineSite');
		return array('entity' => $entity, 'form' =>$form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'un domaine d impact")
	 * @Route("/{id}/modifier_domaine_impact", name="modifier_domaine_impact", requirements={"id"= "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Domaine:editForImpact.html.twig")
	 */
	public function updateForImpactAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:DomaineImpact')->find($id);
		$form = $this->createCreateform($entity, 'DomaineImpact');
		$form->bind($this->get('request'));
		if($form->isValid()) {
			$em->persist($entity);
			//var_dump($entity);exit;
			$em->flush();
			return $this->redirect($this->generateUrl('les_domaines'));
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'un domaine  d'activite")
	 * @Route ("/{id}/modifier_domaine_activite", name="modifier_domaine_activite", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Domaine:editForActivite.html.twig")
	 */
	public function updateForActiviteAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:DomaineActivite')->find($id);
		$form = $this->createCreateForm($entity, 'DomaineActivite');
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return new JsonResponse(array('status' => 'success', 'text' => "L'enregistrement a bien été mis à jour"));
			}
		}
		return new Response($this->renderView('OrangeMainBundle:Domaine:editForActivite.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'un domaine de site")
	 * @Route ("/{id}/modifier_domaine_site", name="modifier_domaine_site", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Domaine:editForSite.html.twig")
	 */
	public function updateForSiteAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:DomaineSite')->find($id);
		$form = $this->createCreateForm($entity, 'DomaineSite');
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return new JsonResponse(array('status' => 'success', 'text' => "L'enregistrement a bien été mis à jour"));
			}
		}
		return new Response($this->renderView('OrangeMainBundle:Domaine:editForSite.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	
	/**
	 * @QMLogger(message="Chargement ajax domaine impact par carto")
	 * @Route("/domaineimpact_by_profilrisque", name="domaineimpact_by_profilrisque")
	 * @Template()
	 */
	public function listImpactByProfilRisqueAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$domaines = $em->getRepository('OrangeMainBundle:DomaineImpact')->findByProfilRisque($request->request->get('id'));
		return array('domaines' => $domaines);
	}
	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\DomaineImpact $entity
	 * @return array
	 */
	protected function addRowInTableForImpact($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$entity->getParent() ? $entity->getParent()->getLibelle() : null,
	  			$this->showEntityStatus($entity, 'etat'),
	  			$this->get('orange.main.actions')->generateActionsForDomaineImpact($entity)
	  	);
	}
	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\DomaineActivite $entity
	 * @return array
	 */
	protected function addRowInTableForActivite($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$this->showEntityStatus($entity, 'etat'),
	  			$this->get('orange.main.actions')->generateActionsForDomaineActivite($entity)
	  	);
	}
	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\DomaineSite $entity
	 * @return array
	 */
	protected function addRowInTableForSite($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$this->showEntityStatus($entity, 'etat'),
	  			$this->get('orange.main.actions')->generateActionsForDomaineSite($entity)
	  	);
	}
	
	
}