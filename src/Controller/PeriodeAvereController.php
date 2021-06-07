<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\PeriodeAvere;
use App\Annotation\QMLogger;

/**
 * PeriodeAvere controller.
 *
 */
class PeriodeAvereController extends BaseController
{

    /**
     * Lists all PeriodeAvere entities.
     * @QMLogger(message="Affichage des périodes avérés")
     * @Route("/les_periodes_averes", name="les_periodes_averes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OrangeMainBundle:PeriodeAvere')->findAll();
        return array(
            'entities' => $entities,
        );
    }
   
    /**
     * Creates a new PeriodeAvere entity.
     * @QMLogger(message="Envoi des données saisies lors de le creation d'une periode averee")
     * @Route("/creer_periode_avere", name="creer_periode_avere")
     * @Method("POST")
     * @Template("OrangeMainBundle:PeriodeAvere:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new PeriodeAvere();
        $form = $this->createCreateForm($entity,'PeriodeAvere');
        if($request->getMethod()=='POST'){
	        $form->handleRequest($request);
	        $entity->setSociete($this->getUser()->getSociete());
	        if ($form->isValid()) {
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($entity);
	            $em->flush();
	            $this->get('session')->getFlashBag()->add('success', "Période défini avec succés.");
	            return $this->redirect($this->generateUrl('les_menaces_averes',array('periode_id'=>$entity->getId())));
	        }
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new PeriodeAvere entity.
     * @QMLogger(message="Creation d'une période avérée")
     * @Route("/nouvelle_periode_avere", name="nouvelle_periode_avere")
     * @Method("GET")
     * @Template()
     */
    public function newAction(){
    	$currentDate= new \DateTime('now');
        $entity = new PeriodeAvere();
        $form   = $this->createCreateForm($entity,'PeriodeAvere');
        $periodes = $this->getDoctrine()
        					->getRepository('OrangeMainBundle:PeriodeAvere')
        					->findBy(array('societe'=>$this->getUser()->getSociete()),  array('id' => 'DESC'));
        $lastPeriode=null;
        if(count($periodes)){
        	$lastPeriode = $periodes[0];
        	if($lastPeriode->getDatefin()->getTimestamp()>$currentDate->getTimestamp())
        		return $this->redirect($this->generateUrl('les_menaces_averes',array('periode_id'=>$lastPeriode->getId())));
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'lastPeriode' => $lastPeriode
        );
    }

}
	