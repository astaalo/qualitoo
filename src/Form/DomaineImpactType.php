<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class DomaineImpactType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('libelle', null, array('label' => 'Libellé'))
			->add('parent')
			->add('cartographie', null, array('attr' => array('class' => 'chzn-select')));
		$builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSetData'));
		$builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'onSetData'));
	}
	
	/**
	 * @param FormEvent $event
	 */
	public function onSetData(FormEvent $event) {
		if(null != $cartographie = $event->getData()->getCartographie()) {
			$parent = $event->getData()->getParent();
			$event->getForm()->add('parent', null, array('label' => 'Domaine parent', 'property_path' => 'name', 'query_builder' => function($er) use($cartographie) {
            		return $er->createQueryBuilder('d')->where('d.cartographie = :cartographie')->setParameter('cartographie', $cartographie);
            	}, 'attr' => array('class' => 'chzn-select')));
			if($event->getForm()==FormEvents::SUBMIT) {
				$event->getForm()->get('parent')->submit($parent ? $parent->getId() : null);
			}
		}
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\DomaineImpact'
		));
	}

	public function getName()
	{
		return 'domaine_impact';
	}
}
