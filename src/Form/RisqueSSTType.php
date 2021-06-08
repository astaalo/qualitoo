<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class RisqueSSTType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('risque', new RisqueType());
		$builder->add('lieu', 'entity', array(
				'label' => 'Lieu ',
				'class' => 'OrangeMainBundle:Lieu',
				'empty_value' => 'Choisir un lieu ...',
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir un lieu ...'
				)
		));
		$builder->add('manifestation', 'entity', array(
				'label' => 'MAnifestation ',
				'class' => 'OrangeMainBundle:Manifestation',
				'empty_value' => 'Choisir une manifestation ...',
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir une manifestation ...'
				)
		));
		
		$builder->add('site', 'entity', array(
				'label' => 'Site',
				'class' => 'OrangeMainBundle:Site',
				'empty_value' => 'Choisir un site ...',
				'query_builder' =>function($er){
				return $er->filter();
				},
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir un site ...',
						'widget_help' => 'Choisir un site dans la liste',
				)
		));
		$builder->add('domaineActivite', 'entity', array(
				'label' => "Domaine d'activité",
				'class' => 'OrangeMainBundle:DomaineActivite',
				'empty_value' => "Choisir un domaine d'activité ...",
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir un domaine ...',
						'widget_help' => 'Choisir un domaine dans la liste',
				)
		));
		
		$builder->add('equipement', 'entity', array(
				'label' => 'Equipement/Activité',
				'class' => 'OrangeMainBundle:Equipement',
				'empty_value' => 'Choisir un equipement/activité ...',
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir un equipement ...',
						'widget_help' => 'Choisir un equipement dans la liste',
				)
		));
	
		$builder->add('proprietaire', 'text', array('disabled'    => true,));
		
	}

	public function finishView(FormView $view, FormInterface $form, array $options) {
		$view->children['proprietaire']->vars['id'] = 'responsable';
	}

	public function setDefaultOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\RisqueSST',
				'cascade_validation'=>true,
				'allow_extra_fields' => true,
				'validation_groups' => function(FormInterface $form) {
					$groups = array('Default');
					if($form->getData()->getRisque()->getId() && $form->getData()->getRisque()->hasToBeValidated()) {
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
		return 'risque_sst';
	}
}
