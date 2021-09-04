<?php
namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class RisqueEnvironnementalType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('risque', RisqueType::class);
		$builder->add('lieu', EntityType::class, array(
				'label' => 'Lieu ',
				'class' => 'App\Entity\Lieu',
				'placeholder' => 'Choisir un lieu ...',
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir un lieu ...'
				)
		));
		$builder->add('manifestation', EntityType::class, array(
				'label' => 'MAnifestation ',
				'class' => 'App\Entity\Manifestation',
				'placeholder' => 'Choisir une manifestation ...',
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir une manifestation ...'
				)
		));
		$builder->add('site', EntityType::class, array(
				'label' => 'Site',
				'class' => 'App\Entity\Site',
				'placeholder' => 'Choisir un site ...',
				'query_builder' =>function($er){
					return $er->filter();
				},
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir un site ...',
						'widget_help' => 'Choisir un site dans la liste',
				)
		));
		$builder->add('domaineActivite', EntityType::class, array(
				'label' => 'Domaine d\'activite ',
				'class' => 'App\Entity\DomaineActivite',
				'placeholder' => 'Choisir un domaine d\'activité ...',
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir un domaine ...',
						'widget_help' => 'Choisir un domaine dans la liste',
				)
		));
		
		$builder->add('equipement', EntityType::class, array(
				'label' => 'Equipement/Activité',
				'class' => 'App\Entity\Equipement',
				'placeholder' => 'Choisir un equipement/activité ...',
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir un équipement ...',
						'widget_help' => 'Choisir un équipement dans la liste',
				)
		));
	$builder->add('proprietaire', TextType::class, array('disabled'    => true,));
		
	}

	public function finishView(FormView $view, FormInterface $form, array $options) {
		$view->children['proprietaire']->vars['id'] = 'responsable';
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\RisqueEnvironnemental',
				'cascade_validation'=>true,
				'allow_extra_fields' => true,
				'validation_groups' => function(FormInterface $form) {
					$groups = array('Default');
					if($form->getData()->getId() && $form->getData()->getRisque()->hasToBeValidated()) {
						$groups [] = 'RisqueValidation';
					}
					if(!$form->getData()->getRisque()->getMenace() &&($form->getData()->getRisque()->getIdentification() && !$form->getData()->getRisque()->getIdentification()->getLibelle())) {
						$groups [] = 'RisqueIdentification';
					}
					return $groups;
				}
			));
	}
	
	public function getName() {
		return 'risque_environnemental';
	}
}
