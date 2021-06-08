<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Risque;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class RisqueType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('toTransferts', 'text', array('label'=> 'selection'));
		$builder->add('maturiteReel', 'text', array('label'=> 'Maturité Réel:'));
		$builder->add('menace', null, array(
				'label' => 'Nom du risque', 'empty_value' => 'Choisir un risque ...', 'attr' => array(
						'label_help' => 'Nom pour identifier le risque', 'class' => 'chzn-select',
						'widget_help' => 'Choisir le nom du risque dans la liste ci-dessus', 'extended_entity' => true
					)
			));
		
		$builder->add('identification', new IdentificationType());
		$builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
			$carto = $event->getData() ? $event->getData()->getCartographie()->getId() : null;
			if($carto==3) {
				$label_help='Définir les dangers liées au risque';
			} elseif($carto==4) {
				$label_help='Définir les aspects liées au risque';
			} else {
				$label_help='Définir les causes liées au risque';
			}
			$event->getForm()->add('causeOfRisque', 'collection', array(
				'type' => new RisqueHasCauseType(), 'allow_add' => true, 'allow_delete' => true,  'by_reference'=>false,
				'label' => 'Causes', 'attr' => array('label_help' => $label_help), 'cascade_validation'=>true,
				'entry_options'  => array('attr' => array('carto' => $carto))
			));
			$event->getForm()->add('menace', null, array(
				'label' => 'Nom du risque', 'empty_value' => 'Choisir un risque ...',
				/*'query_builder' => function($er) use($event) {
					return $er->createQueryBuilder('m')->innerJoin('m.cartographie', 'c')
						->where('c.id = :cartographieId')->setParameter('cartographieId', $event->getData()->getCartographie()->getId());
				},*/
				'attr' => array(
						'label_help' => 'Nom pour identifier le risque', 'class' => 'chzn-select',
						'widget_help' => 'Choisir le nom du risque dans la liste ci-dessus','extended_entity' => true
				)
			));
		});
	}
	
	public function setDefaultOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\Risque',
				'validation_groups' => function(FormInterface $form) {
					$groups = array('Default');
					if($form->getData()->getId() && $form->getData()->hasToBeValidated()) {
						$groups [] = 'RisqueValidation';
					}
					if(!$form->getData()->getMenace() &&($form->getData()->getIdentification() && !$form->getData()->getIdentification()->getLibelle())) {
						$groups [] = 'RisqueIdentification';
					}
					return $groups;
				}
		));
	}
	
	public function getName() {
		return 'risque';
	}
}
