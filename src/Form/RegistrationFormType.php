<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use FOS\UserBundle\Form\Type\RegistrationFormType as AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Repository\StructureRepository;
use App\Repository\SocieteRepository;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	parent::buildForm($builder, $options);
        $builder
            ->add('prenom', null, array('label' => 'Prénom', 'attr' => array('class' => 'large')))
            ->add('nom', null, array('label' => 'Nom', 'attr' => array('class' => 'smallinput')))
            ->add('roles', 'choice', array('label' => 'Profil', 'choices' => array('ROLE_USER' => 'Utilisateur simple', 'ROLE_ADMIN' => 'Administrateur'), 'multiple' => true
            		,'attr' => array('class' => 'chzn-select')))
            ->add('structure', 'entity', array('label' => 'Structure', 'attr'=>array('class'=>'chzn-select'),'class' => 'OrangeMainBundle:Structure','empty_value' => 'Choisir la structure ...',
            				'query_builder'=>function($sr){
            				return $sr->filter();
            				}))
            ->add('matricule', null, array('label' => 'Matricule', 'attr' => array('class' => 'ui-spinner-box')))
            ->add('telephone', null, array('label' => 'Téléphone'))
            ->add('manager', null, array('label' => 'Est manager', 'required' => false, 'attr' => array('class' => 'on_off_checkbox')))
            ->add('societeOfAdministrator',null, array('label' => 'Est administrateur de', 'attr'=>array('class'=>'chzn-select', 'multiple' => 'multiple'),'class' => 'OrangeMainBundle:Societe','empty_value' => 'Choisir la société ...',
            'query_builder'=>function($sr){
            return $sr->listUserSocieties();
            }
            ))
            
            ->add('societeOfAuditor',null, array('label' => 'Est auditeur de', 'attr'=>array('class'=>'chzn-select', 'multiple' => 'multiple'),'class' => 'OrangeMainBundle:Societe','empty_value' => 'Choisir la société ...',
            'query_builder'=>function($sr){
            return $sr->listUserSocieties();
            }
            ))
            ->add('societeOfRiskManager',null, array('label' => 'Est risque manager de', 'attr'=>array('class'=>'chzn-select', 'multiple' => 'multiple'),'class' => 'OrangeMainBundle:Societe','empty_value' => 'Choisir la société ...',
            'query_builder'=>function($sr){
            return $sr-> listUserSocieties();
            }
            ))
    		->add('connectWindows', null, array('label' => 'Connexion avec compte windows', 'required' => false, 'attr' => array('class' => 'on_off_checkbox')));
    }

    public function getName()
    {
        return 'orange_main_registration';
    }
}
