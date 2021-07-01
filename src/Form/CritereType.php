<?php

namespace App\Form;

use App\Entity\Cartographie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
			->add($builder->create('cartographie', HiddenType::class)->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], Cartographie::class)))
            ->add('domaine')
            ->add('grilleImpact', CollectionType::class, array(
                'entry_type' => GrilleImpactType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                //'cascade_validation' => true,
                'entry_options' => array('attr' => $options['attr'])
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
			$event->getForm()->add('domaine', null, array('placeholder' => 'Choisir un domaine ...', 'query_builder' => function($er) use($cartographie) {
            		return $er->createQueryBuilder('d')->where('d.cartographie = :cartographie')->setParameter('cartographie', $cartographie);
            	}, 'attr' => array('class' => 'chzn-select')));
			if($event->getForm()==FormEvents::SUBMIT) {
				$event->getForm()->get('domaine')->submit($domaine ? $domaine->getId() : null);
			}
		}
	}
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
