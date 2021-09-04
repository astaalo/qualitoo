<?php

namespace App\Controller;

use App\Annotation\QMLogger;
use App\Entity\Societe;
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
            $entite = $em->getRepository(Societe::class)->find($id);
            $utilisateur->setSociete($entite);
            $em->persist($utilisateur);
            $em->flush();
        }
        $gridItems = array();
        // BLOC RISQUE
        $gridItems[] = array(
            'header'	=> "Administrer Processus",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE'),
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Ajout Processus",
                    'roles' => array(),
                    'path'	=> $this->generateUrl('nouveau_processus')
                ), array(
                    'icon'	=> 'list.png',
                    'text'	=> "Liste Processus",
                    'roles' => array(),
                    'path'	=> $this->generateUrl('les_processus')
                )/*, array(
                    'icon'	=> 'arrow_up2.png',
                    'text'	=> "Charger des fiches de risque",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER'),
                    'path'	=> $this->generateUrl('suivi_projet')
                )*/
            )
        );
        // BLOC CONTROLE ET MAITRISE
        //$typeSh     = $typeDocumentRepo->findOneBy(array('code'=>TypeDocument::TYPE_TDB));
        $gridItems[] = array(
            'header'	=> "Administrer Les Documents",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_PORTEUR', 'ROLE_SUPERVISEUR', 'ROLE_RESPONSABLE'),
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Ajout Document",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER'),
                    'path'	=> $this->generateUrl('nouveau_processus')
                ),array(
                    'icon'	=> 'list.png',
                    'text'	=> "Liste Document",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER'),
                    'path'	=> $this->generateUrl('les_processus')
                    //'path'	=> $typeSh? $this->generateUrl('choix_type',array('link'=>'documents','year'=>date('Y'), 'type'=>$typeSh->getId())):'#'
                )

            )
        );
        $default_carto_by_profil = ($utilisateur->hasRole('ROLE_RESPONSABLE_ONLY') && !$utilisateur->isManager()) ? $this->getParameter('ids')['carto']['sst'] : $this->getParameter('ids')['carto']['metier'];
        // BLOC Evaluation
        $gridItems[] = array(
            'header'	=> "Administrer Les Utilisateurs",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET', 'ROLE_SUPERVISEUR', 'ROLE_PORTEUR'),
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Ajout Utilisateur",
                    'width' => '50%',
                    'roles' =>array('ROLE_PORTEUR'),
                    'path'	=> $this->generateUrl('choix_carto',array('carto' =>$default_carto_by_profil, 'link'=>'nouveau_processus'))
                ), array(
                    'icon'	=> 'list.png',
                    'text'	=> "Liste Utilisateur",
                    'width' => '40%',
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET', 'ROLE_SUPERVISEUR', 'ROLE_PORTEUR'),
                    'path'	=> $this->generateUrl('les_processus')
                ), array(
                    'icon'	=> 'add.png',
                    'text'	=> "Recherche Utilisateur",
                    'width' => '50%',
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET'),
                    'path'	=> $this->generateUrl('choix_carto',array('carto' =>$default_carto_by_profil, 'link'=>'les_processus'))
                ),array(
                    'icon'	=> 'stats_lines.png',
                    'text'	=> "Statistiques",
                    'width' => '40%',
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET', 'ROLE_SUPERVISEUR', 'ROLE_PORTEUR'),
                    'path'	=> $this->generateUrl('les_processus')
                )/*, array(
                    'icon'	=> 'stats_lines.png',
                    'text'	=> "Matrice",
                    'width' => '40%',
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE', 'ROLE_CHEFPROJET'),
                    'path'	=> $this->generateUrl('choix_carto_kpi', array('carto'=>1, 'type'=>4, 'link'=>'la_restitution'))
                )*/
            )
        );
        // BLOC Fonctions supplémentaires
        //$gridItems[] = array(
          //  'header'	=> "Fonctions supplémentaires",
         //   'roles'		=> array(),
         //   'rows'		=> array()
       // );

        //$typeSh     = $typeDocumentRepo->findOneBy(array('code'=>TypeDocument::TYPE_TDB));
        //$typeVeille = $typeDocumentRepo->findOneBy(array('code'=>TypeDocument::TYPE_VEILLE));
        // BLOC Reporting et veille
        $gridItems[] = array(
            'header'	=> "Administrer Entités",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_AUDITEUR', 'ROLE_RESPONSABLE'),
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Ajout Entités",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE', 'ROLE_AUDITEUR'),
                    'path'	=> $this->generateUrl('nouveau_processus')
                ), array(
                    'icon'	=> 'list.png',
                    'text'	=> "Liste Entités",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE', 'ROLE_AUDITEUR'),
                    'path'	=> $this->generateUrl('les_processus')
                )/*, array(
                    'icon'	=> 'calculator.png',
                    'text'	=> "Formation",
                    'roles' => array('ROLE_SUPERADMIN'),
                    'path'	=> '#'
                ), array(
                    'icon'	=> 'calculator.png',
                    'text'	=> "Divers",
                    'roles' => array('ROLE_SUPERADMIN'),
                    'path'	=> '#'
                )*/
            )
        );
        // BLOC Administration et Exploitation
        $gridItems[] = array(
            'header'	=> "Administrer Les Societes",
            'roles'		=> array('ROLE_ADMIN', 'ROLE_RISKMANAGER', 'ROLE_RESPONSABLE'),
            //'rows'		=> array(),
            'path'		=> ($this->getUser()->hasRole('ROLE_ADMIN')|| $this->getUser()->hasRole('ROLE_RISKMANAGER'))?$this->generateUrl('les_processus'):'#',
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Ajout Société",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RESPONSABLE'),
                    'path'	=> $this->generateUrl('nouveau_processus')
                ),
                array(
                    'icon'	=> 'list.png',
                    'text'	=> "Liste Société",
                    'roles' => array('ROLE_ADMIN'),
                    'path'	=> $this->generateUrl('les_societes')
                    // 'path'	=> ($this->getUser()->getSociete()&&$this->getUser()->getSociete()->getRelance())? $this->generateUrl('edition_relance',array('id' =>$this->getUser()->getSociete()->getRelance()->getId())):'#'
                ),/* array(
                    'icon'	=> 'add.png',
                    'text'	=> "Chargements",
                    'roles' => array('ROLE_ADMIN', 'ROLE_RISKMANAGER'),
                    'path'	=> $this->generateUrl('les_chargements')
                ),
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
