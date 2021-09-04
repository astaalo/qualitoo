<?php
namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use App\Repository\StructureRepository;

class RisqueProjetType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('risque', RisqueType::class);
		$builder->add('direction', EntityType::class, array(
				'label' => 'Direction',
				'class' => 'App\Entity\Structure',
				'query_builder' => function(StructureRepository $er){
					return $er->listAllDirectionBySociete();
				},
				'placeholder' => 'Choisir la direction ...',
				//'mapped' => false,
				'attr' => array(
						'class' => 'chzn-select',
						'label_help' => 'Direction',
						'widget_help' => 'Choisir une direction dans la liste',
				)
		));
		$builder->add('structure', EntityType::class, array(
				'class' => 'App\Entity\Structure',
				'label' => 'Structure',
				'placeholder' => 'Choisir une structure ...',
				'attr' => array(
						'class' => 'chzn-select',
						'label_help' => 'Structure à laquelle l\'activité est rattachée',
						'widget_help' => 'Cliquer puis rechercher et choisir une structure dans la liste',
				),
				'query_builder'=>function(StructureRepository $sr){
				return $sr->filter();
				}
		));
		$builder->add('processus', EntityType::class, array(
				'label' => 'Processus',
				'class' => 'App\Entity\Processus',
				'placeholder' => 'Choisir un processus ...',
				'empty_data'  => null,
				'attr' => array(
						'class' => 'chzn-select',
						'label_help' => 'Processus auquel l\'activité est rattachée',
						'widget_help' => 'Choisir un processus dans la liste',
				)
		));
		
		$builder->add('proprietaire', TextType::class, array('disabled'    => true,));
		
		$builder->add('projet', EntityType::class, array(
				'label' => 'Projet',
				'class' => 'App\Entity\Projet',
				'placeholder' => 'Choisir un projet ...',
				'attr' => array(
						'class' => 'chzn-select',
						'placeholder' => 'Choisir un projet ...',
						'widget_help' => 'Choisir un projet dans la liste',
				)
		));
		
		//$builder->add('proprietaire', 'text', array('attr'=>array('disabled'=>'disabled', 'value'=>'Aucun')));
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\RisqueProjet',
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
				}
			));
	}
	public function finishView(FormView $view, FormInterface $form, array $options) {
		$view->children['proprietaire']->vars['id'] = 'responsable';
	}
	public function getName() {
		return 'risque_projet';
	}
}
