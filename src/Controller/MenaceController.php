<?php
namespace App\Controller;

use App\Form\MenaceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Menace;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\QueryBuilder;
use App\Entity\MenaceAvere;
use App\Annotation\QMLogger;

class MenaceController extends BaseController {

	/**
	 * @QMLogger(message="Validation Menace avéré")
	 * @Route("/{ids}/{periode_id}/valider_ajout_menace_avered", name="valider_ajout_menace_avered")
	 * @Template()
	 */
	public function  valideMenaceAverablesAction(Request $request,$ids,$periode_id){
		$em = $this->getDoctrine()->getManager();
		$datas   = explode(',',str_replace(" ", "",$ids));
		$menaces = $this->getDoctrine()->getRepository(Menace::class)->findBy(array('id'=>$datas));
		$periode = $this->getDoctrine()->getRepository(Menace::class)->find($periode_id);
		foreach ($menaces as $value){
			$ma = new MenaceAvere();
			$ma->setMenace($value);
			$ma->setPeriode($periode);
			$ma->setDateAjout(new \DateTime("NOW"));
			$em ->persist($ma);
			$em ->flush();
		}
		$this->get('session')->getFlashBag()->add('success', "Risques avérés ajoutés avec succés.");
		return $this->redirect($this->generateUrl('les_menaces_averes',array('periode_id'=>$periode_id)));
	}
	
	/**
	 * @QMLogger(message="Liste des Menaces avérées")
	 * @Route("/{periode_id}/les_menaces_averes", name="les_menaces_averes")
	 * @Template()
	 */
	public function menaceAveresAction($periode_id) {
		return array('periode_id'=>$periode_id);
	}
	
	/**
	 * @QMLogger(message="Chargement ajax Menace avéré")
	 * @Route("/{periode_id}/liste_des_menaces_averes", name="liste_des_menaces_averes")
	 * @Template()
	 */
	public function listAveresAction(Request $request,$periode_id) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('App\Entity\Menace')->listAvereByPeriode($periode_id);
		return $this->paginate($request, $queryBuilder,'addRowAvere');
	}
	
	
	/**
	 * @Route("/{periode_id}/les_menaces_averables", name="les_menaces_averables")
	 * @Template()
	 */
	public function menaceAverablesAction($periode_id) {
		return array('periode_id'=>$periode_id);
	}
	
	/**
	 * @Route("/{periode_id}/liste_des_menaces_averales", name="liste_des_menaces_averales")
	 * @Template()
	 */
	public function listAverablesAction(Request $request,$periode_id) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('App\Entity\Menace')->listAverableByPeriode($periode_id);
		return $this->paginate($request, $queryBuilder,'addRowAverable');
	}
	
	/**
	 * @QMLogger(message="Liste des menaces")
	 * @Route("/les_menaces", name="les_menaces")
	 * @Template()
	 */
	public function indexAction() {
		$entity= new Menace();
		//$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
		
		return array();
	}
	
	/**
	 * @QMLogger(message="Chargement ajax Menace")
	 * @Route("/liste_des_menaces", name="liste_des_menaces")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('App\Entity\Menace')->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Creation Menace")
	 * @Route("/nouvelle_menace", name="nouvelle_menace")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Menace();
		$form   = $this->createForm(MenaceType::class, $entity);
		//$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'une menace")
	 * @Route("/creer_menace", name="creer_menace")
	 * @Template("menace/new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Menace();
		$form   = $this->createCreateForm($entity, 'Menace');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			return new JsonResponse(array('status' => 'success', 'text' => 'Le risque a bien été ajouté avec succés'));
		}
		return new Response($this->renderView('menace/new.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	
	/**
	 * @QMLogger(message="Affichage d'une Menace")
	 * @Route("/{id}/details_menace", name="details_menace", requirements={ "id"=  "\d+"})
	 * @Template()
	 *
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$menace = $em->getRepository('App\Entity\Menace')->find($id);
		
		$this->denyAccessUnlessGranted('create', $menace, 'Accés non autorisé');
		
		return array('entity' => $menace);
	}
	
	/**
	 * @QMLogger(message="Modification Menace")
	 * @Route ("/{id}/edition_menace", name="edition_menace", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Menace')->find($id);
		$form = $this->createForm(MenaceType::class, $entity);
		
		//$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
		
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
    
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modifcation d'une menace")
	 * @Route ("/{id}/modifier_menace", name="modifier_menace", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("menace/edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Menace')->find($id);
		$form = $this->createCreateForm($entity, 'Menace');
		$request = $request;
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return new JsonResponse(array('status' => 'success', 'text' => 'Le risque a bien été mis à jour'));
			}
		}
		return new Response($this->renderView('menace/edit.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
	}
	  
	/**
	 * @todo ajoute un filtre
	 * @param sfWebRequest $request
	 */
	protected function setFilter(QueryBuilder$queryBuilder, $aColumns, Request $request) {
		parent::setFilter($queryBuilder, array('q.libelle'), $request);
	}
	
	/**
	 * @param \App\Entity\Menace $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->getLibelle(),
	  			$entity->getLibelleCartographie(),
				  $this->service_action->generateActionsForMenace($entity),
				  
	  		);
	}
	
	
	/**
	 * @param \App\Entity\Menace $entity
	 * @return array
	 */
	protected function addRowAverable($entity) {
		return array(
				'<td  width="35" ><input type="checkbox" name="avered[]" class="chkbox"  value="'.$entity->getId().'" id="check'.$entity->getId().'"/></td>',
				$entity->getLibelle(),
				$entity->getLibelleCartographie(),
				'-'
			//	$this->service_action->generateActionsForMenace($entity)
		);
	}
	

	/**
	 * @param \App\Entity\Menace $entity
	 * @return array
	 */
	protected function addRowAvere($entity) {
		return array(
				$entity->getLibelle(),
				$entity->getLibelleCartographie(),
				'-'
		);
	}

	
	/**
	 *
	 * @QMLogger(message="Comparaison Menace")
	 * @Route ("/{id}/compare_menace", name="compare_menace", requirements={ "id"=  "\d+"})
	 * 
	 */ 
	public function compareAction(Request $request, $id ) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Menace')->find($id);
		$lib= $entity->getLibelleSansCarSpecial();
		$same=$em->getRepository('App\Entity\Menace')->findBy(array("libelleSansCarSpecial"=>$lib,"etat"=>1));
		
		return new Response($this->renderView('menace/compare.html.twig', array(
			'menaces' =>$same,
		))) ;
	}


	/**
	 *
	 * @QMLogger(message="Comparaison Menace")
	 * @Route ("delete_menace/{id}", name="delete_menace", requirements={ "id"=  "\d+"})
	 * 
	 */
		public function deleteAction(Request $request, $id ) {
			$em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('App\Entity\Menace')->findOneBy(array("id"=>$id,"etat"=>1));
				$ok =  $entity->setEtat(0);
				$em->flush();
				$lib= $ok->getLibelleSansCarSpecial();
				$same=$em->getRepository('App\Entity\Menace')->findOneBy(array("libelleSansCarSpecial"=>$lib,"etat"=>1));
				$menacesToUpdate= $em->getRepository('App\Entity\Risque')->findBy(array("menace"=>$id));
				foreach ($menacesToUpdate as $menaceToUpdate) {	
					$menaceToUpdate->setMenace($same);
		    		$menaceToUpdate->setTobeMigrate(1);
				}
				$em->flush();	
				$em->remove($ok);
				$em->flush();		
			return $this->redirect($this->generateUrl('les_menaces', array()));
		}
}

