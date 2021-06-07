<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RisqueEnvironnementalCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('site', 'entity', array(
    			'label' => 'Site',
    			'class' => '\App\Entity\Site',
    			'empty_value' => 'Choisir un site ...',
    			'query_builder' =>function($er){
    			return $er->filter();
    			},
    			'attr' => array(
    					'class' => 'chzn-select',
    					'placeholder' => 'Choisir un site ...',
    					'widget_help' => 'Choisir un site dans la liste',
    			)
    			))
        	->add('equipement', null, array('empty_value'=>'Chosir un équipement ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('domaineActivite', null, array('empty_value'=>'Chosir une activité ...', 'attr'=>array('class'=>'chzn-select')));
    }
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\RisqueEnvironnemental',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'risqueenvironnemental_criteria';
    }
}
