<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\DocumentType;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Document;
use App\Repository;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Annotation\QMLogger;
use App\Service\UploadFile;

class DocumentsController extends BaseController
{
    /**
     * @QMLogger(message="Affichage des documents du sharepoint ")
	 * @Route("/les_documents", name="les_documents")
	 * @Template()
     */
    public function index()
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
	 * @Template()
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
	 * @Template()
	 */
	public function listAction(Request $request, DocumentRepository $docRepo) {
		$document = new Document();
		$form = $this->createForm(DocumentType::class, $document);
		$this->modifyRequestForForm($request, $this->get('session')->get('document_criteria'), $form);
		$docs = $form->getData();
		$queryBuilder = $docRepo->listAll($docs);
		//dd($queryBuilder);
		//return $this->paginate($request, $queryBuilder);
	}

    /**
	 * @QMLogger(message="Affichage d'un document")
	 * @Route("/{id}/details_document", name="details_document", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$document = $em->getRepository('App\Entity\Document')->find($id);
		$this->denyAccessUnlessGranted('read', $document, 'Accés non autorisé');
		return array('entitie' => $document);
	}
	
}
