<?php

namespace App\Service;

use App\Entity\Theme;
use App\Entity\Societe;
use App\Entity\Document;
use App\Entity\Rubrique;
use App\Entity\Structure;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Security;

class Actions
{
    const ACTION_TEMPLATE = '<span class="tip" ><a title="%s" href="%s"><img src="%s" /></a></span>';
    const ACTION_MODAL_TEMPLATE = '<span class="tip" ><a title="%s" href="#myModal" class="actionLink" modal-url="%s" data-target="#myModal" data-toggle="modal"><img src="%s" /></a></span>';

    /**
     * @var \EntityManager
     */
    protected $em;

    /**
     *
     * @var \Twig_Environment
     */
    private $twig;

    /**
     *
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     *
     * @var \Orange\MainBundle\Entity\Utilisateur
     */
    private $user;

    /**
     *
     * @var array
     */
    private $states;

    /**
     *
     * @var string
     */
    private $actions;

    /**
     *
     * @param \Twig_Environment $twig    
     * @param \Symfony\Component\Routing\Router $router  
     * @param array $states  
     * @param \Symfony\Component\Security\Core\SecurityContext $security_context     
     */
    public function __construct($twig, $router, $states, $security_context, EntityManager $entitymanager)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->states = $states;
        $this->user = $security_context->getToken()->getUser();
        $this->em = $entitymanager;
    }
    /**
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $entity  
     * @return string
     */
    public function generateActionsForUtilisateur($entity)
    {
        $render = sprintf('<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>
             <span class="tip" ><a title="Modifier" href="%s"><img src="%s" /></a></span>
             <span class="tip" ><a title="Désactiver" href="%s"><img src="%s" /></a></span>', '#', $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'), $this->router->generate('edition_utilisateur', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'), $this->router->generate(($entity->isEnabled() ? 'desactiver' : 'activer') . '_utilisateur', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/' . ($entity->isEnabled() ? 'cancel' : 'checkmark2') . '.png'));
        if ($this->user->hasRole(Utilisateur::ROLE_SUPER_ADMIN) && $entity->getId() != $this->user->getId()) {
            $render .= sprintf(
                '<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>',
                $this->router->generate('dashboard') . '?_want_to_be_this_user=' . $entity->getUsername(),
                $this->asset('assets/bundles/orangemain/images/icon/color_18/random.png')
            );
        }
        return $render;
    }

    /**
     * @param \Orange\MainBundle\Entity\Document $entity    
     * @return string
     */
    public function generateActionsForDocument($entity)
    {
        $this->addAction("Voir les détails", $this->router->generate('details_document', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        $this->addActionByRoles(array(
            'ROLE_ADMIN'),
            "Modifier",
            $this->router->generate('edition_document', array('id' => $entity->getId(), 'page' => 1)),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'),
            true
        );
        $this->addActionByRoles(array(
            'ROLE_ADMIN'
        ), "Supprimer",  
        $this->asset('assets/bundles/orangemain/images/icon/color_18/close.png'), true);
        return $this->getActions();
    }

    /**
     * @param \Orange\MainBundle\Entity\Societe $entity   
     * @return string
     */
    public function generateActionsForSocite($entity)
    {
        $this->addAction("Voir les détails", $this->router->generate('details_societe', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        $this->addActionByRoles(array(
                'ROLE_ADMIN' 
        ), "Modifier",
        $this->router->generate('edition_societe', array('id' => $entity->getId(), 'page' => 1)),
        $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'),
        true);
        return $this->getActions();
    }

    /**
     * @param \Orange\MainBundle\Entity\Structure $entity  
     * @return string
     */
    public function generateActionsForStructures($entity)
    {
        $this->addAction("Voir les détails", $this->router->generate('details_structure', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'), true);
        $this->addActionByRoles(array(
            'ROLE_ADMIN'
        ), "Modifier", $this->router->generate('edition_structure', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'));
        //if ($entity->getChildren()->count() > 0 || $entity->getCause()->count() > 0) {
            $this->addActionByRoles(array(
                'ROLE_ADMIN'
            ), "Supprimer",'#',
            //$this->router->generate('supprimer_structure', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/close.png'));
        //}
        return $this->getActions();
    }

    /**
     * @param \Orange\MainBundle\Entity\Theme $entity  
     * @return string
     */
    public function generateActionsForTheme($entity)
    {
        $this->addActionByRoles(array(
            'ROLE_ADMIN'
        ), "Modifier", $this->router->generate('edition_theme', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'), true);
        $this->addActionByRoles(array(
            'ROLE_ADMIN'
        ), "Supprimer",
        $this->asset('assets/bundles/orangemain/images/icon/color_18/close.png'), true);
        return $this->getActions();
    }

     /**
     * @param \Orange\MainBundle\Entity\Rubrique $entity  
     * @return string
     */
    public function generateActionsForRubrique($entity)
    {
        $this->addActionByRoles(array(
            'ROLE_ADMIN'
        ), "Modifier", $this->router->generate('edition_domaine_dactivite', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'), true);
        $this->addActionByRoles(array(
            'ROLE_ADMIN'
        ), "Supprimer", 
        $this->asset('assets/bundles/orangemain/images/icon/color_18/close.png'), true);
        return $this->getActions();
    }

    /**
     *
     * @param \Orange\MainBundle\Entity\Processus $entity    
     * @return string
     */
    public function generateActionsForProcessus($entity)
    {
        $this->addAction("Voir les détails", $this->router->generate('details_processus', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        $this->addActionByRoles(
            array('ROLE_ADMIN'),
            "Modifier",
            $this->router->generate('edition_processus', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png')
        );
        $this->addActionByRoles(
            array('ROLE_ADMIN'),
            "Supprimer",
            $this->router->generate('supprimer_processus', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'),
            true
        );
        return $this->getActions();
    }

    /**
     * @param string $asset
     * @return mixed
     */
    protected function asset($asset)
    {
        if (empty($this->assetFunction)) {
            $this->assetFunction = $this->twig->getFunction('asset')->getCallable();
        }
        return call_user_func($this->assetFunction, $asset);
    }

    /**
     * @param array $roles
     * @param string $title
     * @param string $url
     * @param string $icon
     * @param boolean $isModal
     */
    private function addActionByRoles($roles, $title, $url, $icon, $isModal = false)
    {
        if ($this->user->hasRoles($roles)) {
            $this->addAction($title, $url, $icon, $isModal);
        }
    }

    /**
     * @param string $title
     * @param string $url
     * @param string $icon
     * @param string $isModal
     */
    private function addAction($title, $url, $icon, $isModal = false)
    {
        if (!$this->actions) {
            $this->actions = '';
        }
        $this->actions .= sprintf($isModal ? self::ACTION_MODAL_TEMPLATE : self::ACTION_TEMPLATE, $title, $url, $icon);
    }

    /**
     * @return \Orange\MainBundle\Services\string
     */
    private function getActions()
    {
        $actions = $this->actions;
        $this->actions = null;
        return $actions;
    }
}
