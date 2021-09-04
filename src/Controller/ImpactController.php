<?php
namespace App\Controller;

use App\Entity\RisqueHasImpact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Impact;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Criteria\ImpactCriteria;
use App\Annotation\QMLogger;

class ImpactController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des impacts")
	 * @Route("/les_impacts", name="les_impacts")
	 * @Template()
	 */
	public function indexAction() {
		$this->get('session')->set('risquehasimpact_criteria', array());
		return array();
	}
	
	/**
	 * @QMLogger(message="Filtre sur les impacts")
	 * @Route("/filtrer_les_impacts", name="filtrer_les_impacts")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(ImpactCriteria::class, new Impact());
		if($request->getMethod()=='POST') {
			$this->get('session')->set('risquehasimpact_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('risquehasimpact_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des impacts")
	 * @Route("/liste_des_impacts", name="liste_des_impacts")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(ImpactCriteria::class, new Impact());
		$this->modifyRequestForForm($request, $this->get('session')->get('risquehasimpact_criteria'), $form);
		$queryBuilder = $em->getRepository(RisqueHasImpact::class)->listAllQueryBuilder($form->getData());
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Affichage d'un impact")
	 * @Route("/{id}/details_impact", name="details_impact", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$impact = $em->getRepository('App\Entity\RisqueHasImpact')->findOneByImpact($id);
        if (!$impact) {
            throw $this->createNotFoundException('Aucun impact trouvé pour cet id : ' . $id);
        }
		return array('entity' => $impact);
	}
	
	/**
	 * @QMLogger(message="Extraction des impacts")
	 * @Route("/exporter_les_impacts", name="exporter_les_impacts")
	 * @Template()
	 */
	public function exportAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(ImpactCriteria::class);
		$this->modifyRequestForForm($request, $this->get('session')->get('impact_criteria'), $form);
		$queryBuilder = $em->getRepository('App\Entity\Impact')->listQueryBuilder($form->getData());
		$data = $this->orange_main_core->getMapping('Impact')->mapForBasicExport($queryBuilder->getQuery()->getResult());
		$reporting = $this->orange_main_core->getReporting('Impact')->extractByImpact($data);
		$reporting->getResponseAfterSave('php://output', 'Extraction des impacts');
	}
	
	/**
	 * @param \App\Entity\RisqueHasImpact $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
	  			$entity->getRisque()?($entity->getRisque()->getMenace()?$entity->getRisque()->getMenace()->__toString():null):null,
	  			sprintf('<a href="%s" >%s<a/>', $this->generateUrl('details_impact', array('id' => $entity->getId())), $entity->getImpact()->__toString()),
	  			$entity->getImpact()?($entity->getImpact()->getCritere()? $entity->getImpact()->getCritere()->getLibelle() : 'Non renseigné'):null,
	  			$entity->getImpact()?($entity->getImpact()->hasEvaluation() ? $entity->getGrille()->getNote()->__toString() : 'Aucun évaluation'):null,
	  			$this->service_action->generateActionsForImpact($entity)
	  		);
	}
}
