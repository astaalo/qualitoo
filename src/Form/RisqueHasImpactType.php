<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\EntityToIdTransformer;

class RisqueHasImpactType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add($builder->create('domaine', HiddenType::class)->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\DomaineImpact')))
			->add('impact', new ImpactType(), array('attr' => array('em' => $options['attr']['em'])))
			->add('grille', null, array('placeholder' => 'Choisir un niveau ...'));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\RisqueHasImpact'
		));
	}
	
	public function getName()
	{
		return 'risque_has_impact';
	}
}
