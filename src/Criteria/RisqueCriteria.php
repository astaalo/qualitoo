<?php
namespace App\Criteria;

use App\Entity\Criticite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use App\Entity\Risque;

class RisqueCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('motCle', TextType::class, array('label' => 'Mot-clé'))
        	->add('menace', null, array('attr'=>array('class'=>'chzn-select', 'placeholder'=>'Chosir un risque ...')))
            //->add('cartographie', null, array('expanded'=>true, 'required' => true))
            ->add('cartographie', null, array('required' => true))
            ->add('probabilite', ChoiceType::class, array('choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        	->add('gravite', ChoiceType::class, array('choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        	->add('criticite', ChoiceType::class, array('expanded' => true, 'multiple' => true))
        	->add('dateSaisie',  RepeatedType::class, array('type' => DateType::class))
        	->add('risqueMetier', RisqueMetierCriteria::class)
        	->add('risqueProjet', RisqueProjetCriteria::class)
        	->add('risqueSST', RisqueSSTCriteria::class)
        	->add('risqueEnvironnemental', RisqueEnvironnementalCriteria::class)
        	->add($builder->create('dateEvaluation', FormType::class)
        			->add('dateDebut', DateTimeType::class, array('label' => 'Date de début', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        			->add('dateFin', DateTimeType::class, array('label' => 'Date de fin', 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	)->add('cause', EntityType::class, array('label' => 'Cause, danger ou aspect',
        			'class' => 'App\Entity\Cause', 'placeholder'=>'Choisir ...', 'attr'=>array('class'=>'chzn-select'),
        			'query_builder' => function($er) {
				         return $er->createQueryBuilder('m')->orderBy('m.libelle');
				}
			))
			->add('hasPlanAction', ChoiceType::class, array('label' => 'Risque :','expanded' => true, 'choices' => array(null=>'Avec ou sans plans d\'action',true=>'Avec plans d\'actions ', false=>'Sans plans d\'actions')))
        	->add('hasControle', ChoiceType::class, array('label' => 'Risque :','expanded' => true,'choices' => array(null=>'Avec ou sans controles',true=>'Avec controles ', false=>'Sans controles')))
        	->add('statutPlanAction',  EntityType::class, array('class' => 'App\Entity\Statut', 'label' => "Statut des plans d'action", 'placeholder'=>'Choisir un statut ...',  'attr' => array('class' => 'chzn-select')));
       //filtres sur les kpis
       $builder ->add('probaForKpi', ChoiceType::class, array('label'=>'Probabilité' ,'choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        		->add('graviteForKpi', ChoiceType::class, array('label'=>'Gravité' ,'choices' => array(1=>1, 2=>2, 3=>3, 4=>4), 'expanded' => true, 'multiple' => true))
        		->add('criticiteForKpi', EntityType::class, array('label'=>'Criticité' ,'expanded' => true, 'multiple' => true,'class' => Criticite::class))
        		->add('maturiteForKpi', EntityType::class, array('label'=>'Maturité' ,'expanded' => true, 'multiple' => true,'class' => 'App\Entity\Maturite'))
        		->add('occurencesForKpi')
        		->add('maturiteReels', EntityType::class, array('label'=>'Maturité aprés:' ,'expanded' => true, 'multiple' => true,'class' => 'App\Entity\Maturite'))
        		->add('maturiteTheoriques', EntityType::class, array('label'=>'Maturité avant' ,'expanded' => true, 'multiple' => true, 'class' => 'App\Entity\Maturite'))
        		->add('anneeEvaluationDebut',  IntegerType::class,array('label'=>'De l\'an :' ))
        		->add('anneeEvaluationFin',IntegerType::class,array('label'=>'A l\'an :' ));
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
