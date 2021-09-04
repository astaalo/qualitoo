<?php
namespace App\Criteria;

use App\Entity\Menace;
use App\Entity\Processus;
use App\Entity\Structure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CauseCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('processus', EntityType::class, array('class'=>Processus::class, 'placeholder'=>'Choisir un processus ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('structure', EntityType::class, array('class'=>Structure::class, 'placeholder'=>'Choisir une structure ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('menace', EntityType::class, array('class'=>Menace::class, 'placeholder'=>'Chosir un risque ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('famille', null, array('empty_data'=>'Chosir une famille ...'));
    }
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\Cause',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'cause_criteria';
    }
}
