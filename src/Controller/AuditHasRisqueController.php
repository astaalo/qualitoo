<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\AuditHasRisque;
use App\Form\AuditHasRisqueType;

/**
 * AuditHasRisque controller.
 *
 * @Route("/audithasrisque")
 */
class AuditHasRisqueController extends Controller
{

    /**
     * Lists all AuditHasRisque entities.
     *
     * @Route("/", name="audithasrisque")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OrangeMainBundle:AuditHasRisque')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new AuditHasRisque entity.
     *
     * @Route("/", name="audithasrisque_create")
     * @Method("POST")
     * @Template("OrangeMainBundle:AuditHasRisque:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new AuditHasRisque();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('audithasrisque_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a AuditHasRisque entity.
     *
     * @param AuditHasRisque $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AuditHasRisque $entity)
    {
        $form = $this->createForm(new AuditHasRisqueType(), $entity, array(
            'action' => $this->generateUrl('audithasrisque_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new AuditHasRisque entity.
     *
     * @Route("/new", name="audithasrisque_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AuditHasRisque();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a AuditHasRisque entity.
     *
     * @Route("/{id}", name="audithasrisque_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:AuditHasRisque')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuditHasRisque entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing AuditHasRisque entity.
     *
     * @Route("/{id}/edit", name="audithasrisque_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:AuditHasRisque')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuditHasRisque entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a AuditHasRisque entity.
    *
    * @param AuditHasRisque $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AuditHasRisque $entity)
    {
        $form = $this->createForm(new AuditHasRisqueType(), $entity, array(
            'action' => $this->generateUrl('audithasrisque_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing AuditHasRisque entity.
     *
     * @Route("/{id}", name="audithasrisque_update")
     * @Method("PUT")
     * @Template("OrangeMainBundle:AuditHasRisque:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:AuditHasRisque')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuditHasRisque entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('audithasrisque_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a AuditHasRisque entity.
     *
     * @Route("/{id}", name="audithasrisque_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:AuditHasRisque')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AuditHasRisque entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('audithasrisque'));
    }

    /**
     * Creates a form to delete a AuditHasRisque entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('audithasrisque_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
