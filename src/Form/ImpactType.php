<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use App\Form\DataTransformer\EntityToIdTransformer;

class ImpactType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('critere', null, array('empty_value' => 'Choisir un critère ...'));
		$builder->add($builder->create('domaine', 'hidden')->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\DomaineImpact')));
		$builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSetData'));
		$builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'onSetData'));
	}
	
	/**
	 * @param FormEvent $event
	 */
	public function onSetData(FormEvent $event) {
		if($event->getData() && null != $domaine = $event->getData()->getDomaine()) {
			$critere = $event->getData()->getCritere();
			$event->getForm()->add('critere', null, array('query_builder' => function($er) use($domaine) {
						return $er->createQueryBuilder('r')
								->where('r.domaine = :domaine')->andWhere('r.etat = :etat')
								->setParameters(array('domaine' => $domaine, 'etat' => true));
					}, 'empty_value' => 'Choisir un critère ...', 'attr' => array('class' => 'chzn-select select_child')
				));
			if($event->getName()==FormEvents::SUBMIT) {
				$event->getForm()->get('critere')->submit($critere ? $critere->getId() : null);
			}
		}
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Impact'
		));
	}
	
	public function getName()
	{
		return 'impact';
	}
}
