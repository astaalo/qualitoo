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
		$queryBuilder = $em->getRepository('OrangeMainBundle:TableauBord')->listAllQueryBuilder();
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Nouveau tableau de bord")
	 * @Route("/nouveau_tableau_bord", name="nouveau_tableau_bord")
	 * @Template()
	 */
	public function newAction() {
		$entity = new TableauBord();
		$form   = $this->createCreateForm($entity, 'TableauBord');
		return array('entity' => $entity, 'form' => $form->createView());
	}

	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\Site $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->getLibelle(),
	  			'  ',
	  			'   ',
// 	  			$this->get('orange_main.status')->generateStatusForEntity($entity),
// 	  			$this->get('orange.main.actions')->generateActionsForSite($entity)
	  		);
	}
}

