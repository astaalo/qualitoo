<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\EntityToIdTransformer;

class EvaluationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		// cela suppose que le gestionnaire d'entité a été passé en option
		$builder->add($builder->create('risque', HiddenType::class)->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\Risque')))
			->add('causeOfEvaluation', CollectionType::class, array('entry_type' => EvaluationHasCauseType::class, 'allow_add' => true, 'by_reference' => false))
			->add('impactOfEvaluation', CollectionType::class, array('entry_type' => EvaluationHasImpactType::class, 'allow_add' => true, 'by_reference' => false,
					'entry_options' => array('attr' => array('em' => $options['attr']['em']))
			));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Evaluation', 'cascade_validation' => true,
			'validation_groups' =>  array('evaluation')
		));
	}
	
	public function getName()
	{
		return 'evaluation';
	}
}
