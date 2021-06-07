<?php
namespace App\Controller;

use App\Annotation\QMLogger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProcessusType;
use App\Entity\Processus;
use App\Repository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Activite;
use Doctrine\ORM\QueryBuilder;
use App\Query\BaseQuery;

class ProcessusController extends BaseController {

	/**
	 * @QMLogger(message="Affichage des processus")
	 * @Route("/les_processus", name="les_processus")
	 * @Template()
	 */
	public function indexAction() {
		$entity= new Processus();
		$this->denyAccessUnlessGranted('read', $entity, 'Accés non autorisé');
		if(!$this->get('session')->get('processus_criteria')) {
			$this->get('session')->set('processus_criteria', array());
		}
		return array();
	}
	
	/**
	 * @QMLogger(message="Filtre sur les processus")
	 * @Route("/filtrer_les_processus", name="filtrer_les_processus")
	 * @Template()
	 */
	public function filterAction(Request $request) {
		$form = $this->createForm(new ProcessusType());
		if($request->getMethod()=='POST') {
			$this->get('session')->set('processus_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('processus_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des processus")
	 * @Route("/liste_des_processus", name="liste_des_processus")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new ProcessusType());
		$this->modifyRequestForForm($request, $this->get('session')->get('processus_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository('OrangeMainBundle:Processus')->listAllQueryBuilder($criteria);
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Affichage d'un processus")
	 * @Route("/{id}/details_processus", name="details_processus", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function showAction($id){
		$em = $this->getDoctrine()->getManager();
		$processus = $em->getRepository('OrangeMainBundle:Processus')->find($id);
		$this->denyAccessUnlessGranted('read', $processus, 'Accés non autorisé');
		return array('entitie' => $processus);
	}
	
	/**
	 * @QMLogger(message="Suppression d'un processus")
	 * @Route("/{id}/supprimer_processus", name="supprimer_processus", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function deleteAction(Request $request, $id){
		$em = $this->getDoctrine()->getManager();
		$processus = $em->getRepository('OrangeMainBundle:Processus')->find($id);
		$activites=$processus->getActivite();
		if (! $processus) {
			throw $this->createNotFoundException('Aucun processus trouvé pour cet id : ' . $id);
		}
		
		$this->denyAccessUnlessGranted('delete', $processus, 'Accés non autorisé');
		if ($request->getMethod () == 'POST') {
			if(count($activites)) {
				$this->get('session')->getFlashBag()->add('error', "Le processus ne peut pas etre supprimé du fait qu'il y a des activités.");
			} else {
				$em->remove($processus);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "Le processus a été supprimé avec succés.");
			}
			return $this->redirect($this->generateUrl('les_processus'));
		}
		return array ('entity' => $processus);
	}

	/**
	 * @QMLogger(message="Creation d'un processus")
	 * @Route("/{id}/ajout_processus", name="ajout_processus", requirements={ "id"=  "\d+"})
	 * @Route("/nouveau_processus", name="nouveau_processus")
	 * @Template()
	 */
	public function newAction($id = null) {
		$entity = new Processus();
		if($id) {
			$processus = $this->getDoctrine()->getManager()->getRepository('OrangeMainBundle:Processus')->find($id);
			$entity->setParent($processus);
		}
		$form   = $this->createCreateForm($entity, 'Processus');
		$this->denyAccessUnlessGranted('create', $entity, 'Accés non autorisé');
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la creation d'un processus")
	 * @Route("/{id}/ajouter_processus", name="ajouter_processus")
	 * @Route("/creer_processus", name="creer_processus")
	 * @Template("OrangeMainBundle:Processus:new.html.twig")
	 */
	public function createAction(Request $request, $id = null) {
		$entity = new Processus();
		$form   = $this->createCreateForm($entity, 'Processus');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			if($entity->getParent()) {
				return $this->redirect($this->generateUrl('details_processus', array('id' => $entity->getParent()->getId())));
			} else{
				return $this->redirect($this->generateUrl('les_processus'));
			}	
		}
		return array('entity' => $entity, 'form' => $form->createView(), 'id' => $id);
	}
	
	/**
	 * @QMLogger(message="Modification d'un processus")
	 * @Route ("/{id}/edition_processus", name="edition_processus", requirements={ "id"=  "\d+"})
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Processus')->find($id);
		$form = $this->createCreateForm($entity, 'Processus');
		$this->denyAccessUnlessGranted('update', $entity, 'Accés non autorisé');
		return array('entity' => $entity, 'form' => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des donnees saisies lors de la modification d'un processus")
	 * @Route ("/{id}/modifier_processus", name="modifier_processus", requirements={ "id"=  "\d+"})
	 * @Method("POST")
	 * @Template("OrangeMainBundle:Processus:edit.html.twig")
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Processus')->find($id);
		$form = $this->createCreateForm($entity, 'Processus');
		$form->bind($this->get('request'));
		if ($form->isValid()) {
			$em->persist($entity);
			$lib_sans_spec = $this->replaceSpecialChars($entity->getLibelle());
			$this->getDoctrine()->getRepository('OrangeMainBundle:Processus')->createQueryBuilder('p')
				 ->update()->set('p.libelleSansCarSpecial', ':lib')
				 ->where('p.id = :id')
				 ->setParameter('id', $entity->getId())
				 ->setParameter('lib', $lib_sans_spec)
				 ->getQuery()->execute();
			$this->getDoctrine()->getRepository('OrangeMainBundle:RisqueMetier')->createQueryBuilder('rm')
				->update()
				->set('rm.structure', $entity->getStructure()->getId())
				->where('IDENTITY(rm.processus) = :processus')->setParameter('processus', $entity->getId())
				->getQuery()->execute();
			$this->getDoctrine()->getRepository('OrangeMainBundle:RisqueProjet')->createQueryBuilder('rp')
				->update()
				->set('rp.structure', $entity->getStructure()->getId())
				->where('IDENTITY(rp.processus) = :processus')->setParameter('processus', $entity->getId())
				->getQuery()->execute();
			$em->flush();
			$this->get('session')->getFlashBag()->add('success', "Le ".$entity->getTypeProcessus()->getLibelle()." a été modifé avec succés.");
			return $this->redirect($this->generateUrl('les_processus'));
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}
	  
	/**
	 * (non-PHPdoc)
	 * @see \Orange\QuickMakingBundle\Controller\BaseController::setFilter()
	 */
	protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
		parent::setFilter($queryBuilder, array('p.code', 'p.libelle'), $request);
	}
	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\Processus $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
	  	return array(
			$entity->getCode(),
			$entity->getLibelle(),
			$entity->getTypeProcessus()->getLibelle(),
			$entity->getStructure() ? $entity->getStructure()->getName() : null,
			$this->get('orange.main.actions')->generateActionsForProcessus($entity)
		);
	}
}
