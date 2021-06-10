<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\Audit;
use App\Entity\AuditHasRisque;

/**
 * Audit controller.
 *
 */
class AuditController extends BaseController
{

    /**
     * Lists all Audit entities.
     *
     * @Route("/les_audits", name="les_audits")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('App\Entity\Audit')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Audit entity.
     *
     * @Route("/creer_audit", name="creer_audit")
     * @Method("POST")
     * @Template("OrangeMainBundle:Audit:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Audit();
        $form = $this->createCreateForm($entity, 'Audit');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('audit_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    
    /**
     * @Route("/{ids}/{carto}/valider_teste_risque", name="valider_teste_risque")
     * @Template()
     */
    public function validerAuditRisqueAction(Request $request, $ids, $carto){
    	$em = $this->getDoctrine()->getManager();
    	$entity = new Audit();
    	$datas = $datas=explode(',',str_replace(" ", "",$ids));
    	$cartographie = $em->getRepository('App\Entity\Cartographie')->find($carto);
    	$risques = $this->getDoctrine()->getRepository('OrangeMainBundle:Risque')
    					->createQueryBuilder('r')
    					->leftJoin('r.cartographie','c')
    					->andWhere('r.id in (:ids)')->setParameter('ids',$datas)
    					->andWhere('c.id =:carto')->setParameter('carto',$carto)
    					->getQuery()->execute();
    	foreach ($risques as $risque){
    		$auditOfRisque = new AuditHasRisque();
    		$auditOfRisque -> setRisque($risque);
    		$auditOfRisque ->setAudit($entity);
    		$auditOfRisque -> setDateAjout(new \DateTime("NOW"));
    		$entity->addRisque($auditOfRisque);
    	}
    	$form   = $this->createCreateForm($entity, 'Audit');
    	if($request->getMethod()=='POST'){
    		$entity -> setAuteur($this->getUser());
	    	$form->handleRequest($request);
	     	if ($form->isValid()) {
	     		$em->persist($entity);
	     		$em->flush();
	     		return $this->redirect($this->generateUrl('les_risques'));
	     	}
    	}
    	return array('form' => $form->createView(), 'ids'=>$ids, 'carto'=>$carto, 'risques'=>$risques);
    }
    
    /**
     * @Route("/{id}/cloture_valider_teste_risque", name="cloture_valider_teste_risque")
     */
    public function cloturerValiderAuditRisqueAction(Request $request, $id){
    	$em = $this->getDoctrine()->getManager();
    	$entity = $this->getDoctrine()->getRepository('OrangeMainBundle:Audit')->find($id);
    	$entity -> setAuteur($this->getUser());
    	
    	$form->handleRequest($request);
    	if ($form->isValid()) {
    		
    	}
    	return array('form' => $form->createView());
    }

    

}
