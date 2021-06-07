<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use App\Entity\Risque;

class RisqueCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('motCle', 'text', array('label' => 'Mot-clé'))
        	->add('menace', null, array('empty_value'=>'Chosir un risque ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('cartographie', null, array('expanded'=>true, 'required' => true))
        	->add('probabilite', 'choice', array('choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        	->add('gravite', 'choice', array('choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        	->add('criticite', null, array('expanded' => true, 'multiple' => true))
        	->add('dateSaisie',  'repeated', array('type' => 'date'))
        	->add('risqueMetier', new RisqueMetierCriteria())
        	->add('risqueProjet', new RisqueProjetCriteria())
        	->add('risqueSST', new RisqueSSTCriteria())
        	->add('risqueEnvironnemental', new RisqueEnvironnementalCriteria())
        	->add($builder->create('dateEvaluation', 'form')
        			->add('dateDebut', 'datetime', array('label' => 'Date de début', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        			->add('dateFin', 'datetime', array('label' => 'Date de fin', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	)->add('cause', 'entity', array('label' => 'Cause, danger ou aspect',
        			'class' => '\App\Entity\Cause', 'empty_value'=>'Choisir ...', 'attr'=>array('class'=>'chzn-select'),
        			'query_builder' => function($er) {
				         return $er->createQueryBuilder('m')->orderBy('m.libelle');
				}
			))
			->add('hasPlanAction', 'choice', array('label' => 'Risque :','expanded' => true, 'choices' => array(null=>'Avec ou sans plans d\'action',true=>'Avec plans d\'actions ', false=>'Sans plans d\'actions')))
        	->add('hasControle', 'choice', array('label' => 'Risque :','expanded' => true,'choices' => array(null=>'Avec ou sans controles',true=>'Avec controles ', false=>'Sans controles')))
        	->add('statutPlanAction',  'entity', array('class' => '\App\Entity\Statut', 'label' => "Statut des plans d'action", 'empty_value'=>'Choisir un statut ...',  'attr' => array('class' => 'chzn-select')));
       //filtres sur les kpis
       $builder ->add('probaForKpi', 'choice', array('label'=>'Probabilité' ,'choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        		->add('graviteForKpi', 'choice', array('label'=>'Gravité' ,'choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        		->add('criticiteForKpi', 'entity', array('label'=>'Criticité' ,'expanded' => true, 'multiple' => true,'class' => '\App\Entity\Criticite'))
        		->add('maturiteForKpi', 'entity', array('label'=>'Maturité' ,'expanded' => true, 'multiple' => true,'class' => '\App\Entity\Maturite'))
        		->add('occurencesForKpi')
        		->add('maturiteReels', 'entity', array('label'=>'Maturité aprés:' ,'expanded' => true, 'multiple' => true,'class' => '\App\Entity\Maturite'))
        		->add('maturiteTheoriques', 'entity', array('label'=>'Maturité avant' ,'expanded' => true, 'multiple' => true, 'class' => '\App\Entity\Maturite'))
        		->add('anneeEvaluationDebut',  'integer',array('label'=>'De l\'an :' ))
        		->add('anneeEvaluationFin','integer',array('label'=>'A l\'an :' ));
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
        return 'risque_criteria';
    }
}
