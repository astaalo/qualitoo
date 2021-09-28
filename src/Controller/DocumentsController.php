<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Service\UploadFile;
use App\Annotation\QMLogger;
use App\Repository\DocumentRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DocumentsController extends BaseController
{
    /**
     * @QMLogger(message="Affichage des documents du sharepoint ")
	 * @Route("/les_documents", name="les_documents")
	 * @Template("document/index.html.twig")
     */
    public function indexAction()
    {
        $entity= new Document();
		$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
		if(!$this->get('session')->get('document_criteria')) {
			$this->get('session')->set('document_criteria', array());
		}
		return array();
    }

    /**
	 * @QMLogger(message="Creation d'un processus")
	 * @Route("/{id}/ajout_document", name="ajout_document", requirements={ "id"=  "\d+"})
	 * @Route("/nouveau_document", name="nouveau_document")
	 * @Template("document/new.html.twig")
	 */
	public function newAction($id = null) {
		$entity = new Document();
		$form   = $this->createForm(DocumentType::class, $entity);
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un processus")
	 * @Route("/{id}/ajouter_document", name="ajouter_document")
	 * @Route("/creer_document", name="creer_document")
	 * @Template("document/new.html.twig")
	 */
	public function createAction(Request $request, $id = null, UploadFile $file) {
		$entity = new Document();
		$form   = $this->createCreateForm($entity, DocumentType::class);
		$form->handleRequest($request);
        //dd($entity);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $fileName= $file->uploadFile($entity->getFile());
                $entity->setFile($fileName);
                //dd($entity);
                $em->persist($entity);
                $em->flush();$this->get('session')->getFlashBag()->add('success', "Document ajouté avec succés.");
               // return $this->redirect($this->generateUrl('les_documents'));
            }
        }
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}

    /**
	 * @QMLogger(message="Chargement ajax des processus")
	 * @Route("/liste_des_documents", name="liste_des_documents")
	 * @Template("document/index.html.twig")
	 */
	public function listAction(Request $request, DocumentRepository $documentRepo) {
		$document = new Document();
		$form = $this->createForm(DocumentType::class, $document);
		$this->modifyRequestForForm($request, $this->get('session')->get('document_criteria'), $form);
		$docs = $form->getData();
		$queryBuilder = $documentRepo->listAll($docs);
		//dd($queryBuilder);
		return $this->paginate($request, $queryBuilder);
	}

    /**
	 * @QMLogger(message="Affichage d'un document")
	 * @Route("/{id}/details_document", name="details_document", requirements={ "id"=  "\d+"})
	 * @Template("document/show.html.twig")
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$document = $em->getRepository('App\Entity\Document')->find($id);
		$this->denyAccessUnlessGranted('read', $document, 'Accés non autorisé');
		return array('entitie' => $document);
	}

	/**
	 * @QMLogger(message="Details d'un document")
	 * @Route("/{id}/details_fichier", name="details_fichier")
	 * @Template("document/document.html.twig")
	 */
	public function detailsFichierAction($id) {
		$document = $this->getDoctrine()->getRepository(Document::class)->find($id);
		
		return array('document'=>$document);
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

		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$em->flush();
				//$this->get('session')->getFlashBag()->add('success', "La modification s\'est effectuée avec succès.");
				return $this->redirect($this->generateUrl('les_documents'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @QMLogger(message="Modification d'un document ")
	 * @Route ("/{id}/edition_document", name="edition_document", requirements={ "id"=  "\d+"})
	 * @Template("document/edit.html.twig")
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Document')->find($id);
		$form = $this->createCreateForm($entity, DocumentType::class);
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
		/**
	 * @QMLogger(message="filtrer la liste des documents")
	 * @Route("/filtrer_les_documents", name="filtrer_les_documents")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(DocumentType::class);
		if($request->getMethod()=='POST') {
			$this->get('session')->set('document_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('document_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	protected function addRowInTable($entity) {
		return array(
				$entity->getLibelle(),
				$entity->getProfil()->__toString(),
				$entity->getDateCreation()->format('d-m-Y'),
	  			//$this->service_status->generateStatusForDocument($entity),
 	  			$this->service_action->generateActionsForDocument($entity)
			);
  }
}
