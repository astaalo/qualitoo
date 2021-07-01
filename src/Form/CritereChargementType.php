<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\DomaineImpact;
use App\Repository\CritereRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CritereChargementType extends AbstractType {
	
	/**
	 *
	 * @param FormBuilderInterface $builder        	
	 * @param array $options        	
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add ( 'domaine', 'entity', array (
				'label' => 'vt',
				'required' => true,
				'class' => 'OrangeMainBundle:DomaineImpact',
				'attr' => array (
						'class' => 'chzn-select' 
				)) );
		$builder->addEventListener ( FormEvents::POST_SET_DATA, array ($this,'onSetData' ) );
	}
	
	/**
	 *
	 * @param FormEvent $event        	
	 */
	public function onSetData(FormEvent $event) {
		if (null != $domaine = $event->getData ()->getDomaine ()) {
			$domaine = $event->getData ()->getDomaine ();
			$event->getForm ()->add ( 'critere', 'entity', array (
					'class'=>'OrangeMainBundle:Critere',
					'empty_value' => 'Choisir un critère ...',
					'query_builder' => function ($er) use ($domaine) {
						return $er->createQueryBuilder ( 'q' )
						          ->innerJoin('q.domaine','d')
						          ->where ( 'd.root = :root' )->setParameter ( 'root', $domaine->getId() );
					},
					'attr' => array (
							'class' => 'chzn-select' 
					) 
			) );
		}
	}
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\CritereChargement',
				'cascade_validation'=>true,
		));
	}
	
	/**
	 *
	 * @return string
	 */
	public function getName() {
		return 'critere_chargement';
	}
}
