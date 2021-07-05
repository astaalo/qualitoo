<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\Equipement;
use App\Criteria\EquipementCriteria;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\EquipementType;
use App\Annotation\QMLogger;

/**
 * Equipement controller.
 *
 */
class EquipementController extends BaseController
{

    /**
     * Lists all Equipement entities.
     * @QMLogger(message="Affichage des equipements")
     * @Route("/les_equipements", name="les_equipements")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
    	$entity= new Equipement();
    	$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
        $this->get('session')->set('equipement_criteria', array());
		return array();
    }
   
    /**
     * @QMLogger(message="Filtre sur les equipements")
     * @Route("/filtrer_les_equipements", name="filtrer_les_equipements")
     * @Template()
     */
    public function filterAction(Request $request) {
    	$form = $this->createForm(EquipementCriteria::class, new Equipement());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('activite_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('equipement_criteria'), $form);
    		return array('form' => $form->createView());
    	}
    }
    
    /**
     * @QMLogger(message="Chargemetn ajax des equipement")
     * @Route("/liste_des_equipements", name="liste_des_equipements")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(EquipementCriteria::class, new Equipement());
    	$this->modifyRequestForForm($request, $this->get('session')->get('equipement_criteria'), $form);
    	$criteria = $form->getData();
    	$queryBuilder = $em->getRepository('App\Entity\Equipement')->listAllQueryBuilder($criteria);
    	return $this->paginate($request, $queryBuilder);
    }
   
    /**
     * Creates a new Equipement entity.
     * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un equipement")
     * @Route("/{type}/creer_equipement", name="creer_equipement")
     * @Method("POST")
     * @Template("OrangeMainBundle:Equipement:new.html.twig")
     */
    public function createAction(Request $request,$type)
    {
        $entity = new Equipement();
        $form = $this->createCreateForm($entity, 'Equipement');
        $form->handleRequest($request);
		$entity->setType($type);
		$entity->setEtat(true);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('status' => 'success', 'text' => 'L\'ajout s\'est déroulé avec succés'));
        }

        return new Response($this->renderView('OrangeMainBundle:Equipement:new.html.twig', array('entity' => $entity, 'type'   => $type,'form' => $form->createView())), 303);
    }


    /**
     * Displays a form to create a new Equipement entity.
     * @QMLogger(message="Creation equipement")
     * @Route("/{type}/nouvel_equipement", name="nouvel_equipement")
     * @Method("GET")
     * @Template()
     */
    public function newAction($type)
    {
        $entity = new Equipement();
        $entity->setType($type);
        $form   = $this->createForm(EquipementType::class, $entity);
        
        $this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'type'   => $type
        );
    }

    /**
     * Finds and displays a Equipement entity.
     * @QMLogger(message="Details d'un equipement")
     * @Route("/{id}/details_equipement", name="details_equipement")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('App\Entity\Equipement')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Equipement entity.');
        }
        
        $this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
        
        $deleteForm = $this->createDeleteForm($id);
        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Equipement entity.
     * @QMLogger(message="Envoi des donnees saisies lors de la modifcation d'un equipement")
     * @Route("/{id}/edition_equipement", name="edition_equipement")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('App\Entity\Equipement')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Equipement entity.');
        }
        $this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
        $editForm = $this->createEditForm($entity);
        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Equipement entity.
    *
    * @param Equipement $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Equipement $entity)
    {
        $form = $this->createForm(EquipementType::class, $entity, array(
            'action' => $this->generateUrl('modifier_equipement', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Equipement entity.
     *
     * @Route("/{id}/modifier_equipement", name="modifier_equipement")
     * @Template()
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('App\Entity\Equipement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Equipement entity.');
        }

        $editForm = $this->createEditForm($entity);
        if ($request->getMethod() == 'POST') {
	        $editForm->handleRequest($request);
	        if ($editForm->isValid()) {
	        	$em->persist($entity);
	            $em->flush();
	            return new JsonResponse(array('status' => 'success', 'text' => 'Modification avec succés'));
	        }
       }
       return new Response($this->renderView('OrangeMainBundle:Equipement:edit.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
    }
    /**
     * Deletes a Equipement entity.
     *
     * @Route("/{id}/supprimer_equipement", name="supprimer_equipement")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('App\Entity\Equipement')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Equipement entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('equipement'));
    }

    /**
     * Creates a form to delete a Equipement entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('equipement_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * @param Equipement $entity
     * @return array
     */
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getCode(),
    			$entity->getLibelle(),
    			$this->service_action->generateActionsForEquipement($entity)
    	);
    }
}
