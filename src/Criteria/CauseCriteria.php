<?php
namespace App\Criteria;

use App\Entity\Menace;
use App\Entity\Processus;
use App\Entity\Structure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CauseCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('processus', 'entity', array('class'=>Processus::class, 'empty_value'=>'Choisir un processus ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('structure', 'entity', array('class'=>Structure::class, 'property'=>'name', 'empty_value'=>'Choisir une structure ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('menace', 'entity', array('class'=>Menace::class, 'empty_value'=>'Chosir un risque ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('famille', null, array('empty_value'=>'Chosir une famille ...'));
    }
	
	public function setDefaultOptions(OptionsResolver $resolver)
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
