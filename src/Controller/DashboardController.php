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
        // BLOC PROCESSUS
        $gridItems[] = array(
            'header'	=> "Processus",
            'roles'		=> array('ROLE_SUPER_ADMIN', 'ROLE_RESPONSABLE'),
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
                )
            )
        );
        // BLOC DOCUMENTS
        //$typeSh     = $typeDocumentRepo->findOneBy(array('code'=>TypeDocument::TYPE_TDB));
        $gridItems[] = array(
            'header'	=> "Documents",
            'roles'		=> array('ROLE_SUPER_ADMIN','ROLE_RESPONSABLE'),
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Ajout Document",
                    'roles' => array('ROLE_SUPER_ADMIN'),
                    'path'	=> $this->generateUrl('nouveau_processus')
                ),array(
                    'icon'	=> 'list.png',
                    'text'	=> "Liste Document",
                    'roles' => array('ROLE_SUPER_ADMIN'),
                    'path'	=> $this->generateUrl('les_processus')
                    //'path'	=> $typeSh? $this->generateUrl('choix_type',array('link'=>'documents','year'=>date('Y'), 'type'=>$typeSh->getId())):'#'
                )

            )
        );
        // BLOC ENTITES OU STRUCTURES
        $gridItems[] = array(
            'header'	=> "Entités",
            'roles'		=> array('ROLE_SUPER_ADMIN', 'ROLE_RESPONSABLE'),
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Ajout Entités",
                    'roles' => array('ROLE_SUPER_ADMIN','ROLE_RESPONSABLE'),
                    'path'	=> $this->generateUrl('creer_structure')
                ), array(
                    'icon'	=> 'list.png',
                    'text'	=> "Liste Entités",
                    'roles' => array('ROLE_SUPER_ADMIN','ROLE_RESPONSABLE'),
                    'path'	=> $this->generateUrl('les_structures')
                )
            )
        );
        // BLOC SOCIETES 
        $gridItems[] = array(
            'header'	=> "Societes",
            'roles'		=> array('ROLE_SUPER_ADMIN', 'ROLE_RESPONSABLE'),
            //'rows'		=> array(),
            'path'		=> ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')|| $this->getUser()->hasRole('ROLE_RISKMANAGER'))?$this->generateUrl('les_processus'):'#',
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Ajout Société",
                    'roles' => array('ROLE_SUPER_ADMIN', 'ROLE_RESPONSABLE'),
                    'path'	=> $this->generateUrl('creer_societe')
                ),
                array(
                    'icon'	=> 'list.png',
                    'text'	=> "Liste Société",
                    'roles' => array('ROLE_SUPER_ADMIN'),
                    'path'	=> $this->generateUrl('les_societes')
                    // 'path'	=> ($this->getUser()->getSociete()&&$this->getUser()->getSociete()->getRelance())? $this->generateUrl('edition_relance',array('id' =>$this->getUser()->getSociete()->getRelance()->getId())):'#'
                ),
            )
        );
        // BLOC UTILISATEURS 
        $gridItems[] = array(
            'header'	=> "Utilisateurs",
            'roles'		=> array('ROLE_SUPER_ADMIN', 'ROLE_RESPONSABLE'),
            //'rows'		=> array(),
            'path'		=> ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')|| $this->getUser()->hasRole('ROLE_RISKMANAGER'))?$this->generateUrl('les_processus'):'#',
            'rows'		=> array(
                array(
                    'icon'	=> 'add.png',
                    'text'	=> "Ajout Utilisateur",
                    'roles' => array('ROLE_SUPER_ADMIN', 'ROLE_RESPONSABLE'),
                    'path'	=> $this->generateUrl('creer_societe')
                ),
                array(
                    'icon'	=> 'list.png',
                    'text'	=> "Liste Utilisateur",
                    'roles' => array('ROLE_SUPER_ADMIN'),
                    'path'	=> $this->generateUrl('les_societes')
                    // 'path'	=> ($this->getUser()->getSociete()&&$this->getUser()->getSociete()->getRelance())? $this->generateUrl('edition_relance',array('id' =>$this->getUser()->getSociete()->getRelance()->getId())):'#'
                ),
            )
        );
        return array('gridItems' => $gridItems);

    }
}
