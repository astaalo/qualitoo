<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Site;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\TableauBord;
use App\Annotation\QMLogger;

class TableauBordController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des tableaux de bord")
	 * @Route("/les_tableaux_bords", name="les_tableaux_bords")
	 * @Template()
	 */
	public function indexAction() {
		return array();
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des tableaux de bord")
	 * @Route("/liste_des_tableaux_bords", name="liste_des_tableaux_bords")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('App\Entity\TableauBord')->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Nouveau tableau de bord")
	 * @Route("/nouveau_tableau_bord", name="nouveau_tableau_bord")
	 * @Template()
	 */
	public function newAction() {
		$entity = new TableauBord();
		$form   = $this->createCreateForm($entity, TableauBordType::class);
		return array('entity' => $entity, 'form' => $form->createView());
	}

	
	/**
	 * @param \App\Entity\Site $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->getLibelle(),
	  			'  ',
	  			'   ',
// 	  			$this->service_status->generateStatusForEntity($entity),
// 	  			$this->service_action->generateActionsForSite($entity)
	  		);
	}
}

