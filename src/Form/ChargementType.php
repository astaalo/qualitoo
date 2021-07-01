<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use App\Repository\StructureRepository;

class ChargementType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('libelle', null, array('label' => 'Lbelle du chargement:', 'attr' => array('class' => 'medium')))
		    ->add('direction', 'entity', array('class' => 'OrangeMainBundle:Structure', 'empty_value' => 'Choisir la direction ...',
					'label' => 'Direction', 'query_builder' => function(StructureRepository $er) {
						return $er->listAllDirectionBySociete();
					}, 'attr' => array('class' => 'chzn-select', 'label_help' => 'Direction', 'widget_help' => 'Choisir une direction dans la liste')
				))
		    ->add('projet', 'entity', array('class' => 'OrangeMainBundle:Projet', 'empty_value' => 'Choisir le projet ...',
					'label' => 'Projet', 'query_builder' => function($er) {
						return $er->listAllQueryBuilder();
					}, 'attr' => array('class' => 'chzn-select', 'label_help' => 'Projet', 'widget_help' => 'Choisir un projet dans la liste')
				))
			->add('activite', 'entity', array('class' => 'OrangeMainBundle:Activite', 'empty_value' => 'Choisir une activité ...', 'label' => 'Activité', 		
					'attr' => array('class' => 'chzn-select', 'placeholder' => 'Choisir une activité ...', 'widget_help' => 'Choisir une activité dans la liste')
				))
			->add('critere','collection', array('label'=>"Critere", 'by_reference'=>false, 'cascade_validation'=>true, 'type'=>new CritereChargementType()));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Chargement',
			'cascade_validation'=>true,
			'validation_groups' => function(FormInterface $form) {
					$groups = array('Default');
					if($form->getData()->getCartographie()->getId()==1) {
						$groups[] = 'ValideMetier';
					} elseif( $form->getData()->getCartographie()->getId()==2) {
						$groups[] = 'ValideProjet';
					}
					return $groups;
				}
			));
	}
	
	public function getName()
	{
		return 'chargement';
	}
}
