<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class PlanActionCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('menace', 'entity', array('class'=>'\App\Entity\Menace', 'empty_value'=>'Choisir un risque ...', 'attr'=>array('class'=>'chzn-select')))
	        ->add('site', 'entity', array('class' => '\App\Entity\Site', 'empty_value'=>'Choisir un site ...', 'attr'=>array('class'=>'chzn-select')))
	        ->add('projet', 'entity', array('class' => '\App\Entity\Projet', 'empty_value'=>'Choisir un projet ...', 'attr'=>array('class'=>'chzn-select')))
	        ->add('cartographie', 'entity', array('class' => '\App\Entity\Cartographie', 'data_class' => '\App\Entity\Cartographie', 'expanded' => true))
	        ->add('structure',  'entity', array('class' => '\App\Entity\Structure', 'empty_value'=>'Choisir une structure ...', 'attr' => array('class' => 'chzn-select')))
        	->add('porteur',  null, array('label' => 'Porteur ', 'empty_value'=>'Choisir un porteur ...',  'attr' => array('class' => 'chzn-select')))
        	->add('statut',  null, array('label' => 'Statut ', 'empty_value'=>'Choisir un statut ...',  'attr' => array('class' => 'chzn-select')))
        	->add('dateDebutFrom', 'datetime', array('label'=>'De ', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('dateDebutTo', 'datetime', array('label'=>'Et', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('dateFinFrom', 'datetime', array('label'=>'De', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('dateFinTo', 'datetime', array('label'=>'Et', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'));
    }
    
    public function finishView(FormView $view, FormInterface $form, array $options) {
    	foreach($view->children['cartographie'] as $child) {
    		$child->vars['attr']['class'] = 'ck';
    	}
    }
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\PlanAction',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'planaction_criteria';
    }
}
