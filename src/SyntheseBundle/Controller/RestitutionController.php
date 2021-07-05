<?php
namespace Orange\SyntheseBundle\Controller;

use Orange\QuickMakingBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Entity\Risque;
use Orange\MainBundle\Criteria\RisqueCriteria;
use Orange\QuickMakingBundle\Annotation\QMLogger;

class RestitutionController extends BaseController {


	/**
	 * @QMLogger(message="Affichage matrice par carto")
	 * @Route("/{carto}/{type}/la_restitution", name="la_restitution")
	 * @Template("restitution/matrice.html.twig")
	 */
	public function matriceAction(Request $request, $carto, $type) {
		$dm = $this->get('doctrine_mongodb')->getManager();
		$probabiteKPIs = $graviteKPIs = false;
		$entity = new Risque();
		$form = $this->createForm(new RisqueCriteria(), new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risque_criteria', array());
			$this->get('session')->set('risque_criteria', $request->request->get($form->getName()));
			return new JsonResponse($this->generateUrl('la_restitution', array('carto'=>$carto, 'type'=>$type)));
		} 	elseif($this->get('session')->get('risque_criteria')==null || count($this->get('session')->get('risque_criteria'))==0) {
			$this->get('session')->set('risque_criteria', array('cartographie' => $carto));
		}
		$this->denyAccessUnlessGranted('matrice', $entity, 'Accés non autorisée!');
		
		$data = $this->get('session')->get('risque_criteria');
		$this->modifyRequestForForm($request, $data, $form);
		$dm->getRepository('OrangeSyntheseBundle:Risque')->getMatrice($form->getData(), $type, $probabiteKPIs, $graviteKPIs);
		/*foreach($probabiteKPIs as $probabiteKPI) {
			var_dump($probabiteKPI);echo '<br><br>';
		}exit;*/
		$entities = $this->get('orange_main.core')->getMapping('Risque', true)->mapForMatrice($probabiteKPIs, $graviteKPIs, $type, $form->getData());
		return array('entities' => $entities, 'form' => $form->createView(), 'carto'=>$carto, 'type' => $type);
	}

}
