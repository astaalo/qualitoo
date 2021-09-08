<?php
namespace App\Controller;

use App\Annotation\QMLogger;
use App\Entity\Processus;
use App\Entity\Risque;
use App\Entity\RisqueMetier;
use App\Form\ActiviteType;
use App\Repository\RisqueRepository;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Activite;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\QueryBuilder;
use App\Criteria\ActiviteCriteria;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ActiviteController extends BaseController
{

	/**
	 * @QMLogger(message="Affichage de la liste des activités")
	 * @Route("/les_activites", name="les_activites")
	 * @Template()
	 */
	public function indexAction()
	{
		$entity = new Activite();
		$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');

		if (!$this->get('session')->get('activite_criteria')) {
			$this->get('session')->set('activite_criteria', array());
		}
		return array();
	}

	/**
	 * @QMLogger(message="Filtre sur la liste des activités")
	 * @Route("/filtrer_les_activites", name="filtrer_les_activites")
	 * @Template()
	 */
	public function filterAction(Request $request)
	{
		$form = $this->createForm(ActiviteCriteria::class);
		if ($request->getMethod() == 'POST') {
			$this->get('session')->set('activite_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('activite_criteria'), $form);
			return array('form' => $form->createView());
		}
	}

	/**
	 * @Route("/liste_des_activites", name="liste_des_activites")
	 * @Template()
	 */
	public function listAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(ActiviteCriteria::class, new Activite());
		$this->modifyRequestForForm($request, $this->get('session')->get('activite_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository(Activite::class)->listAllQueryBuilder($criteria);
		return $this->paginate($request, $queryBuilder);
	}

	/**
	 * @QMLogger(message="Suppression d'une activite")
	 * @Route("/{id}/supprimer_activite", name="supprimer_activite", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$activite = $em->getRepository('App\Entity\Activite')->find($id);
		$this->denyAccessUnlessGranted('delete', $activite, 'Accés non autorisé');
		$processus = $activite->getProcessus();
		if (count($activite->getRisque()) <= 0) {
			$em->remove($activite);
			$em->flush();
			$this->get('session')->getFlashBag()->add('success', "La suppression de l'activité s'est déroulée avec succés.");
		} else {
			$this->get('session')->getFlashBag()->add('error', "Veuillez supprimer d'abord les risques de l'activité.");
		}
		return $this->redirect($this->generateUrl('details_processus', array('id' => $processus->getId())));
	}

	/**
	 * @QMLogger(message="Creation d'une activité")
	 * @Route("/{id}/ajout_activite", name="ajout_activite", requirements={ "id"=  "\d+"})
	 * @Route("/nouvelle_activite", name="nouvelle_activite")
	 * @Template()
	 */
	public function newAction($id = null)
	{
		$entity = new Activite();
		if ($id) {
			$processus = $this->getDoctrine()->getManager()->getRepository(Processus::class)->find($id);
			$entity->setProcessus($processus);
		}
		$form   = $this->createForm(ActiviteType::class, $entity);

		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');

		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}

	/**
	 * @Route("/{id}/ajouter_activite", name="ajouter_activite", requirements={ "id"=  "\d+"})
	 * @Route("/creer_activite", name="creer_activite")
	 * @Template("activite/add.html.twig")
	 */
	public function createAction(Request $request, $id = null)
	{
		$entity = new Activite();
		$form   = $this->createCreateForm($entity, ActiviteType::class);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
            $this->get('session')->getFlashBag()->add('success', "Activité ajoutée avec succés.");

            if ($id) {
				return $this->redirect($this->generateUrl('details_processus', array('id' => $entity->getProcessus()->getId())));
			} else {
				return $this->redirect($this->generateUrl('les_activites'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}

	/**
	 * @QMLogger(message="Modification d'une activité")
	 * @Route ("/{id}/edition_activite", name="edition_activite", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Activite')->find($id);
		$form = $this->createForm(ActiviteType::class, $entity);

		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');

		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * @Route ("/{id}/modifier_activite", name="modifier_activite", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("activite/edit.html.twig")
	 */
	public function updateAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Activite')->find($id);
		$form = $this->createCreateForm($entity, ActiviteType::class);
		if ($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if ($form->isValid()) {
				$em->persist($entity);
				$this->getDoctrine()->getRepository(RisqueMetier::class)->createQueryBuilder('rm')
					->update()
					->set('rm.processus', $entity->getProcessus()->getId())
					->where('IDENTITY(rm.activite) = :activite')->setParameter('activite', $entity->getId())
					->getQuery()->execute();
				$this->getDoctrine()->getRepository(RisqueMetier::class)->createQueryBuilder('rm')
					->update()
					->set('rm.structure', $entity->getProcessus()->getStructure()->getId())
					->where('IDENTITY(rm.activite) = :activite')->setParameter('activite', $entity->getId())
					->getQuery()->execute();
				$em->flush();
                $this->get('session')->getFlashBag()->add('success', "Activité modifiée avec succés.");
                return $this->redirect($this->generateUrl('les_activites'));
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * (non-PHPdoc)
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setFilter()
	 */
	protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request)
	{
		parent::setFilter($queryBuilder, array('a.code', 'a.libelle', 'p.libelle'), $request);
	}

	/**
	 * @param \App\Entity\Activite $entity
	 * @return array
	 */
	protected function addRowInTable($entity)
	{
		return array(
			$entity->getCode(),
			$entity->getLibelle(),
			$entity->getProcessus()->getLibelle(),
			$this->service_action->generateActionsForActivite($entity)
		);
	}



	/**
	 * @QMLogger(message="Comparaison Activités")
	 * @Route ("/{id}/compare_activite", name="compare_activite", requirements={ "id"=  "\d+"})
	 */
	public function compareAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Activite')->find($id);
		$lib = $entity->getLibelleSansCarSpecial();
		$structure = $entity->getProcessus()->getStructure()->getId();
		$same = $em->getRepository('App\Entity\Activite')->findStructureBy($structure, $lib);
		return new Response($this->renderView('activite/merge.html.twig', array(
			'activites' => $same,
		)));
	}


	/**
	 *
	 * @QMLogger(message="Supprime activites")
	 * @Route ("delete_activite/{id}", name="delete_activite", requirements={ "id"=  "\d+"})
	 * 
	 */
	public function supprimeAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('App\Entity\Activite')->find($id);
		if ($entity) {
			$ok =  $entity->setEtat(0);
			$em->flush();
			$lib = $ok->getLibelleSansCarSpecial();
			$structure = $ok->getProcessus()->getStructure()->getId();
			$sames = $em->getRepository('App\Entity\Activite')->findStructureByEtat($structure, $lib);
			$same = $sames[0];
			$ActivitesToUpdateInRisqueMetier = $em->getRepository('App\Entity\RisqueMetier')->findBy(array("activite" => $id));
			foreach ($ActivitesToUpdateInRisqueMetier as $ActiviteToUpdateInRisqueMetier) {
				$ActiviteToUpdateInRisqueMetier->setActivite($same);
			}
			$em->remove($ok);
			$em->flush();
		} else {
			throw new NotFoundHttpException("Page not found");
		}
		return $this->redirect($this->generateUrl('les_activites', array()));
	}
}
