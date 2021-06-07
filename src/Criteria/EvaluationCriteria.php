<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('processus', 'entity', array('class'=>'\App\Entity\Processus', 'empty_value'=>'Choisir un processus ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('cartographie', 'entity', array('class'=>'\App\Entity\Cartographie', 'empty_value'=>'Chosir une cartographie ...'));
    }
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\Evaluation',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'evaluation_criteria';
    }
}
