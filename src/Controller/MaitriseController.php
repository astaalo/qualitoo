<?php

/*
 * edited by @mariteuw
 */
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Controle;
use App\Annotation\QMLogger;

class MaitriseController extends BaseController {
	
	/**
	 * @QMLogger(message="Maitrise")
	 * @Route("/la_maitrise", name="la_maitrise")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('App\Entity\Controle')->listAll();
		return array('entities' => $entities);
	}
	
}