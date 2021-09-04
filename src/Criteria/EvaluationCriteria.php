<?php
namespace App\Criteria;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('processus', EntityType::class, array('class'=>'App\Entity\Processus', 'placeholder'=>'Choisir un processus ...', 'attr'=>array('class'=>'chzn-select')));
        	//->add('cartographie', EntityType::class, array('class'=>'App\Entity\Cartographie', 'placeholder'=>'Chosir une cartographie ...'));
    }
	
	public function configureOptions(OptionsResolver $resolver)
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
