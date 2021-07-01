<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CauseType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('libelle', null, array('label' => 'Libellé de la cause'))
			->add('famille', null, array('empty_value' => 'Choisir une famille ...', 'attr' => array('class' => 'no-chzn')))
			->add('description', null, array('attr' => array('style' => 'min-height: 21px;')));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Cause'
		));
	}
	
	public function getName()
	{
		return 'cause';
	}
}
