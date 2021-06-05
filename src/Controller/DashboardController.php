<?php

namespace App\Controller;

use App\Annotation\QMLogger;
use App\Entity\TypeDocument;
use App\Repository\TypeDocumentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends BaseController
{
    /**
	 * @QMLogger(message="Page d'accueil ")
	 * @Route("/", name="dashboard")
	 * @Security("has_role('ROLE_USER')")
     * @Route("/", name="dashboard")
     * @Template()
     */
    public function indexAction(Request $request, TypeDocumentRepository $typeDocumentRepo)
    {
        $em = $this->getDoctrine()->getManager();
        $utilisateur = $this->getUser();
        if($this->getUser() && !$this->getUser()->getSociete()){
            $id = $this->getUser()->getStructure()->getSociete()->getId();
            $entite = $em->getRepository('Societe')->find($id);
            $utilisateur->setSociete($entite);
            $em->persist($utilisateur);
            $em->flush();
        }
        $gridItems = array();
        // BLOC RISQUE
        $gridItems[] = array(
            'header'	=> "Saisie",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE'),
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Saisir une fiche de risque",
                    'roles' => array(),
                    'path'	=> $this->generateUrl('dashboard')
                    //'path'	=> $this->generateUrl('menu_nouveau_risque')
                ), array(
                    'icon'	=> 'pencil.png',
                    'text'	=> "Terminer une fiche de risque",
                    'roles' => array(),
                    'path'	=> $this->generateUrl('dashboard')
                    //'path'	=> $this->generateUrl('risques_a_completer')
                ), array(
                    'icon'	=> 'arrow_up2.png',
                    'text'	=> "Charger des fiches de risque",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER'),
                    'path'	=> $this->generateUrl('dashboard')
                    //'path'	=> $this->generateUrl('menu_chargement_risque')
                )
            )
        );
        // BLOC CONTROLE ET MAITRISE
        $gridItems[] = array(
            'header'	=> "Validation",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_PORTEUR', 'ROLE_SUPERVISEUR', 'ROLE_RESPONSABLE'),
            'rows'		=> array(
                array(
                    'icon'	=> 'checkmark2.png',
                    'text'	=> "Valider une fiche de risque",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER'),
                    'path'	=> $this->generateUrl('dashboard')
                    //'path'	=> $this->generateUrl('choix_carto',array('carto' => $this->getParameter('ids')['carto']['metier'], 'link'=>'risques_a_valider'))
                ),array(
                    'icon'	=> 'usb.png',
                    'text'	=> "Transférer les risques projets",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER'),
                    'path'	=> $this->generateUrl('dashboard')
                    //'path'	=> $this->generateUrl('les_risques_a_transferer')
                )

            )
        );
        $default_carto_by_profil = ($utilisateur->hasRole('ROLE_RESPONSABLE_ONLY') && !$utilisateur->isManager()) ? $this->getParameter('ids')['carto']['sst'] : $this->getParameter('ids')['carto']['metier'];
        // BLOC Evaluation
        $gridItems[] = array(
            'header'	=> "Consultation / MAJ / Extraction",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET', 'ROLE_SUPERVISEUR', 'ROLE_PORTEUR'),
            'rows'		=> array(
                array(
                    'icon'	=> 'checkmark2.png',
                    'text'	=> "Risque valide",
                    'width' => '50%',
                    'roles' =>array('ROLE_PORTEUR'),
                    'path'	=> $this->generateUrl('dashboard')
                    //'path'	=> $this->generateUrl('choix_carto',array('carto' =>$default_carto_by_profil, 'link'=>'les_risques'))
                ), array(
                    'icon'	=> 'satellite.png',
                    'text'	=> "Contrôle",
                    'width' => '40%',
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET', 'ROLE_SUPERVISEUR', 'ROLE_PORTEUR'),
                    //'path'	=> $this->generateUrl('les_controles')
                    'path'	=> $this->generateUrl('dashboard')
                ), array(
                    'icon'	=> 'checkmark.png',
                    'text'	=> "Risque rejeté",
                    'width' => '50%',
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET'),
                    'path'	=> $this->generateUrl('dashboard')
                    //'path'	=> $this->generateUrl('choix_carto',array('carto' =>$default_carto_by_profil, 'link'=>'les_risques_rejetes'))
                ),array(
                    'icon'	=> 'hammer.png',
                    'text'	=> "Plan d'action",
                    'width' => '40%',
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET', 'ROLE_SUPERVISEUR', 'ROLE_PORTEUR'),
                    //'path'	=> $this->generateUrl('les_planactions')
                    'path'	=> $this->generateUrl('dashboard')
                ), array(
                    'icon'	=> 'stats_lines.png',
                    'text'	=> "Matrice",
                    'width' => '40%',
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET'),
                    //'path'	=> $this->generateUrl('choix_carto_kpi', array('carto'=>1, 'type'=>4, 'link'=>'la_restitution'))
                    'path'	=> $this->generateUrl('dashboard')
                )
            )
        );
        // BLOC Fonctions supplémentaires
        $gridItems[] = array(
            'header'	=> "Fonctions supplémentaires",
            'roles'		=> array(),
            'rows'		=> array()
        );

        $typeSh     = $typeDocumentRepo->findOneBy(array('code'=>TypeDocument::TYPE_TDB));
        $typeVeille = $typeDocumentRepo->findOneBy(array('code'=>TypeDocument::TYPE_VEILLE));
        // BLOC Reporting et veille
        $gridItems[] = array(
            'header'	=> "Sharepoint",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE'),
            'rows'		=> array(
                array(
                    'icon'	=> 'firewall.png',
                    'text'	=> "Tableau de bord",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE', 'ROLE_AUDITEUR'),
                    // 'path'	=> $typeSh? $this->generateUrl('choix_type',array('link'=>'documents','year'=>date('Y'), 'type'=>$typeSh->getId())):'#'
                    'path'	=> $this->generateUrl('dashboard')
                ), array(
                    'icon'	=> 'abacus.png',
                    'text'	=> "Veille",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE', 'ROLE_AUDITEUR'),
                    'path'	=> $this->generateUrl('dashboard')
                    //'path'	=> $typeVeille?$this->generateUrl('choix_type',array('link'=>'documents','year'=>date('Y'), 'type'=>$typeVeille->getId())):'#'
                ), array(
                    'icon'	=> 'calculator.png',
                    'text'	=> "Formation",
                    'roles' => array('ROLE_SUPERADMIN'),
                    'path'	=> '#'
                ), array(
                    'icon'	=> 'calculator.png',
                    'text'	=> "Divers",
                    'roles' => array('ROLE_SUPERADMIN'),
                    'path'	=> '#'
                )
            )
        );
        // BLOC Administration et Exploitation
        $gridItems[] = array(
            'header'	=> "Administration et Exploitation",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE'),
            //'rows'		=> array(),
            //'path'		=> ($this->getUser()->hasRole('ROLE_ADMIN')|| $this->getUser()->hasRole('ROLE_RISKMANAGER'))?$this->generateUrl('les_processus'):'#',
            'path'	=> $this->generateUrl('dashboard'),
            'rows'		=> array(
                array(
                    'icon'	=> 'briefcase.png',
                    'text'	=> "Suivi projet",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RESPONSABLE'),
                    // 'path'	=> $this->generateUrl('suivi_projet')
                    'path'	=> $this->generateUrl('dashboard')
                ),
                array(
                    'icon'	=> 'gear.png',
                    'text'	=> "Relances",
                    'roles' => array('ROLE_ADMIN'),
                    'path'	=> $this->generateUrl('dashboard')
                    // 'path'	=> ($this->getUser()->getSociete()&&$this->getUser()->getSociete()->getRelance())? $this->generateUrl('edition_relance',array('id' =>$this->getUser()->getSociete()->getRelance()->getId())):'#'
                ), array(
                    'icon'	=> 'add.png',
                    'text'	=> "Chargements",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER'),
                    // 'path'	=> $this->generateUrl('les_chargements')
                    'path'	=> $this->generateUrl('dashboard')
                )/*,
    					array(
    							'icon'	=> 'cutter.png',
    							'text'	=> "Tester les risques",
    							'roles' => array('ROLE_ADMIN', 'ROLE_AUDITEUR'),
    							'path'	=> $this->generateUrl('les_risques_a_tester')
    					)
    					,
    					array(
    							'icon'	=> 'screwdriver.png',
    							'text'	=> "Tester les controles",
    							'roles' => array('ROLE_ADMIN', 'ROLE_AUDITEUR'),
    							'path'	=> '#'
    					)*/
            )
        );

        return array('gridItems' => $gridItems);

    }
}
