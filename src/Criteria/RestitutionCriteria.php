<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class RestitutionCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('menace', null, array('empty_value' => 'Choisir un risque ...', 'attr' => array('class' => 'chzn-select')))
        	->add('cartographie', 'entity', array(
        	      'class' => '\App\Entity\Cartographie', 'data_class' => '\App\Entity\Cartographie', 'expanded' => true
        	))
        	->add('probabilite', 'choice', array('choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        	->add('gravite', 'choice', array('choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        	->add('criticite', null, array('expanded' => true, 'multiple' => true))
        	->add('risqueMetier', new RisqueMetierCriteria())
        	->add('risqueProjet', new RisqueProjetCriteria())
        	->add('risqueSST', new RisqueSSTCriteria())
        	->add('risqueEnvironnemental', new RisqueEnvironnementalCriteria())
        	->add($builder->create('dateEvaluation', 'form')
        			->add('dateDebut', 'datetime', array('label' => 'Date de début', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        			->add('dateFin', 'datetime', array('label' => 'Date de fin', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	)->add('cause', 'entity', array('label' => 'Cause, danger ou aspect',
        			'class' => '\App\Entity\Cause', 'empty_value'=>'Chosir ...', 'attr'=>array('class'=>'chzn-select')
        	));
    }
	
	public function finishView(FormView $view, FormInterface $form, array $options) {
		foreach($view->children['cartographie'] as $child) {
			$child->vars['attr']['class'] = 'ck';
		}
		foreach($view->children['criticite'] as $child) {
			$child->vars['attr'] = array('class' => 'ck', 'style' => 'margin-right: 40px');
		}
		foreach($view->children['probabilite'] as $child) {
			$child->vars['attr'] = array('class' => 'ck', 'style' => 'margin-right: 40px');
		}
		foreach($view->children['gravite'] as $child) {
			$child->vars['attr'] = array('class' => 'ck', 'style' => 'margin-right: 40px');
		}
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\Risque',
				'csrf_protection' => false
			));
	}
	
    public function getName()
    {
        return 'restitution_criteria';
    }
}
