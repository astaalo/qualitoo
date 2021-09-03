<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Security;

class Actions
{
    const ACTION_TEMPLATE = '<span class="tip" ><a title="%s" href="%s"><img src="%s" /></a></span>';
    const ACTION_MODAL_TEMPLATE = '<span class="tip" ><a title="%s" href="#myModal" class="actionLink" modal-url="%s" data-target="#myModal" data-toggle="modal"><img src="%s" /></a></span>';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
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
     * @var \App\Entity\Utilisateur
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
     * @param \Symfony\Component\Security\Core\Security $security_context
     */
    public function __construct($twig, $router, $states,Security $security_context, EntityManager $entitymanager)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->states = $states;
        $this->user = $security_context->getToken()->getUser();
        $this->em = $entitymanager;
    }

    /**
     *
     * @param \App\Entity\Societe $entity
     * @return string
     */
    public function generateActionsForSociete($entity)
    {
        return sprintf('<span class="tip" ><a title="Détails" href="%s"><img src="%s" /></a></span> 
         <span class="tip" ><a title="Modifier" href="%s"><img src="%s" /></a></span> 
          <span class="tip" ><a class="Delete" title="Désactiver" href="%s"><img src="%s" /></a></span> 
          <span class="tip" ><a class="Delete" title="Supprimer" href="%s"><img src="%s" /></a></span>', '#', $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'), '#', $this->asset('assets/bundles/orangemain/images/icon/
                  /pencil.png'), '#', $this->asset('assets/bundles/orangemain/images/icon/color_18/close.png'), '#', $this->asset('assets/bundles/orangemain/images/icon/color_18/cross.png'));
    }

    /**
     *
     * @param \App\Entity\Utilisateur $entity
     * @return string
     */
    public function generateActionsForUtilisateur($entity)
    {
        $render = sprintf('<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>
             <span class="tip" ><a title="Modifier" href="%s"><img src="%s" /></a></span>
             <span class="tip" ><a title="Désactiver" href="%s"><img src="%s" /></a></span>',
            $this->router->generate('details_utilisateur', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'),
            $this->router->generate('edition_utilisateur', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'),
            $this->router->generate(($entity->isEnabled() ? 'desactiver' : 'activer') . '_utilisateur', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/' . ($entity->isEnabled() ? 'cancel' : 'checkmark2') . '.png'));
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
     *
     * @param \App\Entity\Processus $entity
     * @return string
     */
    public function generateActionsForProcessus($entity)
    {
        $this->addAction("Voir les détails", $this->router->generate('details_processus', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        $this->addActionByRoles(
            array('ROLE_RISKMANAGER', 'ROLE_ADMIN'),
            "Modifier",
            $this->router->generate('edition_processus', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png')
        );
        $this->addActionByRoles(
            array('ROLE_RISKMANAGER', 'ROLE_ADMIN'),
            "Supprimer",
            $this->router->generate('supprimer_processus', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'),
            true
        );
        return $this->getActions();
    }

}
