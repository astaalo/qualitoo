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

use App\Entity\Utilisateur;
use FOS\UserBundle\Form\Type\RegistrationFormType as AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Repository\StructureRepository;
use App\Repository\SocieteRepository;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	parent::buildForm($builder, $options);
        $builder
            ->add('prenom', null, array('label' => 'Prénom', 'attr' => array('class' => 'large')))
            ->add('nom', null, array('label' => 'Nom', 'attr' => array('class' => 'smallinput')))
            ->add('roles', ChoiceType::class, array('label' => 'Profil', 'choices' => array('ROLE_USER' => 'Utilisateur simple', 'ROLE_ADMIN' => 'Administrateur'), 'multiple' => true
            		,'attr' => array('class' => 'chzn-select')))
            ->add('structure', EntityType::class, array('label' => 'Structure', 'attr'=>array('placeholder' => 'Choisir la structure ...', 'class'=>'chzn-select'),'class' => 'App\Entity\Structure',
            				'query_builder'=>function($sr){
            				return $sr->filter();
            				}))
            ->add('matricule', null, array('label' => 'Matricule', 'attr' => array('class' => 'ui-spinner-box')))
            ->add('telephone', null, array('label' => 'Téléphone'))
            ->add('manager', null, array('label' => 'Est manager', 'required' => false, 'attr' => array('class' => 'on_off_checkbox')))
            ->add('societeOfAdministrator',null, array(
                'label' => 'Est administrateur de',
                'attr'=>array(
                    'placeholder' => 'Choisir la société ...',
                    'class'=>'chzn-select', 'multiple' => 'multiple'),
                'class' => 'App\Entity\Societe',
                'query_builder'=>function($sr){
                    return $sr->listUserSocieties();
                }
            ))
            
            ->add('societeOfAuditor',null, array('label' => 'Est auditeur de', 'attr'=>array('placeholder' => 'Choisir la société ...','class'=>'chzn-select', 'multiple' => 'multiple'),'class' => 'App\Entity\Societe',
            'query_builder'=>function($sr){
            return $sr->listUserSocieties();
            }
            ))
            ->add('societeOfRiskManager',null, array('label' => 'Est risque manager de','class' => 'App\Entity\Societe', 'attr'=>array('placeholder' => 'Choisir la société ...', 'class'=>'chzn-select', 'multiple' => 'multiple'),
            'query_builder'=>function($sr){
            return $sr-> listUserSocieties();
            }
            ))
    		->add('connectWindows', null, array('label' => 'Connexion avec compte windows', 'required' => false, 'attr' => array('class' => 'on_off_checkbox')));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Utilisateur::class
        ));
    }

    public function getName()
    {
        return 'orange_main_registration';
    }
}
