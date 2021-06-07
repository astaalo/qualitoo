<?php
/*
 * edit by @mariteuw
 */
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\GrilleImpact;
use App\Form\GrilleImpactType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Annotation\QMLogger;

class GrilleController extends BaseController{
	
	/**
	 * @QMLogger(message="Affichage grille par critere")
	 * @Route("/grille_by_critere", name="grille_by_critere")
	 * @Template()
	 */
	public function listByCritereAction() {
	  $em = $this->getDoctrine()->getManager();
	  $arrData = $em->getRepository('OrangeMainBundle:Grille')->listByCritere($this->getRequest()->request->get('id'));
	  $output = array(0 => array('id' => '', 'libelle' => 'Choisir un niveau ...'));
	  foreach ($arrData as $data) {
			$output[] = array('id' => $data['id'], 'libelle' => $data['libelle']);
      }
      $response = new Response();
      $response->headers->set('Content-Type', 'application/json');
      return $response->setContent(json_encode($output));
	}
	
}
