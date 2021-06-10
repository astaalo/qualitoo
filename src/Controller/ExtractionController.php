<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\QueryBuilder;
use App\Form\ExtractionType;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Execution;
use App\Annotation\QMLogger;

class ExtractionController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des extractions")
	 * @Route("/les_extractions", name="les_extractions")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$extractions = $em->getRepository('App\Entity\Extraction')->findAll();
		$societe = $em->getRepository('App\Entity\Societe')->find($this->getUser()->getSociete()->getId());
		$form = $this->createCreateForm($societe, 'OurReporting');
		return array('form' => $form->createView(), 'extractions' => $extractions);
	}

	/**
	 * @QMLogger(message="Changement des extractions")
	 * @Route("/changer_les_extractions", name="changer_les_extractions")
	 * @Method("POST")
	 * @Template()
	 */
	public function changeAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$societe = $em->getRepository('App\Entity\Societe')->find($this->getUser()->getSociete()->getId());
		$form = $this->createCreateForm($societe, 'OurReporting');
		$form->handleRequest($request);
		$em->persist($societe);
		$em->flush();
		$this->get('session')->getFlashBag()->add('success', "Les colonnes concernant par le reporting a été mises avec succés.");
		return $this->redirect($this->generateUrl('les_extractions'));
	}
	
}
