<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use App\Form\DataTransformer\EntityToIdTransformer;

class CritereType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle', null, array('label' => 'Libellé'))
			->add($builder->create('cartographie', 'hidden')->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\Cartographie')))
            ->add('domaine')
            ->add('grilleImpact', 'collection', array(
            		'type' => new GrilleImpactType(), 'allow_add' => true, 'allow_delete' => true, 'by_reference' => false, 'cascade_validation' => true,
            		'options' => array('attr' => $options['attr'])
            	));
		$builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSetData'));
		$builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'onSetData'));
    }
	
	/**
	 * @param FormEvent $event
	 */
	public function onSetData(FormEvent $event) {
		if(null != $cartographie = $event->getData()->getCartographie()) {
			$domaine = $event->getData()->getDomaine();
			$event->getForm()->add('domaine', null, array('empty_value' => 'Choisir un domaine ...', 'query_builder' => function($er) use($cartographie) {
            		return $er->createQueryBuilder('d')->where('d.cartographie = :cartographie')->setParameter('cartographie', $cartographie);
            	}, 'attr' => array('class' => 'chzn-select')));
			if($event->getName()==FormEvents::SUBMIT) {
				$event->getForm()->get('domaine')->submit($domaine ? $domaine->getId() : null);
			}
		}
	}
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            	'data_class' => 'App\Entity\Critere', 'cascade_validation' => true
        	));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'critere';
    }
}
