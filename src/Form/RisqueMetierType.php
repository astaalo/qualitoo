<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use App\Repository\StructureRepository;

class RisqueMetierType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('risque', new RisqueType());
		$builder->add('structure', 'entity', array(
				'class' => 'OrangeMainBundle:Structure',
				'label' => 'Structure',
				'validation_groups' => array('RisqueValidation'),
				'empty_value' => 'Choisir une structure ...',
				'query_builder'=>function(StructureRepository $sr){
					          return $sr->filter();
					 },
				'attr' => array(
						'class' => 'chzn-select',
						'label_help' => 'Structure à laquelle l\'activité est rattachée',
						'widget_help' => 'Cliquer puis rechercher et choisir une structure dans la liste',
				)
		));
		$builder->add('direction', 'entity', array(
				'label' => 'Direction', 'class' => 'OrangeMainBundle:Structure',
				'query_builder' => function(StructureRepository $er){
							return $er->listAllDirectionBySociete();
						}, 'attr' => array(
							'class' => 'chzn-select', 'label_help' => 'Direction', 'widget_help' => 'Choisir une direction dans la liste'
					), 'empty_value' => 'Choisir la direction ...'
		));
		$builder->add('processus', 'entity', array(
				'label' => 'Processus',
				'class' => 'OrangeMainBundle:Processus',
				'empty_value' => 'Chosir un processus ...',
				'empty_data'  => null,
				'attr' => array(
						'class' => 'chzn-select',
						'label_help' => 'Processus auquel l\'activité est rattachée',
						'widget_help' => 'Choisir un processus dans la liste',
				)
		));
		
		$builder->add('proprietaire', 'text', array('disabled'    => true,));
		$builder->add('activite', 'entity', array(
				'label' => 'Activité',
				'class' => 'OrangeMainBundle:Activite',
				'empty_value' => 'Choisir une activité ...',
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir une activité ...',
						'widget_help' => 'Choisir une activité dans la liste',
				)
		));
	}
	
	public function finishView(FormView $view, FormInterface $form, array $options) {
		$view->children['proprietaire']->vars['id'] = 'responsable';
	}
	
	public function setDefaultOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\RisqueMetier',
				'cascade_validation'=>true, 
				'validation_groups' => function(FormInterface $form) {
					$groups = array('Default');
					if($form->getData()->getId() && $form->getData()->getRisque()->hasToBeValidated()) {
						$groups [] = 'RisqueValidation';
					}
					if(!$form->getData()->getRisque()->getMenace() &&($form->getData()->getRisque()->getIdentification() && !$form->getData()->getRisque()->getIdentification()->getLibelle())) {
						$groups [] = 'RisqueIdentification';
					}
					return $groups;
				},
			));
	}
	
	public function getName() {
		return 'risque_metier';
	}
}
