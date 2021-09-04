<?php
namespace App\Criteria;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RisqueEnvironnementalCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('site', EntityType::class, array(
    			'label' => 'Site',
    			'class' => 'App\Entity\Site',
    			'placeholder' => 'Choisir un site ...',
    			'query_builder' =>function($er){
    			return $er->filter();
    			},
    			'attr' => array(
    					'class' => 'chzn-select',
    					'placeholder' => 'Choisir un site ...',
    					'widget_help' => 'Choisir un site dans la liste',
    			)
    			))
        	->add('equipement', null, array('attr'=>array('placeholder'=>'Chosir un équipement ...', 'class'=>'chzn-select')))
        	->add('domaineActivite', null, array('attr'=>array('empty_value'=>'Chosir une activité ...', 'class'=>'chzn-select')));
    }
	
	public function configureOptions(OptionsResolver $resolver)
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
