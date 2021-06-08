<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class EvaluationHasImpactType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add($builder->create('domaine', 'hidden')->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\DomaineImpact')))
			->add('impact', new ImpactType(), array('attr' => array('em' => $options['attr']['em'])))
			->add('grille', null, array('empty_value' => 'Choisir un niveau ...'));
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\EvaluationHasImpact'
		));
	}
	
	public function finishView(FormView $view, FormInterface $form, array $options) {
		foreach($view->children['grille']->vars['choices'] as $child) {
			$child->vars['attr']['niveau'] = 'ck';
		}
	}
	
	public function getName()
	{
		return 'evaluation_has_impact';
	}
}
