<?php
namespace App\Criteria;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RisqueProjetCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('structure', EntityType::class, array(
				'class' => 'App\Entity\Structure', 'label' => 'Structure', 'query_builder'=>function($sr){
					          return $sr->filter();
					 }, 'attr' => array(
					        'empty_value' => 'Choisir une structure ...',
                            'class' => 'chzn-select', 'label_help' => 'Structure à laquelle l\'activité est rattachée',
						    'widget_help' => 'Cliquer puis rechercher et choisir une structure dans la liste'
					)
			));
		$builder->add('direction', EntityType::class, array(
				'label' => 'Direction', 'class' => 'App\Entity\Structure', 'query_builder' => function($er) {
							return $er->listAllDirectionBySociete();
						}, 'attr' => array(
                                'empty_value' => 'Choisir la direction ...',
							    'class' => 'chzn-select', 'label_help' => 'Direction', 'widget_help' => 'Choisir une direction dans la liste'
					)
			))
        	->add('processus', null, array('attr'=>array('placeholder'=>'Chosir un processus ...', 'class'=>'chzn-select')))
        	->add('projet', null, array('attr'=>array('empty_value'=>'Chosir un projet ...', 'class'=>'chzn-select')));
    }
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\RisqueProjet',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'risqueprojet_criteria';
    }
}
