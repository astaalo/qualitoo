<?php
namespace  App\Controller;

use App\Form\DomaineActiviteType;
use App\Form\DomaineImpactType;
use App\Form\DomaineSiteType;
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
    	$queryBuilder = $em->getRepository('App\Entity\DomaineImpact')->listAllQueryBuilder();
    	return $this->paginate($request, $queryBuilder, 'addRowInTableForImpact');
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des domaines d'activites")
	 * @Route("/liste_des_domaines_dactivite", name="liste_des_domaines_dactivite")
	 * @Template()
	 */
	public function listForActiviteAction(Request $request){
		$em = $this->getDoctrine()->getManager();
    	$queryBuilder = $em->getRepository(DomaineActivite::class)->listAllQueryBuilder();
    	return $this->paginate($request, $queryBuilder, 'addRowInTableForActivite');
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des domaines de site")
	 * @Route("/liste_des_domaines_site", name="liste_des_domaines_site")
	 * @Template()
	 */
	public function listForSiteAction(Request $request){
		$em = $this->getDoctrine()->getManager();
    	$queryBuilder = $em->getRepository(DomaineSite::class)->listAllQueryBuilder();
    	return $this->paginate($request, $queryBuilder, 'addRowInTableForSite');
	}
	
	/**
	 * @QMLogger(message="Creation d'un domaine d'impact")
	* @Route("/{id}/nouveau_domaine_impact", name="nouveau_domaine_impact")
	* @Template("domaine/newForImpact.html.twig")
	*/
	public function newForImpactAction($id) {
		$em = $this->getDoctrine()->getManager();
		$cartographie = $em->getRepository('App\Entity\Cartographie')->find($id);
		$entity = new DomaineImpact();
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisée!');
		$form = $this->CreateForm(DomaineImpactType::class, $entity->setCartographie($cartographie));
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Creation d'un domaine d'activite")
	* @Route("/nouveau_domaine_activite", name="nouveau_domaine_activite")
	* @Template("domaine/newForActivite.html.twig")
	*/
	public function newForActiviteAction(){
		$entity = new DomaineActivite();
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisée!');
		$form = $this->createForm(DomaineActiviteType::class, $entity);
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Creation d'un domaine de site")
	* @Route("/nouveau_domaine_site", name="nouveau_domaine_site")
	* @Template("domaine/newForSite.html.twig")
	*/
	public function newForSiteAction() {
		$entity = new DomaineSite();
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisée!');
		$form = $this->createCreateForm($entity,DomaineSiteType::class);
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un domaine d'impact")
	 * @Route("/creer_domaine_impact", name="creer_domaine_impact")
	 * @Template("domaine/newForImpact.html.twig")
	 */
	public function createForImpactAction(Request $request){
		$entity = new DomaineImpact();
		$form = $this->createCreateForm($entity,DomaineImpactType::class);
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()){
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			$this->addFlash('success', 'Le domaine a été créé avec succès');
			return new JsonResponse(array('status' => 'success', 'text' => 'Le domaine a été créé avec succès'));
		}
		return new Response($this->renderView('domaine/newForImpact.html.twig', array('entity' => $entity, 'form' =>$form->createView())), 303);
	}

	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un domaine d'activite")
	 * @Route("/creer_domaine_activite", name="creer_domaine_activite")
	 * @Template("domaine/newForActivite.html.twig")
	 */
	public function createForActiviteAction(Request $request) {
		$entity = new DomaineActivite();
		$form   = $this->createCreateForm($entity, DomaineActiviteType::class);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			return new JsonResponse(array('status' => 'success', 'text' => 'Le domaine a été créé avec succès'));
		}
		return new Response($this->renderView('domaine/newForActivite.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un domaine de site")
	 * @Route("/creer_domaine_site", name="creer_domaine_site")
	 * @Template("domaine/newForSite.html.twig")
	 */
	public function createForSiteAction(Request $request) {
		$entity = new DomaineSite();
		$form   = $this->createCreateForm($entity, DomaineSiteType::class);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			return new JsonResponse(array('status' => 'success', 'text' => 'Le domaine a été créé avec succès'));
		}
		return new Response($this->renderView('domaine/newForSite.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	
	/**
	 * @QMLogger(message="Details d'un domaine ")
	 * @Route("/{id}/details_domaine", name="details_domaine", requirements={"id"= "\d+"})
	 * @Template()
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$domaine = $em->getRepository('App\Entity\Domaine')->find($id);
		
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
		$entity = $em->getRepository('App\Entity\DomaineImpact')->find($id);
		$form = $this->createCreateform($entity, DomaineImpactType::class);
		
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisée!');
		
		return array('entity' => $entity, 'form' =>$form->createView());
	}
	
	/**
	 * @QMLogger(message="Modification d'un domaine d'activtie")
	 * @Route("/{id}/edition_domaine_dactivite", name="edition_domaine_dactivite", requirements={"id"= "\d+"})
	 * @Template("domaine/editForActivite.html.twig")
	 */
	public function editForActiviteAction($id){
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\DomaineActivite')->find($id);
		$form = $this->createCreateform($entity, DomaineActiviteType::class);

		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisée!');
		
		return array('entity' => $entity, 'form' =>$form->createView());
	}
	
	/**
	 * @QMLogger(message="Modification d'un domaine de site")
	 * @Route("/{id}/edition_domaine_site", name="edition_domaine_site", requirements={"id"= "\d+"})
	 * @Template("domaine/editForSite.html.twig")
	 */
	public function editForSiteAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository(DomaineSite::class)->find($id);

		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisée!');
		
		$form = $this->createCreateform($entity, DomaineSiteType::class);
		return array('entity' => $entity, 'form' =>$form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'un domaine d impact")
	 * @Route("/{id}/modifier_domaine_impact", name="modifier_domaine_impact", requirements={"id"= "\d+"})
	 * @Method("POST")
	 * @Template("domaine/editForImpact.html.twig")
	 */
	public function updateForImpactAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\DomaineImpact')->find($id);
		$form = $this->createCreateform($entity, DomaineImpactType::class);
		$form->handleRequest($request);
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
	 * @Template("domaine/editForActivite.html.twig")
	 */
	public function updateForActiviteAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\DomaineActivite')->find($id);
		$form = $this->createCreateForm($entity, DomaineActiviteType::class);

		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return new JsonResponse(array('status' => 'success', 'text' => "L'enregistrement a bien été mis à jour"));
			}
		}
		return new Response($this->renderView('domaine/editForActivite.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'un domaine de site")
	 * @Route ("/{id}/modifier_domaine_site", name="modifier_domaine_site", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("domaine/editForSite.html.twig")
	 */
	public function updateForSiteAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository(DomaineSite::class)->find($id);
		$form = $this->createCreateForm($entity, DomaineSiteType::class);
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return new JsonResponse(array('status' => 'success', 'text' => "L'enregistrement a bien été mis à jour"));
			}
		}
		return new Response($this->renderView('domaine/editForSite.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	
	/**
	 * @QMLogger(message="Chargement ajax domaine impact par carto")
	 * @Route("/domaineimpact_by_profilrisque", name="domaineimpact_by_profilrisque")
	 * @Template()
	 */
	public function listImpactByProfilRisqueAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$domaines = $em->getRepository('App\Entity\DomaineImpact')->findByProfilRisque($request->request->get('id'));
		return array('domaines' => $domaines);
	}
	
	/**
	 * @param \App\Entity\DomaineImpact $entity
	 * @return array
	 */
	protected function addRowInTableForImpact($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$entity->getParent() ? $entity->getParent()->getLibelle() : null,
	  			$this->showEntityStatus($entity, 'etat'),
	  			$this->service_action->generateActionsForDomaineImpact($entity)
	  	);
	}
	
	/**
	 * @param \App\Entity\DomaineActivite $entity
	 * @return array
	 */
	protected function addRowInTableForActivite($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$this->showEntityStatus($entity, 'etat'),
	  			$this->service_action->generateActionsForDomaineActivite($entity)
	  	);
	}
	
	/**
	 * @param \App\Entity\DomaineSite $entity
	 * @return array
	 */
	protected function addRowInTableForSite($entity) {
	  	return array(
	  			$entity->__toString(),
	  			$this->showEntityStatus($entity, 'etat'),
	  			$this->service_action->generateActionsForDomaineSite($entity)
	  	);
	}
	
	
}
