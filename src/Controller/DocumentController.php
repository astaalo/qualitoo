<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Document;
use App\Entity\TypeDocument;
use App\Criteria\DocumentCriteria;
use App\Annotation\QMLogger;

class DocumentController extends BaseController {
	

	/**
	 * @QMLogger(message="Affichage des documents du sharepoint ")
	 * @Route("/les_documents", name="les_documents")
	 * @Template()
	 */
	public function indexAction(Request $request) {
		$position=$this->get('session')->get('document_criteria')['typeDocument'];
		
		$position=$this->get('session')->get('document_criteria')['typeDocument'];
		
		$data = $this->get('session')->get('document_criteria');
		$form = $this->createForm(DocumentCriteria::class, new Document(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		$this->modifyRequestForForm($request, $data, $form);
		return array('form' => $form->createView(),'position'=>intval($position));
	}
	
	/**
	 * @QMLogger(message="Details d'un document")
	 * @Route("/{id}/details_fichier", name="details_fichier")
	 * @Template()
	 */
	public function detailsFichierAction($id) {
		$document = $this->getDoctrine()->getRepository(Document::class)->find($id);
		
		return array('document'=>$document);
	}
	
	
	/**
	 * @QMLogger(message="Affichage document par type et par annee ")
	 * @Method({"GET","POST"})
	 * @Route("/{year}/{type}/documents", name="documents")
	 * @Template()
	 */
	public function documentAction(Request $request,$year, $type) {
		$em = $this->getDoctrine()->getManager();
		$entityType = $em->find(TypeDocument::class, $type);
		$currrentYear = date('Y');
		$form = $this->createForm(DocumentCriteria::class, new Document(), array('attr' => array('em' => $this->getDoctrine()->getManager(), 'year'=>$year)));
		if($request->getMethod()=='POST') {
			$this->get('session')->set('document_criteria', $request->request->get($form->getName()));
		}
		$data = $this->get('session')->get('document_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		$documents = $em->getRepository(Document::class)->getDocumentsByType($form->getData())->getQuery()->execute();
		return array('form' => $form->createView(),'position'=>$year, 'type'=>$type, 'currentYear'=>$currrentYear,'documents'=>$documents, 'entityType'=>$entityType);
	}
	
	
	
	/**
	 * @QMLogger(message="Chargement document par ajax ")
	 * @Route("/liste_des_documents", name="liste_des_documents")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(DocumentCriteria::class);
		$this->modifyRequestForForm($request, $this->get('session')->get('document_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Document')->getDocumentsByType($form->getData());
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @Route("/{year}/{type}/{link}/choix_type", name="choix_type")
	 */
	public function choixCartoAction($type,$link,$year){
		$this->get('session')->set('document_criteria', array('annee'=>$year, 'typeDocument'=>$type));
		return $this->redirect($this->generateUrl($link,array('type'=>$type, 'year'=>$year)));
	}
	
	/**
	 * @QMLogger(message="Ajout d'un document ")
	 * @Route("/{type}/nouveau_document", name="nouveau_document")
	 * @Template()
	 */
	public function newAction($type) {
		$entity = new Document();
		$typeDocument = $this->getDoctrine()->getRepository(TypeDocument::class)->find($type);
		$currrentYear = date('Y');
		$entity->setTypeDocument($typeDocument);
		$form   = $this->createCreateForm($entity, DocumentType::class);
		return array('entity' => $entity, 'form' => $form->createView(), 'type'=>$type, 'year'=>$currrentYear);
	}

	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation du dcument ")
	 * @Route("/{type}/creer_document", name="creer_document")
	 * @Template("document/new.html.twig")
	 */
	public function createAction(Request $request,$type) {
		$entity = new Document();
		$form   = $this->createCreateForm($entity, DocumentType::class);
		$typeDocument = $this->getDoctrine()->getRepository(TypeDocument::class)->find($type);
		$entity->setTypeDocument($typeDocument);
		$currrentYear = date('Y');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity->setUtilisateur($this->getUser());
			$file = $entity->getFile();
			
			// Generate a unique name for the file before saving it
			$fileName = md5(uniqid()).'.'.$file->guessExtension();
			
			// Move the file to the directory where brochures are stored
			$brochuresDir = $this->container->getParameter('kernel.root_dir').'/../web/upload/sharepoint';
			$file->move($brochuresDir, $fileName);
			
			// instead of its contents
			$entity->setNomFichier($file->getClientOriginalName());
			$entity->setFile($fileName);
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add('success', "Le document a été ajouté avec succés.");
			return $this->redirect($this->generateUrl('choix_type',array('link'=>'documents','year'=>date('Y'), 'type'=>$entity->getTypeDocument()->getId())));
		}
		return $this->render('OrangeMainBundle:Document:new.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'type'=>$type, 'year'=>$currrentYear));
	}
	
	
	/**
	 * @QMLogger(message="Suppression d'un document ")
	 * @Method({"GET","POST"})
	 * @Route ("/{id}/supprimer_document", name="supprimer_document", requirements={ "id"=  "\d+"})
	 * @Template("document/confirmDeletion.html.twig")
	 */
	public function supprimeAction(Request $request,$id) {
		$em = $this->getDoctrine()->getManager();
		
		$entity = $em->getRepository('App\Entity\Document')->find($id);
		
		if(!$entity){
			$this->get('session')->getFlashBag()->add('success', "Le document n'existe pas");
			return $this->redirect($this->generateUrl('dashboard'));
		}else{
			if($request->getMethod()=='POST')
			{
				$entity->setDeleted(true);
				$em->persist($entity);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "Le document a été supprimé avec succés.");
				return new Response($this->redirect($this->generateUrl('choix_type',array('link'=>'documents','year'=>date('Y'), 'type'=>$entity->getTypeDocument()->getId()))));
			}
		}
		return array('entity' => $entity);
	}
	
	/**
	 * @QMLogger(message="Modification d'un document ")
	 * @Route ("/{id}/edition_document", name="edition_document", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Document')->find($id);
		$form = $this->createCreateForm($entity, DocumentType::class);
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification du dcument ")
	 * @Route ("/{id}/modifier_document", name="modifier_document", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("document/edit.html.twig")
	 */
	public function updateAction(Request $request,$id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Document')->find($id);
		$form = $this->createCreateForm($entity, DocumentType::class);
		$request = $request;
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				return $this->redirect($this->generateUrl('choix_type',array('link'=>'documents','year'=>date('Y'), 'type'=>$entity->getTypeDocument()->getId())));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @param \App\Entity\Site $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->getLibelle(),
	  			$entity->getUtilisateur()->__toString(),
	  			$entity->getDateCreation()->format('d-m-Y'),
// 	  			$this->service_status->generateStatusForEntity($entity),
// 	  			$this->service_action->generateActionsForSite($entity)
	  		);
	}
}

