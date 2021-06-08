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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\StructureRepository;
use App\Repository\SocieteRepository;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UtilisateurFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('email', 'email', array('label' => "Adresse e-mail"))
      		->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('prenom', null, array('label' => 'Prénom'))
            ->add('nom', null, array('label' => 'Nom'))
            ->add('structure', 'entity', array('label' => 'Structure', 'class' => 'OrangeMainBundle:Structure',
				'query_builder'=>function($sr){
				return $sr->filter();
				}))
            ->add('matricule', 'text', array('label' => 'Matricule', 'attr' => array('class' => 'ui-spinner-box')))
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
            ->add('structureOfConsulteur', null, array('label' => 'Est consulteur de', 'required' => false, 'attr' => array('class' => 'chzn-select', 'multiple' => 'multiple'),'class' => 'OrangeMainBundle:Structure','empty_value' => 'Choisir ...', 'query_builder'=>function($sr){
                return $sr-> listUserStructure();
                }
            ))
            ->add('roles', 'choice', array('label' => 'Profil', 'choices' => array('ROLE_USER' => 'Utilisateur simple', 'ROLE_ADMIN' => 'Administrateur'), 'multiple' => true, 'required' => true, 'empty_value' => 'Choisir ...'));
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Utilisateur',
            'intention'  => 'profile',
        ));
    }

    public function getName()
    {
        return 'fos_user_profile';
    }


}
