<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Entity\Risque;
use App\Entity\Notification;
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
     * @param \App\Entity\Grille $entity
     * @return string
     */
    public function generateActionsForGrille($entity)
    {
        return sprintf('<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>
             <span class="tip" ><a title="Modifier" href="%s"><img src="%s" /></a></span>', '#', $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'), '#', $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'));
    }

    /**
     * @param \App\Entity\Cause $entity
     * @return string
     */
    public function generateActionsForCause($entity)
    {
        $this->addAction("Voir les détails", $this->router->generate('details_cause', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        $this->addActionByRoles(
            array('ROLE_RISKMANAGER', 'ROLE_ADMIN'),
            "Modifier",
            $this->router->generate('edition_cause', array('id' => $entity->getCause()->getId(), 'page' => 1)),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'),
            true
        );
        return $this->getActions();
    }

    /**
     * @param \App\Entity\Impact $entity
     * @return string
     */
    public function generateActionsForImpact($entity)
    {
        $this->addAction("Voir les détails", $this->router->generate('details_impact', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        /*$this->addActionByRoles(array(
                'ROLE_RISKMANAGER'
        ), "Modifier", "#", $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'));*/
        return $this->getActions();
    }

    /**
     * @param \App\Entity\Famille $entity
     * @return string
     */
    public function generateActionsForFamille($entity)
    {
        $this->addAction("Voir les détails", $this->router->generate('details_famille', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'), true);
        $this->addActionByRoles(array(
            'ROLE_RISKMANAGER'
        ), "Modifier", $this->router->generate('edition_famille', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'));
        if ($entity->getChildren()->count() > 0 || $entity->getCause()->count() > 0) {
            $this->addActionByRoles(array(
                'ROLE_RISKMANAGER'
            ), "Supprimer", '#', $this->asset('assets/bundles/orangemain/images/icon/color_18/close.png'));
        }
        return $this->getActions();
    }

    /**
     * @param \App\Entity\DomaineImpact $entity
     * @return string
     */
    public function generateActionsForDomaineImpact($entity)
    {
        $this->addAction("Voir les détails", '#', $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        $this->addActionByRoles(array(
            'ROLE_ADMIN', 'ROLE_RISKMANAGER'
        ), "Modifier", $this->router->generate('edition_domaine_dimpact', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'), true);
        return $this->getActions();
    }

    /**
     * @param \App\Entity\DomaineActivite $entity
     * @return string
     */
    public function generateActionsForDomaineActivite($entity)
    {
        $this->addActionByRoles(array(
            'ROLE_ADMIN', 'ROLE_RISKMANAGER'
        ), "Modifier", $this->router->generate('edition_domaine_dactivite', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'), true);
        return $this->getActions();
    }

    /**
     * @param \App\Entity\DomaineSite $entity
     * @return string
     */
    public function generateActionsForDomaineSite($entity)
    {
        $this->addActionByRoles(array(
            'ROLE_ADMIN', 'ROLE_RISKMANAGER'
        ), "Modifier", $this->router->generate('edition_domaine_site', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'), true);
        return $this->getActions();
    }

    /**
     * @param \App\Entity\Structure $entity
     * @return string
     */
    public function generateActionsForStructure($entity)
    {
        /* $text = sprintf('
                <span class="tip" ><a title="Voir les détails" href="#"><img src="%s" /></a></span>
                <span class="tip" ><a title="Modifier" href="%s"><img src="%s" /></a></span>',
                $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'),
                $this->router->generate('edition_structure', array('id' => $entity->getId())),
                $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png')
            );
        return $text; */
        $info_icon = $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png');
        $pencil_icon = $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png');
        $cancel_icon = $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png');
        $id = $entity->getId();
        $links = '<span class="tip" ><a title="Voir les détails" href="%s"><img src=' . $info_icon . ' /></a></span>
             <span class="tip" ><a title="Modifier" href="%s"><img src="' . $pencil_icon . '" /></a></span>';
        $connection = $this->em->getConnection();
        $statement = $connection->prepare(
            "SELECT * FROM structure s
             WHERE s.id NOT IN (SELECT rm.structure_id FROM risque_metier rm WHERE rm.structure_id IS NOT NULL) AND
                   s.id NOT IN (SELECT rp.structure_id FROM risque_projet rp WHERE rp.structure_id IS NOT NULL) AND
                   s.id NOT IN (SELECT u.structure_id FROM utilisateur u WHERE u.structure_id IS NOT NULL) AND
                   s.id = :id"
        );
        $statement->bindValue('id', $id);
        $statement->execute();
        if ($this->user->hasRole(Utilisateur::ROLE_ADMIN) || $this->user->hasRole(Utilisateur::ROLE_RISKMANAGER)) {
            if (count($statement->fetchAll()) > 0) {
                $links .= '<span class="tip" ><a title="Supprimer" href="#myModal" class="actionLink" modal-url="%s" data-target="#myModal" data-toggle="modal"><img src="' . $cancel_icon . '" /></a></span>';
                return sprintf($links, '#', $this->router->generate('edition_structure', array('id' => $id)), $this->router->generate('suppression_structure', array('id' => $id)));
            }
        }
        return sprintf($links, '#', $this->router->generate('edition_structure', array('id' => $id)));
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

    /**
     *
     * @param \App\Entity\Activite $entity
     * @return string
     */
    public function generateActionsForActivite($entity)
    {
        $this->addAction(
            "Voir les détails",
            $this->router->generate('details_activite', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png')
        );
        $this->addActionByRoles(
            array('ROLE_RISKMANAGER'),
            "Modifier",
            $this->router->generate('edition_activite', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png')
        );
        $this->addActionByRoles(
            array('ROLE_RISKMANAGER'),
            "Supprimer",
            $this->router->generate('supprimer_activite', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png')
        );
        /*$this->addActionByRoles(array(
                'ROLE_RISKMANAGER'
        ), "Comparer", $this->router->generate('compare_activite', array(
                'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/eye.png'));*/
        return $this->getActions();
    }

    /**
     *
     * @param \App\Entity\Risque $entity
     * @return string
     */
    public function generateActionsForRisque($entity)
    {

        $this->addAction(
            "Voir les détails",
            $this->router->generate('details_risque', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png')
        );
        $this->addActionByRoles(
            array('ROLE_RISKMANAGER'),
            "Supprimer Risque",
            $this->router->generate('suppression_risque', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'),
            true
        );
        return $this->getActions();

        /*return sprintf('<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>', $this->router->generate('details_risque', array(
                'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        $this->addActionByRoles(array('ROLE_ADMIN'), "Supprimer PA", $this->router->generate('supprimer_pa', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'),true);*/
    }

    /**
     *
     * @param \App\Entity\Risque $entity
     * @return string
     */
    public function generateActionsForRejetedRisque($entity)
    {
        $this->addActionByRoles(
            array('ROLE_RISKMANAGER'),
            "Supprimer Risque",
            $this->router->generate('suppression_risque', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'),
            true
        );
        return $this->getActions();
    }

    /**
     * @param \App\Entity\Evaluation $entity
     * @return string
     */
    public function generateActionsForEvaluation($entity)
    {
        return sprintf('<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>', $this->router->generate('details_evaluation', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
    }

    /**
     *
     * @param \App\Entity\Projet $entity
     * @return string
     */
    public function generateActionsForProjet($entity)
    {
        $text = sprintf(
            '<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>',
            $this->router->generate('details_projet', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png')
        );

        if ($this->user->hasRole(Utilisateur::ROLE_ADMIN) || $this->user->hasRole(Utilisateur::ROLE_RISKMANAGER)) {
            $text .= sprintf(
                '<span class="tip"><a title="Supprimer Projet" href="#myModal" class="actionLink" modal-url="%s" data-target="#myModal" data-toggle="modal"><img src="%s" /></a></span>',
                $this->router->generate('suppression_projet', array('id' => $entity->getId())),
                $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'),
                true
            );
        }
        return $text;
    }

    /**
     *
     * @param \App\Entity\Projet $entity
     * @return string
     */
    public function generateActionsForSuiviProjet($entity)
    {
        $text = sprintf('<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>', $this->router->generate('details_projet', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        if ($entity->getEtat() != $this->states['projet']['cloture'])
            $text .= ' ' . sprintf('<span class="tip" >
                                <a title="Cloturer projet" class="icon add actionLink" href="#myModal" data-toggle="modal" data-target="#myModal" class="actionLink" modal-url="%s">
                                <img src="%s" /></a></span>', $this->router->generate('changer_statut', array(
                'id' => $entity->getId(), 'statut' => $this->states['projet']['cloture']
            )), $this->asset('assets/bundles/orangemain/images/icon/color_18/lock.png'));

        if ($entity->getEtat() != $this->states['projet']['actif'])
            $text .= sprintf('<span class="tip" >
                     <a title="Activer projet" class="icon add actionLink" href="#myModal" data-toggle="modal" data-target="#myModal" class="actionLink" modal-url="%s">
                     <img src="%s" /></a></span>', $this->router->generate('changer_statut', array(
                'id' => $entity->getId(), 'statut' => $this->states['projet']['actif']
            )), $this->asset('assets/bundles/orangemain/images/icon/color_18/lock_open.png'));

        if ($entity->getEtat() != $this->states['projet']['abandonne'])
            $text .= sprintf('<span class="tip" >
                     <a title="Abandonner projet" class="icon add actionLink" href="#myModal" data-toggle="modal" data-target="#myModal" class="actionLink" modal-url="%s">
                     <img src="%s" /></a></span>', $this->router->generate('changer_statut', array(
                'id' => $entity->getId(), 'statut' => $this->states['projet']['abandonne']
            )), $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'));

        return $text;
    }

    /**
     * @param \App\Entity\Risque $entity
     * @return string
     */
    public function generateActionsForUnvalidatedRisque($entity)
    {
        return sprintf('<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>', $this->router->generate('apercu_risque', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png')) .
            sprintf('<span class="tip" ><a title="Valider le risque" href="%s"><img src="%s" /></a></span>', $this->router->generate('validation_risque', array(
                'id' => $entity->getId()
            )), $this->asset('assets/bundles/orangemain/images/icon/color_18/checkmark2.png')) .
            sprintf('<span class="tip" ><a title="Modifier" href="%s"><img src="%s" /></a></span>', $this->router->generate('edition_risque', array(
                'id' => $entity->getId()
            )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png')).
            sprintf('<span class="tip" ><a title="Supprimer" href="#myModal" class="actionLink" modal-url="%s" data-target="#myModal" data-toggle="modal"><img src="%s" /></a></span>', $this->router->generate('suppression_risque', array(
                'id' => $entity->getId()
            )), $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'));

    }

    /**
     * @param \App\Entity\Risque $entity
     * @return string
     */
    public function generateActionsForUncompletedRisque($entity)
    {
        return sprintf('<span class="tip" ><a title="Poursuivre" href="%s"><img src="%s" /></a></span>', $this->router->generate('edition_risque', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/play.png'));
    }

    /**
     *
     * @param \App\Entity\Question $entity
     * @return string
     */
    public function generateActionsForQuestion($entity)
    {
        $this->addAction("Faire monter", $this->router->generate('changer_position', array('sens' => 'H')), $this->asset('assets/bundles/orangemain/images/icon/color_18/directional_up.png'));
        $this->addAction("Faire descendre", $this->router->generate('changer_position', array('sens' => 'B')), $this->asset('assets/bundles/orangemain/images/icon/color_18/directional_down.png'));
        $this->addAction("Voir les détails", "#", $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        $this->addActionByRoles(array('ROLE_RISKMANAGER'), "Modifier", $this->router->generate('edition_question', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'), true);
        return $this->getActions();
    }

    /**
     *
     * @param \App\Entity\Menace $entity
     * @return string
     */
    public function generateActionsForMenace($entity)
    {
        $this->addActionByRoles(array(
            'ROLE_RISKMANAGER', 'ROLE_ADMIN'
        ), "Modifier", $this->router->generate('edition_menace', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'), true);

        /*$this->addActionByRoles(array(
            'ROLE_RISKMANAGER' , 'ROLE_ADMIN'
        ), "Comparer", $this->router->generate('compare_menace', array(
                'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/eye.png'), false);*/

        return $this->getActions();
    }

    /**
     *
     * @param \App\Entity\Site $entity
     * @return string
     */
    public function generateActionsForSite($entity)
    {
        $this->addActionByRoles(
            array('ROLE_RISKMANAGER'),
            "Modifier",
            $this->router->generate('edition_site', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'),
            true
        );
        $this->addActionByRoles(
            array('ROLE_RISKMANAGER'),
            $entity->getEtat() ? 'Désactiver' : 'Activer',
            $this->router->generate('activer_desactiver_site', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/' . ($entity->getEtat() ? 'cancel' : 'checkmark2') . '.png'),
            true
        );
        return $this->getActions();
    }

    /**
     * generate actions for plan action
     * @param \App\Entity\PlanAction $entity
     * @return string
     */
    public function generateActionsForPlanAction($entity)
    {
        $this->addAction("Voir les détails", $this->router->generate('details_planaction', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
        $this->addActionByRoles(array('ROLE_ADMIN'), "Supprimer PA", $this->router->generate('supprimer_pa', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'), true);
        return $this->getActions();
    }

    /**
     * generate actions for controle
     * @param \App\Entity\Controle $entity
     * @return string
     */
    public function generateActionsForControle($entity)
    {
        $content = sprintf(
            '<span class="tip" ><a title="Voir les détails" href="%s"><img src="%s" /></a></span>',
            $this->router->generate('details_controle', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png')
        );

        if ($this->user->hasRole(Utilisateur::ROLE_ADMIN) || $this->user->hasRole(Utilisateur::ROLE_RISKMANAGER) || $this->user->hasRole(Utilisateur::ROLE_AUDITEUR) || $this->user->hasRole(Utilisateur::ROLE_RESPONSABLE)) {
            $content .= sprintf(
                '<span class="tip" ><a title="Modifier le controle" href="%s"><img src="%s" /></a></span>',
                $this->router->generate('edition_controle', array('id' => $entity->getId())),
                $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png')
            );
        }
        //      if($this->user->hasRole(Utilisateur::ROLE_ADMIN) || $this->isAuditorOfRisque($entity->getRisque())){
        //              $content .= sprintf('<span class="tip" ><a title="Supprimer le controle" href="#myModal" class="actionLink" modal-url="%s" data-target="#myModal" data-toggle="modal"><img src="%s" /></a></span>',
        //                       $this->router->generate('supprimer_controle', array('id' => $entity->getId())), $this->asset('assets/bundles/orangemain/images/icon/color_18/cancel.png'));
        //      }
        return $content;
    }

    /**
     *
     * @param Notification $entity
     * @return string
     */
    public function generateActionsForNotification($entity)
    {
        return sprintf('<span class="tip" ><a title="Détails" href="%s"><img src="%s" /></a></span>', $this->router->generate('read_notification', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png'));
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


    private function getActions()
    {
        $actions = $this->actions;
        $this->actions = null;
        return $actions;
    }

    /**
     * @param \App\Entity\Equipement $entity
     * @return string
     */
    public function generateActionsForEquipement($entity)
    {
        $this->addActionByRoles(array(
            'ROLE_ADMIN', 'ROLE_RISKMANAGER'
        ), "Modifier", $this->router->generate('edition_equipement', array(
            'id' => $entity->getId()
        )), $this->asset('assets/bundles/orangemain/images/icon/color_18/pencil.png'), true);
        return $this->getActions();
    }

    /**
     *
     * @param \App\Entity\Chargement $entity
     * @return string
     */
    public function generateActionsForChargement($entity)
    {
        return sprintf(
            '<span class="tip" ><a title="Voir les risques" href="%s"><img src="%s" /></a></span>',
            $this->router->generate('les_risques_importes', array('id' => $entity->getId())),
            $this->asset('assets/bundles/orangemain/images/icon/color_18/info.png')
        );
    }


    /**
     * 
     * @param Risque $risque
     */
    public function isAuditorOfRisque($risque)
    {
        if ($risque->getUtilisateur() && $this->user->getId() == $risque->getUtilisateur()->getId())
            return true;
        else
            return false;
    }
}
