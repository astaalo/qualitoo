<?php
namespace App\Criteria;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class PlanActionCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('menace', EntityType::class, array('class'=>'App\Entity\Menace', 'placeholder'=>'Choisir un risque ...', 'attr'=>array('class'=>'chzn-select')))
	        ->add('site', EntityType::class, array('class' => 'App\Entity\Site', 'placeholder'=>'Choisir un site ...', 'attr'=>array('class'=>'chzn-select')))
	        ->add('projet', EntityType::class, array('class' => 'App\Entity\Projet', 'placeholder'=>'Choisir un projet ...', 'attr'=>array('class'=>'chzn-select')))
	        ->add('cartographie', EntityType::class, array('class' => 'App\Entity\Cartographie', 'data_class' => 'App\Entity\Cartographie', 'expanded' => true))
	        ->add('structure',  EntityType::class, array('class' => 'App\Entity\Structure', 'placeholder'=>'Choisir une structure ...', 'attr' => array('class' => 'chzn-select')))
        	->add('porteur',  null, array('label' => 'Porteur ', 'placeholder'=>'Choisir un porteur ...',  'attr' => array('class' => 'chzn-select')))
        	->add('statut',  null, array('label' => 'Statut ', 'placeholder'=>'Choisir un statut ...',  'attr' => array('class' => 'chzn-select')))
        	->add('dateDebutFrom', DateTimeType::class, array('label'=>'De ', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('dateDebutTo', DateTimeType::class, array('label'=>'Et', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('dateFinFrom', DateTimeType::class, array('label'=>'De', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('dateFinTo', DateTimeType::class, array('label'=>'Et', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'));
    }
    
    public function finishView(FormView $view, FormInterface $form, array $options) {
    	foreach($view->children['cartographie'] as $child) {
    		$child->vars['attr']['class'] = 'ck';
    	}
    }
	
	public function configureOptions(OptionsResolver $resolver)
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
