<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\Chargement;
use App\Annotation\QMLogger;
use App\Form\ImportType;
use App\Entity\CritereChargement;
use App\Entity\Risque;
use App\Criteria\RisqueCriteria;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\DBAL\DBALException;
use App\Entity\Rapport;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Cartographie;

/**
 * 
 * @author MK@SS
 *
 */
class ChargementController extends BaseController {
	
	const FROM = "orange@orange.sn";
	const FROM_NAME = "CORIS ARQ";
	
	/**
	 * @QMLogger(message="Liste des chargements")
	 * @Route("/les_chargements", name="les_chargements")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		
		return array();
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des chargements")
	 * @Route("/liste_des_chargements", name="liste_des_chargements")
	 * @Template()
	 */
	public function listeAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $em->getRepository('OrangeMainBundle:Chargement')->createQueryBuilder('c')->where('c.etat=1');
		return $this->paginate($request, $queryBuilder,'addRowInTableChargement');
	}
	
	/**
	 * @QMLogger(message="Débuter un chargement")
	 * @Route("/{id}/nouveau_chargement", name="nouveau_chargement")
	 * @Template()
	 */
	public function newAction( Request $request, $id) {
		$em=$this->getDoctrine()->getManager();
		$entity =new Chargement();
		$carto = $em->getRepository('OrangeMainBundle:Cartographie')->find($id);
		$entity->setCartographie($carto);
		$domaines =  $em->getRepository('OrangeMainBundle:DomaineImpact')->findBy(array('cartographie'=>$carto,'lvl'=>0));
		$CC = array();
		foreach ($domaines as $domaine){
			$object = new CritereChargement();
			$object->setChargement($entity);
			$object->setDomaine($domaine);
			$CC[] = $object;
			$entity->addCritere($object);
		}
		$form =$this->createCreateForm($entity, 'Chargement');
		if($id!=$this->getMyParameter('ids', array('carto', 'metier'))) { 
			$form->remove('direction');
			$form->remove('activite');
		}
		if($form->handleRequest($request) && $form->isValid()) {
			foreach ($CC as $c){ $entity->removeCritere($c); }
			$criteres_chosis = $request->request->get('chargement')['critere'];
			foreach ($criteres_chosis as $cr){
				$id_cr = (int) $cr['critere'];
				$critere = $em->getRepository('OrangeMainBundle:Critere')->find($id_cr);
				$object = new CritereChargement();
				$object->setChargement($entity);
				$object->setDomaine($critere->getDomaine());
				$object->setCritere($critere);
				$entity->addCritere($object);
			}
			$entity->setUtilisateur($this->getUser());
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add('success', "Vous pouvez charger les risques.");
			return $this->redirect($this->generateUrl('importer_les_risques', array('id'=>$entity->getId())));
		}
		return array('form'=>$form->createView(), 'id'=>$id);
	}
	
	/**
	 * @QMLogger(message="Envoie du formulaire de chargement")
	 * @Route("/{id}/creer_chargement", name="creer_chargement")
	 * @Template("OrangeMainBundle:Chargement:new.html.twig")
	 */
	public function createAction(Request $request,$id) {
		$em=$this->getDoctrine()->getEntityManager();
		$entity = new Chargement();
		$carto = $em->getRepository('OrangeMainBundle:Cartographie')->find($id);
		$domaines =  $em->getRepository('OrangeMainBundle:DomaineImpact')->findBy(array('cartographie'=>$carto,'lvl'=>0));
		foreach ($domaines as $domaine){
			$object = new CritereChargement();
			$object->setChargement($entity);
			$object->setDomaine($domaine);
			$entity->addCritere($object);
		}
		$entity->setCartographie($carto);
		$form =$this->createCreateForm($entity, 'Chargement');
		if($id!=$this->getMyParameter('ids', array('carto', 'metier'))){
			$form->remove('direction');
		}
		$form->handleRequest($request);
		if($form->isValid()) {
			$entity->setUtilisateur($this->getUser());
		    $em->persist($entity);
		    $em->flush();
		    $this->get('session')->getFlashBag()->add('success', "Vous pouvez charger les risques.");
		    return $this->redirect($this->generateUrl('importer_les_risques', array('id'=>$entity->getId())));
	    }
		return array('form'=>$form->createView(),'id'=>$id);
	}
	
	/**
	 * @QMLogger(message="Importer les risques")
	 * @Route("/{id}/importer_les_risques", name="importer_les_risques")
	 * @method({"GET","POST"})
	 * @Template()
	 */
	public function importAction(Request $request,$id){
		$em = $this->getDoctrine()->getEntityManager();
		$form = $this->createForm(new ImportType());
		$chargement = $em->getRepository('OrangeMainBundle:Chargement')->find($id);
		if(!$chargement) {
		   throw new EntityNotFoundException("Chargement inexistant.");
		}
		if($request->getMethod()=='POST') {
			$form->handleRequest($request);
			if($form->isValid()) {
				try {
					$data = $form->getData();
					$number = $this->get('orange.main.loader')->loadRisque($data['file'], $this->getUser(), $chargement);
					$em->persist($chargement->generateRapport());
					$em->flush();
					$to = $this->getUser()->getEmail();
					$list_managers = $this->getUser()->getSociete()->getRiskManager();
					$cc = array();
					for($i=0; $i<count($list_managers); $i++) {
						$cc[] = $list_managers[$i]->getEmail();
					}
					$bcc = array("mohamed.sall@orange-sonatel.com", "madiagne.sylla@orange-sonatel.com");
					$mail = (\Swift_Message::newInstance());
					$mail->setFrom(array(self::FROM => self::FROM_NAME))
						 ->setTo($to)
						 ->setCc($cc)
						 ->setBcc($bcc)
						 ->setBody($this->renderView('OrangeMainBundle:Chargement:sendMailRapport.html.twig', 
						 		array('chargement' => $chargement)) ,'text/html');
					$this->get('mailer')->send($mail);
					$this->get('session')->getFlashBag()->add('success', "Le chargement s'est déroulé avec succés!.");
					return $this->redirect($this->generateUrl('les_risques_importes', array('id'=>$chargement->getId())));
				} catch (DBALException $e) {
					$this->get('session')->set('erreurs_chargement', $e->getMessage());
					$this->get('session')->getFlashBag()->add('error', "Des erreurs se sont produites au chargement!");
					return array('form'=>$form->createView(), 'id'=>$id, 'chargement'=>$chargement);
				}
			}
		} else {
			$this->get('session')->set('erreurs_chargement', null);
		}
		return array('form'=>$form->createView(), 'id'=>$id, 'chargement'=>$chargement);
	}
	
	/**
	 * @QMLogger(message="Liste des  risques importés")
	 * @Route("/{id}/les_risques_importes", name="les_risques_importes")
	 * @Template()
	 */
	public function indexImportAction($id) {
		$entity=new Risque();
		$em=$this->getDoctrine()->getEntityManager();
		$chargement = $em->getRepository('OrangeMainBundle:Chargement')->find($id);
		$this->get('session')->set('risque_criteria', array('cartographie' => $chargement->getCartographie()->getId()));
		$position=$this->get('session')->get('risque_criteria')['cartographie'];
		$data = $this->get('session')->get('risque_criteria');
		$form = $this->createForm(new RisqueCriteria(), new Risque(), array('attr' => array('em' => $this->getDoctrine()->getManager())));
		$this->modifyRequestForForm($this->get('request'), $data, $form);
		$entity->setCartographie($form->getData()->getCartographie());
		$this->denyAccessUnlessGranted('read', $entity,'Accés non autorisé!');
		return array('form' => $form->createView(),'position'=>intval($position) ,'id_import'=>$id,'rapport'=>$chargement->getRapport());
	}
	
	/**
	 * @QMLogger(message="Chargement ajax des risques importés")
	 * @Route("/{id}/liste_des_risques_importer", name="liste_des_risques_importer")
	 * @Template()
	 */
	public function listImportAction(Request $request,$id) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new RisqueCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('risque_criteria'), $form);
		$queryBuilder = $em->getRepository('OrangeMainBundle:Risque')->listByImport($form->getData(), $id);
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Supprimer les risques importés")
	 * @Route("/{id}/supprimer_risques_importes", name="supprimer_risques_importes")
	 * @Template()
	 */
	public function deleteRisquesImportesAction(Request $request, $id) {
		$em=$this->getDoctrine()->getEntityManager();
		$chargement = $em->getRepository('OrangeMainBundle:Chargement')->find($id);
		if($chargement == null)
			$this->createNotFoundException("Ce chargement n'existe pas!");
		if($request->getMethod()=='POST') {
			$les_risques = $chargement->getRisque();
			foreach ($les_risques as $r) {
				$em->remove($r);
			}
			$em->remove($chargement);
			$em->flush();
			$this->get('session')->getFlashBag()->add('success', "Le chargement a été supprimé avec succés!.");
			return $this->redirect($this->generateUrl('les_risques'));
		}
		return new Response($this->renderView('OrangeMainBundle:Chargement:deleteRisquesImportes.html.twig', array('entity' => $chargement)));
	}
	
	/**
	 * @QMLogger(message="Affichage erreur du chargement")
	 * @Route("/erreur_chargement", name="erreur_chargement")
	 * @Template()
	 */
	public function showErreurAction() {
		$erreurs=$this->get('session')->get('erreurs_chargement');
		$erreurs =unserialize($erreurs);
		return array('erreurs'=>$erreurs);
	}
	/**
	 * @Route("/menu_chargement_risque", name="menu_chargement_risque")
	 * @Template("OrangeMainBundle:Chargement:menu_load_risque.html.twig")
	 */
	public function menuAction() {
		return array();
	}
	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\Risque $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
		if($entity->getCartographie()->getId()<=2)
			return array(
					$entity->getCode(),
					$entity->isPhysical()==false?($entity->getRisqueData()?($entity->getRisqueData()->getProcessus()?$entity->getRisqueData()->getProcessus()->__toString():''):''):'',
					$entity->getStructreOrSite()?$entity->getStructreOrSite()->__toString():'',
					$entity->getActivite()?$entity->getActivite()->__toString():'',
					$entity->getMenace() ? $entity->getMenace()->__toString() : 'Non renseigné',
					'<a class="actionLink"  href="#myModal" data-toggle="modal" data-target="#myModal" modal-url="'.$this->generateUrl('cause_of_risque', array('id' => $entity->getId())).'">'.$entity->getCauseOfRisque()->count().'</a>',
					$entity->getDateSaisie()?$entity->getDateSaisie()->format('d/m/Y'):'',
					$this->get('orange.main.actions')->generateActionsForRisque($entity)
			);
			else
				return array(
						$entity->getCode(),
						$entity->getStructreOrSite()?$entity->getStructreOrSite()->__toString():'',
						$entity->getActivite()?$entity->getActivite()->__toString():'',
						$entity->getMenace() ? $entity->getMenace()->__toString() : 'Non renseigné',
						'<a class="actionLink"  href="#myModal" data-toggle="modal" data-target="#myModal" modal-url="'.$this->generateUrl('cause_of_risque', array('id' => $entity->getId())).'">'.$entity->getCauseOfRisque()->count().'</a>',
						//$entity->getUtilisateur() ? $entity->getUtilisateur()->__toString() : null,
						$entity->getDateSaisie()?$entity->getDateSaisie()->format('d/m/Y'):'',
						$this->get('orange.main.actions')->generateActionsForRisque($entity)
				);
	}
	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param \App\Entity\Chargement $entity
	 * @return array
	 */
	protected function addRowInTableChargement($entity) {
		return array(
				$entity->getLibelle(),
				$entity->getDate()?$entity->getDate()->format('d/m/Y'):'',
				$entity->getUtilisateur()?$entity->getUtilisateur()->__toString():'',
				$this->get('orange.main.actions')->generateActionsForChargement($entity)
		);
	}
}